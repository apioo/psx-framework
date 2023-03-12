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

namespace PSX\Framework\Test;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use PSX\Framework\Bootstrap;
use PSX\Framework\Config\Config;

/**
 * Environment
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Environment
{
    private Config $config;
    private Connection $connection;
    private bool $hasConnection = false;

    public function __construct(Config $config, Connection $connection)
    {
        $this->config = $config;
        $this->connection = $connection;
    }

    /**
     * Setups the environment to run unit tests. Includes the DI container and optional creates a database schema
     */
    public function setup(Closure $schemaSetup = null): void
    {
        Bootstrap::setupEnvironment($this->config);

        $this->setupConnection($schemaSetup);
    }

    public function getBaseUrl(): string
    {
        return $this->config->get('psx_url') . '/' . $this->config->get('psx_dispatch');
    }

    public function hasConnection(): bool
    {
        return $this->hasConnection;
    }

    private function setupConnection(Closure $schemaSetup = null): void
    {
        $fromSchema = $this->connection->getSchemaManager()->createSchema();

        // we get the schema from the callback if available
        if ($schemaSetup !== null) {
            $toSchema = $schemaSetup($fromSchema, $this->connection);

            if ($toSchema instanceof Schema) {
                $queries = $fromSchema->getMigrateToSql($toSchema, $this->connection->getDatabasePlatform());

                foreach ($queries as $query) {
                    $this->connection->executeStatement($query);
                }
            }
        }

        $this->hasConnection = true;
    }
}
