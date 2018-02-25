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

namespace PSX\Framework\Tests\Console;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiV2Controller;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ApiGenerateCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiGenerateCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = Environment::getService('console')->find('api:generate');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'dir'      => __DIR__ . '/output',
            '--format' => 'markdown',
        ]);

        $response = $commandTester->getDisplay();

        $this->assertRegExp('/Successful!/', $response);
        $this->assertTrue(is_file(__DIR__ . '/output/controller.md'));
        $this->assertTrue(is_file(__DIR__ . '/output/foo__bar.md'));

        $actual = file_get_contents(__DIR__ . '/output/controller.md');
        $expect = file_get_contents(__DIR__ . '/output/expect.md');

        $this->assertEquals($expect, $actual);
    }

    protected function getPaths()
    {
        return [
            [['GET', 'POST', 'PUT', 'DELETE'], '/controller', TestSchemaApiController::class],
            [['GET', 'POST', 'PUT', 'DELETE'], '/foo/:bar', TestSchemaApiV2Controller::class],
        ];
    }
}
