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

namespace PSX\Framework\Command;

use PSX\Framework\Loader\RoutingParserInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RouteCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[AsCommand(name: 'route', description: 'Displays all available routes')]
class RouteCommand extends Command
{
    private RoutingParserInterface $routingParser;

    public function __construct(RoutingParserInterface $routingParser)
    {
        parent::__construct();

        $this->routingParser = $routingParser;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collection = $this->routingParser->getCollection();
        $rows       = [];

        foreach ($collection as $route) {
            $rows[] = [implode('|', $route[0]), $route[1], implode('::', $route[2])];
        }

        $table = new Table($output);
        $table
            ->setStyle('compact')
            ->setRows($rows);

        $table->render();

        return 0;
    }
}
