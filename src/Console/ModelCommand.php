<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Console;

use PSX\Framework\Config\DirectoryInterface;
use PSX\Schema\Generator\Code\Chunks;
use PSX\Schema\Generator\FileAwareInterface;
use PSX\Schema\GeneratorFactory;
use PSX\Schema\SchemaManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ModelCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ModelCommand extends Command
{
    private DirectoryInterface $directory;
    private SchemaManagerInterface $schemaManager;

    public function __construct(DirectoryInterface $directory, SchemaManagerInterface $schemaManager)
    {
        parent::__construct();

        $this->directory = $directory;
        $this->schemaManager = $schemaManager;
    }

    protected function configure()
    {
        $this
            ->setName('generate:model')
            ->setDescription('Generates model classes based on a TypeSchema specification');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appDir = $this->directory->getAppDir();
        $srcDir = $this->directory->getSrcDir();

        $source = $appDir . '/resources/typeschema.json';
        $target = $srcDir . '/Model';
        $format = 'php';
        $config = 'namespace=App\Model';

        if (!is_file($source)) {
            throw new \RuntimeException('TypeSchema file does not exist at resources/typeschema.json, please create the file in order to generate the models, more information about TypeSchema at: typeschema.org');
        }

        if (!is_dir($target)) {
            throw new \RuntimeException('The folder src/Model does not exist, please create it in order to generate the models');
        }

        $count = $this->generate($source, $target, $format, $config);

        $output->writeln('Generated ' . $count . ' files at ' . $target);

        return 0;
    }

    private function generate(string $source, string $target, string $format, string $config): int
    {
        $schema = $this->schemaManager->getSchema($source);

        $generator = (new GeneratorFactory())->getGenerator($format, $config);
        $response  = $generator->generate($schema);

        if ($generator instanceof FileAwareInterface && $response instanceof Chunks) {
            $count = 0;
            foreach ($response->getChunks() as $file => $code) {
                file_put_contents($target . '/' . $generator->getFileName($file), $generator->getFileContent($code));
                $count++;
            }

            return $count;
        } else {
            throw new \RuntimeException('The configured generator cant produce files');
        }
    }
}
