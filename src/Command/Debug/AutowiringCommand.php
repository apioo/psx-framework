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

use PSX\Framework\Dependency\ContainerBuilder;
use Symfony\Bundle\FrameworkBundle\Console\Descriptor\Descriptor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Target;

/**
 * Based on the Symfony package. For the full copyright and license information,
 * please take a look at https://github.com/symfony/symfony
 *
 * A console command for autowiring information.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Ryan Weaver <ryan@knpuniversity.com>
 * @author Christoph Kappestein <christoph.kappestein@gmail.com>
 */
#[AsCommand(name: 'debug:autowiring', description: 'List classes/interfaces you can use for autowiring')]
class AutowiringCommand extends ContainerCommand
{
    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('search', InputArgument::OPTIONAL, 'A search filter'),
                new InputOption('all', null, InputOption::VALUE_NONE, 'Show also services that are not aliased'),
            ])
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays the classes and interfaces that
you can use as type-hints for autowiring:

  <info>php %command.full_name%</info>

You can also pass a search term to filter the list:

  <info>php %command.full_name% log</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $errorIo = $io->getErrorStyle();

        $container = ContainerBuilder::getContainerBuilder(
            $this->container->getParameter('psx_path_app'),
            $this->container->getParameter('psx_container_files')
        );

        $serviceIds = $container->getServiceIds();
        $serviceIds = array_filter($serviceIds, $this->filterToServiceTypes(...));

        if ($search = $input->getArgument('search')) {
            $searchNormalized = preg_replace('/[^a-zA-Z0-9\x7f-\xff $]++/', '', $search);

            $serviceIds = array_filter($serviceIds, fn ($serviceId) => false !== stripos(str_replace('\\', '', $serviceId), $searchNormalized) && !str_starts_with($serviceId, '.'));

            if (!$serviceIds) {
                $errorIo->error(\sprintf('No autowirable classes or interfaces found matching "%s"', $search));

                return 1;
            }
        }

        $reverseAliases = [];

        foreach ($container->getAliases() as $id => $alias) {
            if ('.' === ($id[0] ?? null)) {
                $reverseAliases[(string) $alias][] = $id;
            }
        }

        uasort($serviceIds, 'strnatcmp');

        $io->title('Autowirable Types');
        $io->text('The following classes & interfaces can be used as type-hints when autowiring:');
        if ($search) {
            $io->text(\sprintf('(only showing classes/interfaces matching <comment>%s</comment>)', $search));
        }
        $hasAlias = [];
        $all = $input->getOption('all');
        $previousId = '-';
        $serviceIdsNb = 0;
        foreach ($serviceIds as $serviceId) {
            if ($container->hasDefinition($serviceId) && $container->getDefinition($serviceId)->hasTag('container.excluded')) {
                continue;
            }
            $text = [];
            $resolvedServiceId = $serviceId;
            if (!str_starts_with($serviceId, $previousId.' $')) {
                $text[] = '';
                $previousId = preg_replace('/ \$.*/', '', $serviceId);
                if ('' !== $description = Descriptor::getClassDescription($previousId, $resolvedServiceId)) {
                    if (isset($hasAlias[$previousId])) {
                        continue;
                    }
                    $text[] = $description;
                }
            }

            $serviceLine = \sprintf('<fg=yellow>%s</>', $serviceId);

            if ($container->hasAlias($serviceId)) {
                $hasAlias[$serviceId] = true;
                $serviceAlias = $container->getAlias($serviceId);
                $alias = (string) $serviceAlias;

                $target = null;
                foreach ($reverseAliases[(string) $serviceAlias] ?? [] as $id) {
                    if (!str_starts_with($id, '.'.$previousId.' $')) {
                        continue;
                    }
                    $target = substr($id, \strlen($previousId) + 3);

                    if ($previousId.' $'.(new Target($target))->getParsedName() === $serviceId) {
                        $serviceLine .= ' - <fg=magenta>target:</><fg=cyan>'.$target.'</>';
                        break;
                    }
                }

                if ($container->hasDefinition($serviceAlias) && $decorated = $container->getDefinition($serviceAlias)->getTag('container.decorator')) {
                    $alias = $decorated[0]['id'];
                }

                if ($alias !== $target) {
                    $serviceLine .= ' - <fg=magenta>alias:</><fg=cyan>'.$alias.'</>';
                }

                if ($serviceAlias->isDeprecated()) {
                    $serviceLine .= ' - <fg=magenta>deprecated</>';
                }
            } elseif (!$all) {
                ++$serviceIdsNb;
                continue;
            } elseif ($container->getDefinition($serviceId)->isDeprecated()) {
                $serviceLine .= ' - <fg=magenta>deprecated</>';
            }
            $text[] = $serviceLine;
            $io->text($text);
        }

        $io->newLine();

        if (0 < $serviceIdsNb) {
            $io->text(\sprintf('%s more concrete service%s would be displayed when adding the "--all" option.', $serviceIdsNb, $serviceIdsNb > 1 ? 's' : ''));
        }
        if ($all) {
            $io->text('Pro-tip: use interfaces in your type-hints instead of classes to benefit from the dependency inversion principle.');
        }

        $io->newLine();

        return 0;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('search')) {
            $container = ContainerBuilder::getContainerBuilder(
                $this->container->getParameter('psx_path_app'),
                $this->container->getParameter('psx_container_files')
            );

            $suggestions->suggestValues(array_filter($container->getServiceIds(), $this->filterToServiceTypes(...)));
        }
    }
}