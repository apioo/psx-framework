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
use Psr\Container\ContainerInterface;
use PSX\Framework\Bootstrap;

/**
 * Environment
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Environment
{
    private static self $instance;

    private bool $hasConnection = false;
    private Connection $connection;
    private ContainerInterface $container;
    private bool $debug;

    public function __construct(Connection $connection, ContainerInterface $container, bool $debug)
    {
        $this->connection = $connection;
        $this->container = $container;
        $this->debug = $debug;
    }

    /**
     * Setups the environment to run unit tests. Includes the DI container and optional creates a database schema
     */
    public function setup(Closure $schemaSetup = null): void
    {
        Bootstrap::setupEnvironment($this->debug);

        $this->setupConnection($schemaSetup);
    }

    public function hasConnection(): bool
    {
        return $this->hasConnection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public static function getContainer(): ContainerInterface
    {
        return self::$instance->container;
    }

    public static function getService(string $id): mixed
    {
        return self::$instance->container->get($id);
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

    public function register(): void
    {
        self::$instance = $this;
    }
}
