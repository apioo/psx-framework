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

namespace PSX\Framework\Test;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\MigratorConfiguration;
use Psr\Container\ContainerInterface;
use PSX\Framework\Bootstrap;
use PSX\Framework\Connection\ConnectionFactory;

/**
 * Environment
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Environment
{
    private static self $instance;

    private bool $hasConnection = false;
    private Connection $connection;
    private ConnectionFactory $connectionFactory;
    private ContainerInterface $container;
    private DependencyFactory $dependencyFactory;

    public function __construct(ConnectionFactory $connectionFactory, ContainerInterface $container, DependencyFactory $dependencyFactory)
    {
        $this->connectionFactory = $connectionFactory;
        $this->container = $container;
        $this->dependencyFactory = $dependencyFactory;
    }

    /**
     * Setups the environment to run unit tests. Includes the DI container and optional creates a database schema
     */
    public function setup(array $params): void
    {
        Bootstrap::setupEnvironment();

        $this->setupConnection($params);
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

    /**
     * @template T
     * @psalm-param class-string<T> $id
     * @return T
     */
    public static function getService(string $id): mixed
    {
        return self::$instance->container->get($id);
    }

    public static function getConfig(string $name): mixed
    {
        return self::$instance->container->getParameter($name);
    }

    private function setupConnection(array $params): void
    {
        $this->connectionFactory->setParams($params);
        $this->connection = $this->connectionFactory->factory();
        $this->hasConnection = true;

        // check whether we can execute
        $this->dependencyFactory->getMetadataStorage()->ensureInitialized();

        $migratorConfiguration = new MigratorConfiguration();
        $planCalculator = $this->dependencyFactory->getMigrationPlanCalculator();
        $version = $this->dependencyFactory->getVersionAliasResolver()->resolveVersionAlias('latest');
        $plan = $planCalculator->getPlanUntilVersion($version);

        $migrator = $this->dependencyFactory->getMigrator();
        $migrator->migrate($plan, $migratorConfiguration);

        self::$instance = $this;
    }
}
