<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Controller;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;

/**
 * SupportedWriterControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SupportedWriterControllerTest extends ControllerTestCase
{
    public function testIndex()
    {
        $response = $this->sendRequest('http://127.0.0.1/', 'GET', ['Accept' => 'application/json']);
        $body     = (string) $response->getBody();

        $expect = <<<XML
<record type="object">
  <foo type="string">bar</foo>
</record>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testForward()
    {
        $response = $this->sendRequest('http://127.0.0.1/forward', 'GET', ['Accept' => 'application/json']);
        $body     = (string) $response->getBody();

        $expect = <<<XML
<record type="object">
  <bar type="string">foo</bar>
</record>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testError()
    {
        Environment::getService('config')->set('psx_debug', false);

        $response = $this->sendRequest('http://127.0.0.1/error', 'GET', ['Accept', 'application/json']);
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<error type="object">
 <success type="boolean">false</success>
 <title type="string">Internal Server Error</title>
 <message type="string">The server encountered an internal error and was unable to complete your request.</message>
</error>
XML;

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/', 'PSX\Framework\Tests\Controller\Foo\Application\TestSupportedWriterController::doIndex'],
            [['GET'], '/forward', 'PSX\Framework\Tests\Controller\Foo\Application\TestSupportedWriterController::doForward'],
            [['GET'], '/error', 'PSX\Framework\Tests\Controller\Foo\Application\TestSupportedWriterController::doError'],
            [['GET'], '/inherit', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doInheritSupportedWriter'],
        );
    }
}
