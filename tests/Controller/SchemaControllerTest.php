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

use PSX\Framework\Test\ControllerDbTestCase;

/**
 * SchemaControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class SchemaControllerTest extends ControllerDbTestCase
{
    public function getDataSet(): array
    {
        return $this->createFromFile(__DIR__ . '/../table_fixture.php');
    }

    public function testGet()
    {
        $response = $this->sendRequest('/tests/schema/foo/bar', 'GET');
        $body     = (string) $response->getBody();
        $expect   = file_get_contents(__DIR__ . '/resource/schema_api_abstract_get.json');

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPost()
    {
        $data     = json_encode(['userId' => 3, 'title' => 'test', 'date' => '2013-05-29T16:56:32Z']);
        $response = $this->sendRequest('/tests/schema/foo/bar', 'POST', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "success": true,
    "message": "You have successful post a record"
}
JSON;

        $this->assertEquals(201, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPostInvalidTitleLength()
    {
        $data     = json_encode(['userId' => 3, 'title' => 'foobarfoobarfoobarfoobar', 'date' => '2013-05-29T16:56:32Z']);
        $response = $this->sendRequest('/tests/schema/foo/bar', 'POST', ['Content-Type' => 'application/json'], $data);

        $body = (string) $response->getBody();
        $data = \json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertFalse($data->success);
        $this->assertStringStartsWith('/title must contain less or equal than 16 characters', $data->message);
    }

    public function testPostInvalidFields()
    {
        $data     = json_encode(['foobar' => 'title']);
        $response = $this->sendRequest('/tests/schema/foo/bar', 'POST', ['Content-Type' => 'application/json'], $data);

        $body = (string) $response->getBody();
        $data = \json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertFalse($data->success);
        $this->assertStringStartsWith('/ the following properties are required: title, date', $data->message);
    }

    public function testPut()
    {
        $data     = json_encode(['id' => 1, 'userId' => 3, 'title' => 'foobar']);
        $response = $this->sendRequest('/tests/schema/foo/bar', 'PUT', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "success": true,
    "message": "You have successful put a record"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testDelete()
    {
        $data     = json_encode(['id' => 1]);
        $response = $this->sendRequest('/tests/schema/foo/bar', 'DELETE', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "success": true,
    "message": "You have successful delete a record"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPatch()
    {
        $data     = json_encode(['id' => 1, 'userId' => 3, 'title' => 'foobar']);
        $response = $this->sendRequest('/tests/schema/foo/bar', 'PATCH', ['Content-Type' => 'application/json'], $data);
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "success": true,
    "message": "You have successful patch a record"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testOptions()
    {
        $response = $this->sendRequest('/tests/schema/foo/bar', 'OPTIONS', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'x-operation-id' => ['tests.controller.foo.application.schema_controller.doGet'],
            'x-stability' => ['experimental'],
        ], $response->getHeaders());
        $this->assertEmpty($body, $body);
    }

    public function testOptionsCors()
    {
        $response = $this->sendRequest('/tests/schema/foo/bar', 'OPTIONS', ['Content-Type' => 'application/json', 'Access-Control-Request-Method' => 'DELETE', 'Access-Control-Request-Headers' => 'origin, authorization, Content-Type', 'Origin' => 'https://foo.bar.org']);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertEquals([
            'access-control-allow-origin' => ['*'],
            'access-control-allow-methods' => ['OPTIONS, HEAD, GET, POST, PUT, DELETE, PATCH'],
            'access-control-allow-headers' => ['Accept, Accept-Language, Authorization, Content-Language, Content-Type'],
            'access-control-expose-headers' => ['*'],
            'x-operation-id' => ['tests.controller.foo.application.schema_controller.doGet'],
            'x-stability' => ['experimental'],
        ], $response->getHeaders());
        $this->assertEmpty($body);
    }
}
