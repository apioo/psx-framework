<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Command;

use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorRegistry;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Framework\Config\DirectoryInterface;
use PSX\Schema\Generator\Code\Chunks;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SdkCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[AsCommand(name: 'generate:sdk', description: 'Generates a client SDK')]
class SdkCommand extends Command
{
    private DirectoryInterface $directory;
    private ScannerInterface $scanner;
    private GeneratorFactory $factory;
    private ?FilterFactoryInterface $filterFactory;

    public function __construct(DirectoryInterface $directory, ScannerInterface $scanner, GeneratorFactory $factory, FilterFactoryInterface $filterFactory)
    {
        parent::__construct();

        $this->directory = $directory;
        $this->scanner = $scanner;
        $this->factory = $factory;
        $this->filterFactory = $filterFactory;
    }

    protected function configure()
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'The target format of the SDK')
            ->addOption('namespace', 's', InputOption::VALUE_REQUIRED, 'A namespace which is used', null)
            ->addOption('filter', 'e', InputOption::VALUE_REQUIRED, 'Optional a filter which is used', null)
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Optional the output dir, the default is output/', 'output')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Optional the generator config', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $this->getOutputDir($input);
        $registry = $this->factory->factory();

        $type = $input->getArgument('type');
        if (!is_string($type) || !in_array($type, $registry->getPossibleTypes())) {
            throw new \InvalidArgumentException('Provided an invalid type, possible values are: ' . implode(', ', $registry->getPossibleTypes()));
        }

        $config = $this->getConfig($input);
        $filter = null;
        $filterName = $input->getOption('filter');
        if (!empty($filterName) && is_string($filterName)) {
            $filter = $this->filterFactory?->getFilter($filterName);
            if ($filter === null) {
                throw new \RuntimeException('Provided an invalid filter name');
            }
        }

        $generator = $registry->getGenerator($type, $config);
        $extension = $registry->getFileExtension($type);

        $output->writeln('Generating ...');

        $content = $generator->generate($this->scanner->generate($filter));

        if ($content instanceof Chunks) {
            if (!empty($filterName)) {
                $file = 'sdk-' . $type .  '-' . $filterName . '.zip';
            } else {
                $file = 'sdk-' . $type .  '.zip';
            }

            $content->writeTo($dir . '/' . $file);
        } else {
            if (!empty($filterName)) {
                $file = 'output-' . $type . '-' . $filterName . '.' . $extension;
            } else {
                $file = 'output-' . $type . '.' . $extension;
            }

            file_put_contents($dir . '/' . $file, $content);
        }

        $output->writeln('Successful!');

        return 0;
    }

    private function getConfig(InputInterface $input): ?string
    {
        $config = $input->getOption('config');
        if (!empty($config)) {
            return $config;
        }

        $namespace = $input->getOption('namespace');
        $options = [];
        if (!empty($namespace)) {
            $options['namespace'] = $namespace;
        }

        if (!empty($options)) {
            return http_build_query($options);
        } else {
            return null;
        }
    }

    private function getOutputDir(InputInterface $input): string
    {
        $outputDir = $input->getOption('output');
        if (is_dir($outputDir)) {
            return $outputDir;
        }

        $appDir = $this->directory->getAppDir();
        if (is_dir($appDir . '/' . $outputDir)) {
            return $appDir . '/' . $outputDir;
        }

        throw new \RuntimeException('The folder ' . $outputDir . ' does not exist, please create it in order to generate the SDK');
    }
}
