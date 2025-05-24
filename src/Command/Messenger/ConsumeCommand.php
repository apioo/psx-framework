<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
use Symfony\Component\Messenger\EventListener\StopWorkerOnMemoryLimitListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnTimeLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;

/**
 * Based on the Symfony package. For the full copyright and license information,
 * please take a look at https://github.com/symfony/symfony
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Samuel Roze <samuel.roze@gmail.com>
 * @author Christoph Kappestein <christoph.kappestein@gmail.com>
 */
#[AsCommand(name: 'messenger:consume', description: 'Consume messages')]
class ConsumeCommand extends Command
{
    private ?Worker $worker = null;

    public function __construct(
        private TransportInterface $transport,
        private MessageBusInterface $messageBus,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of received messages'),
                new InputOption('failure-limit', 'f', InputOption::VALUE_REQUIRED, 'The number of failed messages the worker can consume'),
                new InputOption('memory-limit', 'm', InputOption::VALUE_REQUIRED, 'The memory limit the worker can consume'),
                new InputOption('time-limit', 't', InputOption::VALUE_REQUIRED, 'The time limit in seconds the worker can handle new messages'),
                new InputOption('sleep', null, InputOption::VALUE_REQUIRED, 'Seconds to sleep before asking for new messages after no messages were found', 1),
            ])
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command consumes messages and dispatches them to the message bus.

    <info>php %command.full_name% <receiver-name></info>

To receive from multiple transports, pass each name:

    <info>php %command.full_name% receiver1 receiver2</info>

Use the --limit option to limit the number of messages received:

    <info>php %command.full_name% <receiver-name> --limit=10</info>

Use the --failure-limit option to stop the worker when the given number of failed messages is reached:

    <info>php %command.full_name% <receiver-name> --failure-limit=2</info>

Use the --memory-limit option to stop the worker if it exceeds a given memory usage limit. You can use shorthand byte values [K, M or G]:

    <info>php %command.full_name% <receiver-name> --memory-limit=128M</info>

Use the --time-limit option to stop the worker when the given time limit (in seconds) is reached.
If a message is being handled, the worker will stop after the processing is finished:

    <info>php %command.full_name% <receiver-name> --time-limit=3600</info>

Use the --bus option to specify the message bus to dispatch received messages
to instead of trying to determine it automatically. This is required if the
messages didn't originate from Messenger:

    <info>php %command.full_name% <receiver-name> --bus=event_bus</info>

Use the --queues option to limit a receiver to only certain queues (only supported by some receivers):

    <info>php %command.full_name% <receiver-name> --queues=fasttrack</info>

Use the --no-reset option to prevent services resetting after each message (may lead to leaking services' state between messages):

    <info>php %command.full_name% <receiver-name> --no-reset</info>

Use the --all option to consume from all receivers:

    <info>php %command.full_name% --all</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopsWhen = [];
        if (null !== $limit = $input->getOption('limit')) {
            if (!is_numeric($limit) || 0 >= $limit) {
                throw new InvalidOptionException(\sprintf('Option "limit" must be a positive integer, "%s" passed.', $limit));
            }

            $stopsWhen[] = "processed {$limit} messages";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener($limit, $this->logger));
        }

        if ($failureLimit = $input->getOption('failure-limit')) {
            $stopsWhen[] = "reached {$failureLimit} failed messages";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnFailureLimitListener($failureLimit, $this->logger));
        }

        if ($memoryLimit = $input->getOption('memory-limit')) {
            $stopsWhen[] = "exceeded {$memoryLimit} of memory";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnMemoryLimitListener($this->convertToBytes($memoryLimit), $this->logger));
        }

        if (null !== $timeLimit = $input->getOption('time-limit')) {
            if (!is_numeric($timeLimit) || 0 >= $timeLimit) {
                throw new InvalidOptionException(\sprintf('Option "time-limit" must be a positive integer, "%s" passed.', $timeLimit));
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

    private function convertToBytes(string $memoryLimit): int
    {
        $memoryLimit = strtolower($memoryLimit);
        $max = ltrim($memoryLimit, '+');
        if (str_starts_with($max, '0x')) {
            $max = \intval($max, 16);
        } elseif (str_starts_with($max, '0')) {
            $max = \intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr(rtrim($memoryLimit, 'b'), -1)) {
            case 't': $max *= 1024;
                // no break
            case 'g': $max *= 1024;
                // no break
            case 'm': $max *= 1024;
                // no break
            case 'k': $max *= 1024;
        }

        return $max;
    }
}
