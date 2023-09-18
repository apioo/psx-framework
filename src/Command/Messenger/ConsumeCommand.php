<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Framework\Command\Messenger;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use PSX\Framework\Messenger\DefaultTransport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\EventListener\StopWorkerOnFailureLimitListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnTimeLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;

/**
 * ConsumeCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org
 */
#[AsCommand(name: 'messenger:consume', description: 'Consume messages')]
class ConsumeCommand extends Command
{
    private TransportInterface $transport;
    private MessageBusInterface $messageBus;
    private EventDispatcherInterface $eventDispatcher;
    private LoggerInterface $logger;

    public function __construct(TransportInterface $transport, MessageBusInterface $messageBus, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        parent::__construct();

        $this->transport = $transport;
        $this->messageBus = $messageBus;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of received messages')
            ->addOption('failure-limit', 'f', InputOption::VALUE_REQUIRED, 'The number of failed messages the worker can consume')
            ->addOption('time-limit', 't', InputOption::VALUE_REQUIRED, 'The time limit in seconds the worker can handle new messages')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Seconds to sleep before asking for new messages after no messages were found', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopsWhen = [];
        if (null !== $limit = $input->getOption('limit')) {
            if (!is_numeric($limit) || 0 >= $limit) {
                throw new InvalidOptionException(sprintf('Option "limit" must be a positive integer, "%s" passed.', $limit));
            }

            $stopsWhen[] = "processed {$limit} messages";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener($limit, $this->logger));
        }

        if ($failureLimit = $input->getOption('failure-limit')) {
            $stopsWhen[] = "reached {$failureLimit} failed messages";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnFailureLimitListener($failureLimit, $this->logger));
        }

        if (null !== $timeLimit = $input->getOption('time-limit')) {
            if (!is_numeric($timeLimit) || 0 >= $timeLimit) {
                throw new InvalidOptionException(sprintf('Option "time-limit" must be a positive integer, "%s" passed.', $timeLimit));
            }

            $stopsWhen[] = "been running for {$timeLimit}s";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnTimeLimitListener($timeLimit, $this->logger));
        }

        $stopsWhen[] = 'received a stop signal via the messenger:stop-workers command';

        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);
        $io->success(sprintf('Consuming messages from transport "%s".', $this->transport::class));

        if ($stopsWhen) {
            $last = array_pop($stopsWhen);
            $stopsWhen = ($stopsWhen ? implode(', ', $stopsWhen).' or ' : '').$last;
            $io->comment("The worker will automatically exit once it has {$stopsWhen}.");
        }

        $io->comment('Quit the worker with CONTROL-C.');

        if (OutputInterface::VERBOSITY_VERBOSE > $output->getVerbosity()) {
            $io->comment('Re-run the command with a -vv option to see logs about consumed messages.');
        }

        $options = [
            'sleep' => $input->getOption('sleep') * 1000000,
        ];

        $worker = new Worker([DefaultTransport::NAME => $this->transport], $this->messageBus, $this->eventDispatcher, $this->logger);
        $worker->run($options);

        return 0;
    }
}
