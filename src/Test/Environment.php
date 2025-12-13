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

namespace PSX\Framework\Test;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\MigratorConfiguration;
use Psr\Container\ContainerInterface;
use PSX\Framework\Bootstrap;
use PSX\Framework\Connection\ConnectionFactory;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

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
    public function setup(): void
    {
        Bootstrap::setupEnvironment();

        $this->setupConnection();
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
        $container = self::$instance->container;
        if ($container instanceof SymfonyContainerInterface) {
            return $container->getParameter($name);
        } else {
            return null;
        }
    }

    private function setupConnection(): void
    {
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
