<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Psr\Container\ContainerInterface;
use PSX\Dependency\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Displays all available services from the DI container. Note the getServiceIds
 * method is not defined in the interface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ContainerCommand extends Command
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('container')
            ->setDescription('Displays all registered services');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->container instanceof Container) {
            throw new \RuntimeException('It is not possible to introspect the container');
        }

        $services  = $this->container->getServiceIds();
        $rows      = array();

        sort($services);

        foreach ($services as $serviceId) {
            $return = $this->container->getReturnType($serviceId);

            if (!empty($return)) {
                $definition = $return;
            } else {
                $definition = 'void';
            }

            $rows[] = array($serviceId, $definition);
        }

        $table = new Table($output);
        $table
            ->setStyle('compact')
            ->setRows($rows);

        $table->render();
    }
}
