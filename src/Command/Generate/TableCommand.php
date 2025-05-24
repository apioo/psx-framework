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

namespace PSX\Framework\Command\Generate;

use Doctrine\DBAL\Connection;
use PSX\Framework\Config\DirectoryInterface;
use PSX\Sql\Generator\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TableCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[AsCommand(name: 'generate:table', description: 'Generates table and row classes for the configured connection')]
class TableCommand extends Command
{
    private DirectoryInterface $directory;
    private Connection $connection;

    public function __construct(DirectoryInterface $directory, Connection $connection)
    {
        parent::__construct();

        $this->directory = $directory;
        $this->connection = $connection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $srcDir = $this->directory->getSrcDir();
        $tableFolder = $srcDir . '/Table';
        if (!is_dir($tableFolder)) {
            throw new \RuntimeException('The folder src/Table does not exist, please create it in order to generate the table classes');
        }

        $tableGeneratedFolder = $tableFolder . '/Generated';
        if (!is_dir($tableGeneratedFolder)) {
            throw new \RuntimeException('The folder src/Table/Generated does not exist, please create it in order to generate the table classes');
        }

        $generator = new Generator($this->connection, 'App\\Table\\Generated', 'app_');
        $count = 0;
        foreach ($generator->generate() as $className => $source) {
            file_put_contents($tableGeneratedFolder . '/' . $className . '.php', '<?php' . "\n\n" . $source);

            if (str_ends_with($className, 'Table')) {
                $overwriteClass = substr($className, 0, -5);
                $overwriteFile = $tableFolder . '/' . $overwriteClass . '.php';
                if (!is_file($overwriteFile)) {
                    file_put_contents($overwriteFile, $this->getOverwriteClass($className, $overwriteClass));
                }
            }

            $count++;
        }

        $output->writeln('Generated ' . $count . ' files at ' . $tableGeneratedFolder);

        return 0;
    }

    private function getOverwriteClass(string $className, string $overwriteClass): string
    {
        return <<<PHP
<?php

namespace App\Table;

use App\\Table\\Generated\\{$className};

class {$overwriteClass} extends {$className}
{
}

PHP;

    }
}
