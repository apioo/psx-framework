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

namespace PSX\Framework\Tests\Controller;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Controller\Foo\Application\TestController\FilterController;
use PSX\Framework\Tests\Controller\Foo\Application\TestController\IndexController;
use PSX\Framework\Tests\Controller\Foo\Application\TestController\InspectController;
use PSX\Framework\Tests\Controller\Foo\Application\TestController\MethodsController;
use PSX\Framework\Tests\Controller\Foo\Application\TestController\SetBodyController;
use PSX\Framework\Tests\Controller\Foo\Application\TestController\SupportedWriterController;
use PSX\Json\Parser;

/**
 * ControllerAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerAbstractTest extends ControllerTestCase
{
    public function testNormalRequest()
    {
        $response = $this->sendRequest('/controller', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals('foobar', $body, $body);
    }

    public function testInnerApi()
    {
        $data = json_encode(array(
            'foo' => 'bar',
            'bar' => ['foo' => 'nested'],
            'entries' => [['title' => 'bar'], ['title' => 'foo']],
        ));

        $response = $this->sendRequest('/controller/inspect?foo=bar', 'POST', [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json'
        ], $data);

        $body = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString('{"bar": "foo"}', $body, $body);
    }

    public function testSetArrayBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=array', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(['content-type' => ['application/json'], 'vary' => ['Accept']], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetStdClassBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=stdclass', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(['content-type' => ['application/json'], 'vary' => ['Accept']], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetRecordBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=record', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{"foo":["bar"]}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(['content-type' => ['application/json'], 'vary' => ['Accept']], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testSetDomDocumentBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=dom', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(['content-type' => ['application/xml']], $response->getHeaders(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testSetSimpleXmlBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=simplexml', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(['content-type' => ['application/xml']], $response->getHeaders(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testSetStringBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=string', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<TEXT
foobar
TEXT;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals($expect, $body, $body);
    }

    public function testSetStreamBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=stream', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(['content-type' => ['application/octet-stream'], 'content-disposition' => ['attachment; filename="foo.txt"']], $response->getHeaders(), $body);
        $this->assertEquals('foobar', $body, $body);
    }

    public function testSetBodyBody()
    {
        $response = $this->sendRequest('/controller/setbody?type=body', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(['content-type' => ['application/json']], $response->getHeaders(), $body);
        $this->assertJsonStringEqualsJsonString('{"foo": "bar"}', $body, $body);
    }

    /**
     * @dataProvider requestMethodProvider
     */
    public function testAllRequestMethods($requestMethod)
    {
        $response = $this->sendRequest('/controller/methods', $requestMethod);
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);

        if ($requestMethod == 'HEAD') {
            $this->assertEmpty($body);
        } else {
            $this->assertEquals($requestMethod, $body, $body);
        }
    }

    public function requestMethodProvider()
    {
        return array(
            ['DELETE'],
            ['GET'],
            ['HEAD'],
            ['OPTIONS'],
            ['POST'],
            ['PUT'],
            ['TRACE'],
            ['PROPFIND'],
        );
    }

    public function testFilter()
    {
        $response = $this->sendRequest('/controller/filter', 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals('foobar', $body, $body);
    }

    public function testUnknownLocation()
    {
        $response = $this->sendRequest('/controller/foobar', 'GET');
        $body     = (string) $response->getBody();
        $data     = Parser::decode($body, true);

        $this->assertEquals(404, $response->getStatusCode(), $body);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertEquals(false, $data['success']);
        $this->assertEquals('PSX\Framework\Loader\InvalidPathException', $data['title']);
    }

    public function testSupportedWriter()
    {
        $response = $this->sendRequest('/controller/supported_writer', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<record type="object">
 <foo type="string">bar</foo>
</record>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/controller', IndexController::class],
            [['POST'], '/controller/inspect', InspectController::class],
            [['GET'], '/controller/setbody', SetBodyController::class],
            [['ANY'], '/controller/methods', MethodsController::class],
            [['GET'], '/controller/filter', FilterController::class],
            [['GET'], '/controller/supported_writer', SupportedWriterController::class],
        );
    }
}
