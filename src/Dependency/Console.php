<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Dependency;

use Doctrine\DBAL\Tools\Console\Command as DBALCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use PSX\Api\Console\ApiCommand;
use PSX\Data\Exporter;
use PSX\Framework\Base;
use PSX\Framework\Console as PSXCommand;
use PSX\Framework\Console\Reader;
use PSX\Schema\Console\SchemaCommand;
use PSX\Sql\Console as SqlConsole;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Console
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait Console
{
    /**
     * @return \Symfony\Component\Console\Application
     */
    public function getConsole()
    {
        $application = new Application('psx', Base::getVersion());
        $application->setHelperSet(new HelperSet($this->appendConsoleHelpers()));

        $this->appendConsoleCommands($application);

        return $application;
    }

    /**
     * @return \PSX\Framework\Console\ReaderInterface
     */
    public function getConsoleReader()
    {
        return new Reader\Stdin();
    }

    protected function appendConsoleCommands(Application $application)
    {
        $application->add(new PSXCommand\ContainerCommand($this));
        $application->add(new PSXCommand\RouteCommand($this->get('routing_parser')));
        $application->add(new PSXCommand\ServeCommand($this->get('config'), $this->get('dispatch'), $this->get('console_reader')));

        $application->add(new ApiCommand($this->get('api_manager'), $this->get('annotation_reader'), $this->get('config')->get('psx_json_namespace'), $this->get('config')->get('psx_url'), $this->get('config')->get('psx_dispatch')));
        $application->add(new SchemaCommand($this->get('schema_manager')));

        $application->add(new SqlConsole\MigrateCommand($this->get('connection'), $this->get('table_manager')));
        $application->add(new SqlConsole\GenerateCommand($this->get('connection')));

        // symfony commands
        $application->add(new SymfonyCommand\HelpCommand());
        $application->add(new SymfonyCommand\ListCommand());

        // dbal commands
        $application->add(new DBALCommand\ImportCommand());
        $application->add(new DBALCommand\ReservedWordsCommand());
        $application->add(new DBALCommand\RunSqlCommand());
    }

    /**
     * @return array
     */
    protected function appendConsoleHelpers()
    {
        return array(
            'db' => new ConnectionHelper($this->get('connection')),
        );
    }
}
