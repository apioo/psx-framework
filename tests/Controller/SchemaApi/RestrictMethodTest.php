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

namespace PSX\Framework\Tests\Controller\SchemaApi;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Controller\Foo\Application\SchemaApi\RestrictMethodController;

/**
 * RestrictMethodTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RestrictMethodTest extends ControllerTestCase
{
    public function testHead()
    {
        $response = $this->sendRequest('/api', 'HEAD');
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($body);
    }

    public function testGet()
    {
        $response = $this->sendRequest('/api', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "foo": "bar"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPost()
    {
        $response = $this->sendRequest('/api', 'POST', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $body);
        $this->assertEquals('OPTIONS, HEAD, GET, DELETE', $response->getHeader('Allow'), $body);
    }

    public function testPut()
    {
        $response = $this->sendRequest('/api', 'PUT', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('OPTIONS, HEAD, GET, DELETE', $response->getHeader('Allow'));
    }

    public function testDelete()
    {
        $response = $this->sendRequest('/api', 'DELETE', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testPatch()
    {
        $response = $this->sendRequest('/api', 'PATCH', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('OPTIONS, HEAD, GET, DELETE', $response->getHeader('Allow'));
    }

    public function testOptions()
    {
        $response = $this->sendRequest('/api', 'OPTIONS', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OPTIONS, HEAD, GET, DELETE', $response->getHeader('Allow'));
    }

    protected function getPaths()
    {
        return array(
            [['ANY'], '/api', RestrictMethodController::class],
        );
    }
}
