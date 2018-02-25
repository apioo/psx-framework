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

namespace PSX\Framework\App\Test\Api;

use PSX\Framework\Test\Environment;
use PSX\Framework\App\ApiTestCase;

/**
 * EntityTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class EntityTest extends ApiTestCase
{
    /**
     * @dataProvider routeDataProvider
     */
    public function testGet($path)
    {
        $response = $this->sendRequest('/' . str_replace(':id', 1, $path), 'GET');

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/entity.json');

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testGetNotFound($path)
    {
        $response = $this->sendRequest('/' . str_replace(':id', 16, $path), 'GET');

        $actual = (string) $response->getBody();

        $this->assertEquals(404, $response->getStatusCode(), $actual);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPost($path)
    {
        $response = $this->sendRequest('/' . str_replace(':id', 1, $path), 'POST');

        $actual = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $actual);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPut($path)
    {
        $payload = json_encode([
            'id'         => 1,
            'place'      => 11,
            'region'     => 'Foo',
            'population' => 1024,
            'users'      => 512,
            'worldUsers' => 0.6,
        ]);

        $response = $this->sendRequest('/' . str_replace(':id', 1, $path), 'PUT', ['Content-Type' => 'application/json'], $payload);

        $actual = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Update successful"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'place', 'region', 'population', 'users', 'worldUsers')
            ->from('population')
            ->where('id = :id')
            ->getSQL();

        $result = Environment::getService('connection')->fetchAssoc($sql, ['id' => 1]);
        $expect = [
            'id' => 1,
            'place' => 11,
            'region' => 'Foo',
            'population' => 1024,
            'users' => 512,
            'worldUsers' => 0.6
        ];

        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testDelete($path)
    {
        $response = $this->sendRequest('/' . str_replace(':id', 1, $path), 'DELETE');

        $actual = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Delete successful"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'place', 'region', 'population', 'users', 'worldUsers')
            ->from('population')
            ->where('id = :id')
            ->getSQL();

        $result = Environment::getService('connection')->fetchAssoc($sql, ['id' => 1]);

        $this->assertEmpty($result);
    }

    public function routeDataProvider()
    {
        return [
            ['population/popo/:id'],
            ['population/jsonschema/:id'],
            ['population/raml/:id'],
            ['population/openapi/:id'],
        ];
    }
}
