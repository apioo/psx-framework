<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Dependency\InspectorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Displays all available services from the DI container
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ContainerCommand extends Command
{
    protected $inspector;

    public function __construct(InspectorInterface $inspector)
    {
        parent::__construct();

        $this->inspector = $inspector;
    }

    protected function configure()
    {
        $this
            ->setName('container')
            ->setDescription('Displays all registered services');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $services = $this->inspector->getTypedServiceIds();
        $rows     = [];

        asort($services);

        foreach ($services as $type => $serviceId) {
            $rows[] = [$serviceId, $type];
        }

        $table = new Table($output);
        $table
            ->setStyle('compact')
            ->setRows($rows);

        $table->render();
    }
}
