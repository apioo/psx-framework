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

namespace PSX\Framework\Tests\Command;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Application;

/**
 * ConsoleTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ConsoleTest extends ControllerTestCase
{
    public function testCommand()
    {
        $application = Environment::getService(Application::class);
        $commands    = $application->all();

        $keys = array_keys($commands);
        sort($keys);

        $expect = [
            '_complete',
            'api:push',
            'completion',
            'dbal:run-sql',
            'debug:autowiring',
            'debug:container',
            'debug:event-dispatcher',
            'diff',
            'dump-schema',
            'execute',
            'generate',
            'generate:model',
            'generate:sdk',
            'generate:table',
            'help',
            'latest',
            'list',
            'list-migrations',
            'messenger:consume',
            'messenger:setup-transports',
            'messenger:stop-workers',
            'migrate',
            'migrations:diff',
            'migrations:dump-schema',
            'migrations:execute',
            'migrations:generate',
            'migrations:latest',
            'migrations:list',
            'migrations:migrate',
            'migrations:rollup',
            'migrations:status',
            'migrations:sync-metadata-storage',
            'migrations:up-to-date',
            'migrations:version',
            'rollup',
            'route',
            'serve',
            'status',
            'sync-metadata-storage',
            'up-to-date',
            'version',
        ];

        $this->assertEquals($expect, $keys);
    }
}
