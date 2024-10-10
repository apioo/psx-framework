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

namespace PSX\Framework\Command;

use PSX\Engine\DispatchInterface;
use PSX\Framework\Environment\CLI\Engine;
use PSX\Framework\Environment\Environment;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ServeCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[AsCommand(name: 'serve', description: 'Accepts an HTTP request via stdin and returns the HTTP response')]
class ServeCommand extends Command
{
    private DispatchInterface $dispatch;

    public function __construct(DispatchInterface $dispatch)
    {
        parent::__construct();

        $this->dispatch = $dispatch;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('method', InputArgument::REQUIRED, 'HTTP method i.e. GET or POST')
            ->addArgument('uri', InputArgument::REQUIRED, 'Request URI i.e. /foo')
            ->addArgument('headers', InputArgument::OPTIONAL, 'Request headers form encoded i.e. "Content-Type=application/json&X-Header=foo"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $engine      = new Engine($input, $output);
        $environment = new Environment($this->dispatch, $engine, true);

        $environment->serve();

        return 0;
    }
}
