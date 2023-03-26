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

namespace PSX\Framework\Tests\Console;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Application\TestApiController;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ServeCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ServeCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = Environment::getService(Application::class)->find('serve');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'method'  => 'GET',
            'uri'     => '/system/routing',
            'headers' => 'Accept=application/xml',
        ));

        $actual = $commandTester->getDisplay();
        $expect = file_get_contents(__DIR__ . '/output/routes.xml');

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }
}
