<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Console;

use Doctrine\DBAL\Connection;
use PSX\Framework\Config\DirectoryInterface;
use PSX\Sql\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TableCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org
 */
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

    protected function configure()
    {
        $this
            ->setName('generate:table')
            ->setDescription('Generates table and row classes for the configured connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
