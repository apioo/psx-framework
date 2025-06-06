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

namespace PSX\Framework\Command\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * Based on the Symfony package. For the full copyright and license information,
 * please take a look at https://github.com/symfony/symfony
 *
 * A console command for retrieving information about event dispatcher.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Matthieu Auger <mail@matthieuauger.com>
 * @author Christoph Kappestein <christoph.kappestein@gmail.com>
 */
#[AsCommand(name: 'debug:event-dispatcher', description: 'Display configured listeners for an application')]
class EventDispatcherCommand extends Command
{
    private const DEFAULT_DISPATCHER = PsrEventDispatcherInterface::class;

    public function __construct(
        private ContainerInterface $dispatchers,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('event', InputArgument::OPTIONAL, 'An event name or a part of the event name'),
                new InputOption('dispatcher', null, InputOption::VALUE_REQUIRED, 'To view events of a specific event dispatcher', self::DEFAULT_DISPATCHER),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, \sprintf('The output format ("%s")', implode('", "', $this->getAvailableFormatOptions())), 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw description'),
            ])
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays all configured listeners:

  <info>php %command.full_name%</info>

To get specific listeners for an event, specify its name:

  <info>php %command.full_name% kernel.request</info>

The <info>--format</info> option specifies the format of the command output:

  <info>php %command.full_name% --format=json</info>
EOF
            )
        ;
    }

    /**
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $options = [];
        $dispatcherServiceName = $input->getOption('dispatcher');
        if (!$this->dispatchers->has($dispatcherServiceName)) {
            $io->getErrorStyle()->error(\sprintf('Event dispatcher "%s" is not available.', $dispatcherServiceName));

            return 1;
        }

        $dispatcher = $this->dispatchers->get($dispatcherServiceName);

        if ($event = $input->getArgument('event')) {
            if ($dispatcher->hasListeners($event)) {
                $options = ['event' => $event];
            } else {
                // if there is no direct match, try find partial matches
                $events = $this->searchForEvent($dispatcher, $event);
                if (0 === \count($events)) {
                    $io->getErrorStyle()->warning(\sprintf('The event "%s" does not have any registered listeners.', $event));

                    return 0;
                } elseif (1 === \count($events)) {
                    $options = ['event' => $events[array_key_first($events)]];
                } else {
                    $options = ['events' => $events];
                }
            }
        }

        $helper = new DescriptorHelper();

        if (self::DEFAULT_DISPATCHER !== $dispatcherServiceName) {
            $options['dispatcher_service_name'] = $dispatcherServiceName;
        }

        $options['format'] = $input->getOption('format');
        $options['raw_text'] = $input->getOption('raw');
        $options['output'] = $io;
        $helper->describe($io, $dispatcher, $options);

        return 0;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('event')) {
            $dispatcherServiceName = $input->getOption('dispatcher');
            if ($this->dispatchers->has($dispatcherServiceName)) {
                $dispatcher = $this->dispatchers->get($dispatcherServiceName);
                $suggestions->suggestValues(array_keys($dispatcher->getListeners()));
            }

            return;
        }

        if ($input->mustSuggestOptionValuesFor('dispatcher')) {
            if ($this->dispatchers instanceof ServiceProviderInterface) {
                $suggestions->suggestValues(array_keys($this->dispatchers->getProvidedServices()));
            }

            return;
        }

        if ($input->mustSuggestOptionValuesFor('format')) {
            $suggestions->suggestValues($this->getAvailableFormatOptions());
        }
    }

    private function searchForEvent(EventDispatcherInterface $dispatcher, string $needle): array
    {
        $output = [];
        $lcNeedle = strtolower($needle);
        $allEvents = array_keys($dispatcher->getListeners());
        foreach ($allEvents as $event) {
            if (str_contains(strtolower($event), $lcNeedle)) {
                $output[] = $event;
            }
        }

        return $output;
    }

    /** @return string[] */
    private function getAvailableFormatOptions(): array
    {
        return (new DescriptorHelper())->getFormats();
    }
}