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
use PSX\Framework\Environment\CLI\Engine;
use PSX\Framework\Environment\Environment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ServeCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServeCommand extends Command
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
            ->setName('serve')
            ->setDescription('Accepts an HTTP request via stdin and returns the HTTP response')
            ->addArgument('method', InputArgument::REQUIRED, 'HTTP method i.e. GET or POST')
            ->addArgument('uri', InputArgument::REQUIRED, 'Request URI i.e. /foo')
            ->addArgument('headers', InputArgument::OPTIONAL, 'Request headers form encoded i.e. "Content-Type=application/json&X-Header=foo"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $engine      = new Engine($input, $output);
        $environment = new Environment($this->container, $engine);

        return $environment->serve();
    }
}
