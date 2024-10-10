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

namespace PSX\Framework\Tests\Http;

use PHPUnit\Framework\TestCase;
use PSX\Data\Configuration;
use PSX\Data\Processor;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Tests\Controller\Foo\Model\Property;
use PSX\Http\Request;
use PSX\Http\Stream\StringStream;
use PSX\Schema\SchemaManager;
use PSX\Uri\Uri;

/**
 * RequestReaderTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class RequestReaderTest extends TestCase
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
        $request = new Request(Uri::parse('/'), 'POST', $headers, $body);

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
        $request = new Request(Uri::parse('/'), 'POST', $headers, $body);

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
        $request = new Request(Uri::parse('/'), 'POST', $headers, $body);

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
        $request = new Request(Uri::parse('/'), 'POST', $headers, $body);

        $data = $this->newRequestReader()->getBodyAs($request, Property::class);

        $this->assertInstanceOf(Property::class, $data);
        $this->assertEquals(["foo", "bar"], $data->getArray());
        $this->assertEquals(true, $data->getBoolean());
    }

    protected function newRequestReader(): RequestReader
    {
        $config = Configuration::createDefault(new SchemaManager());
        $processor = new Processor($config);

        return new RequestReader($processor);
    }
}
