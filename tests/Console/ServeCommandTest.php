<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ServeCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServeCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = Environment::getService('console')->find('serve');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'method'  => 'GET',
            'uri'     => '/api',
            'headers' => 'Accept=application/xml',
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<record type="object">
  <bar type="string">foo</bar>
</record>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/api', TestApiController::class],
        );
    }
}
