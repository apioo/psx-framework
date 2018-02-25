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

namespace PSX\Framework\Tests\Http;

use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Model\Property;
use PSX\Http\Request;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Uri;

/**
 * RequestReaderTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBodyJson()
    {
        $json = <<<JSON
{
    "foo": "bar"
}
JSON;

        $headers = [];
        $body    = new StringStream($json);
        $request = new Request(new Uri('/'), 'POST', $headers, $body);

        $data = $this->newRequestReader()->getBody($request);

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertJsonStringEqualsJsonString($json, \json_encode($data));
    }

    public function testGetBodyExplicitJson()
    {
        $json = <<<JSON
{
    "foo": "bar"
}
JSON;

        $headers = ['Content-Type' => 'application/json'];
        $body    = new StringStream($json);
        $request = new Request(new Uri('/'), 'POST', $headers, $body);

        $data = $this->newRequestReader()->getBody($request);

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertJsonStringEqualsJsonString($json, \json_encode($data));
    }

    public function testGetBodyExplicitXml()
    {
        $xml = <<<XML
<foo>
  <bar>baz</bar>
</foo>
XML;

        $headers = ['Content-Type' => 'application/xml'];
        $body    = new StringStream($xml);
        $request = new Request(new Uri('/'), 'POST', $headers, $body);

        $data = $this->newRequestReader()->getBody($request);

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertJsonStringEqualsJsonString(\json_encode(['bar' => 'baz']), \json_encode($data));
    }

    public function testGetBodyAs()
    {
        $json = <<<JSON
{
    "array": ["foo", "bar"],
    "boolean": true
}
JSON;

        $headers = [];
        $body    = new StringStream($json);
        $request = new Request(new Uri('/'), 'POST', $headers, $body);

        $data = $this->newRequestReader()->getBodyAs($request, Property::class);

        $this->assertInstanceOf(Property::class, $data);
        $this->assertEquals(["foo", "bar"], $data->getArray());
        $this->assertEquals(true, $data->getBoolean());
    }

    /**
     * @return \PSX\Framework\Http\RequestReader
     */
    protected function newRequestReader()
    {
        return Environment::getService('request_reader');
    }
}
