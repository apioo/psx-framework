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

namespace PSX\Framework\Tests\App\Api\Population;

use PSX\Framework\Tests\App\ApiTestCase;
use PSX\Json\Parser;

/**
 * VersionTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VersionTest extends ApiTestCase
{
    public function testGetDefaultVersion()
    {
        $response = $this->sendRequest('/population/2', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "id": 2,
    "place": 2,
    "region": "United States",
    "population": 307212123,
    "users": 227719000,
    "worldUsers": 13.1,
    "datetime": "2009-11-29T15:22:40Z"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetExplicitVersion()
    {
        $response = $this->sendRequest('/population/2', 'GET', ['Accept' => 'application/vnd.psx.v1+json']);

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "id": 2,
    "place": 2,
    "region": "United States",
    "population": 307212123,
    "users": 227719000,
    "worldUsers": 13.1,
    "datetime": "2009-11-29T15:22:40Z"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetInvalidVersion()
    {
        $response = $this->sendRequest('/population/2', 'GET', ['Accept' => 'application/vnd.psx.v8+json']);

        $body = (string) $response->getBody();
        $body = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $body->success);
        $this->assertEquals('PSX\\Http\\Exception\\NotAcceptableException', $body->title);
        $this->assertEquals('Version is not available', substr($body->message, 0, 24));
    }
}
