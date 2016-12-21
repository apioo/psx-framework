<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Tests\Console;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ConsoleTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConsoleTest extends ControllerTestCase
{
    public function testCommand()
    {
        /** @var \Symfony\Component\Console\Application $application */
        $application = Environment::getService('console');
        $commands    = $application->all();

        $keys = array_keys($commands);
        sort($keys);

        $expect = [
            'api',
            'container',
            'dbal:import',
            'dbal:reserved-words',
            'dbal:run-sql',
            'help',
            'list',
            'route',
            'schema',
            'serve',
            'sql:generate',
            'sql:migrate',
        ];

        $this->assertEquals($expect, $keys);
    }
}
