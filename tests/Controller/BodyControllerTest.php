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

namespace PSX\Framework\Tests\Controller;

use PSX\Framework\Test\ControllerTestCase;

/**
 * BodyControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class BodyControllerTest extends ControllerTestCase
{
    public function testSetArrayBody()
    {
        $response = $this->sendRequest('/tests/body?type=array', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'content-type' => ['application/json'],
            'vary' => ['Accept'],
            'x-stability' => ['experimental'],
            'x-operation-id' => ['PSX.Framework.Tests.Controller.Foo.Application.BodyController.doGet'],
        ], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetStdClassBody()
    {
        $response = $this->sendRequest('/tests/body?type=stdclass', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'content-type' => ['application/json'],
            'vary' => ['Accept'],
            'x-stability' => ['experimental'],
            'x-operation-id' => ['PSX.Framework.Tests.Controller.Foo.Application.BodyController.doGet'],
        ], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetRecordBody()
    {
        $response = $this->sendRequest('/tests/body?type=record', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'content-type' => ['application/json'],
            'vary' => ['Accept'],
            'x-stability' => ['experimental'],
            'x-operation-id' => ['PSX.Framework.Tests.Controller.Foo.Application.BodyController.doGet'],
        ], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetDomDocumentBody()
    {
        $response = $this->sendRequest('/tests/body?type=dom', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'content-type' => ['application/xml'],
            'x-stability' => ['experimental'],
            'x-operation-id' => ['PSX.Framework.Tests.Controller.Foo.Application.BodyController.doGet'],
        ], $response->getHeaders(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testSetSimpleXmlBody()
    {
        $response = $this->sendRequest('/tests/body?type=simplexml', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'content-type' => ['application/xml'],
            'x-stability' => ['experimental'],
            'x-operation-id' => ['PSX.Framework.Tests.Controller.Foo.Application.BodyController.doGet'],
        ], $response->getHeaders(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testSetStringBody()
    {
        $response = $this->sendRequest('/tests/body?type=string', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<TEXT
foobar
TEXT;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals($expect, $body, $body);
    }

    public function testSetStreamBody()
    {
        $response = $this->sendRequest('/tests/body?type=stream', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'content-type' => ['application/octet-stream'],
            'content-disposition' => ['attachment; filename="foo.txt"'],
            'x-stability' => ['experimental'],
            'x-operation-id' => ['PSX.Framework.Tests.Controller.Foo.Application.BodyController.doGet'],
        ], $response->getHeaders(), $body);
        $this->assertEquals('foobar', $body, $body);
    }

    public function testSetBodyBody()
    {
        $response = $this->sendRequest('/tests/body?type=body', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'content-type' => ['application/json'],
            'x-stability' => ['experimental'],
            'x-operation-id' => ['PSX.Framework.Tests.Controller.Foo.Application.BodyController.doGet'],
        ], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString('{"foo": "bar"}', $body, $body);
    }
}
