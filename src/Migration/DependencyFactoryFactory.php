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

namespace PSX\Framework\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;

/**
 * DependencyFactoryFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class DependencyFactoryFactory
{
    private Connection $connection;
    private array $paths = [];

    public function __construct(Connection $connection, string $srcDir, string $namespace)
    {
        $this->connection = $connection;

        $this->addPath($namespace, $srcDir);
    }

    public function addPath(string $namespace, string $srcDir): void
    {
        $migrationDir = $srcDir . '/Migrations';
        if (!is_dir($migrationDir)) {
            return;
        }

        $this->paths[$namespace] = $migrationDir;
    }

    public function factory(): DependencyFactory
    {
        $config = new ConfigurationArray([
            'table_storage' => [
                'table_name' => 'doctrine_migration_versions',
                'version_column_name' => 'version',
                'version_column_length' => 191,
                'executed_at_column_name' => 'executed_at',
                'execution_time_column_name' => 'execution_time',
            ],
            'migrations_paths' => $this->paths,
            'all_or_nothing' => false,
            'transactional' => true,
            'check_database_platform' => true,
            'organize_migrations' => 'none',
            'connection' => null,
            'em' => null,
        ]);

        return DependencyFactory::fromConnection($config, new ExistingConnection($this->connection));
    }
}
