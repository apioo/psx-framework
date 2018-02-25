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
 * CollectionTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CollectionTest extends ApiTestCase
{
    /**
     * @dataProvider routeDataProvider
     */
    public function testGetAll($path)
    {
        $response = $this->sendRequest('/' . $path, 'GET');

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/collection.json');

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testGetLimited($path)
    {
        $response = $this->sendRequest('/' . $path . '?startIndex=4&count=4', 'GET');

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/collection_limited.json');

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPost($path)
    {
        $payload = json_encode([
            'id'         => 11,
            'place'      => 11,
            'region'     => 'Foo',
            'population' => 1024,
            'users'      => 512,
            'worldUsers' => 0.6,
        ]);

        $response = $this->sendRequest('/' . $path, 'POST', ['Content-Type' => 'application/json'], $payload);

        $actual = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Create population successful"
}
JSON;

        $this->assertEquals(201, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'place', 'region', 'population', 'users', 'worldUsers')
            ->from('population')
            ->orderBy('id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(2)
            ->getSQL();

        $result = Environment::getService('connection')->fetchAll($sql);
        $expect = [
            ['id' => 11, 'place' => 11, 'region' => 'Foo', 'population' => 1024, 'users' => 512, 'worldUsers' => 0.6],
            ['id' => 10, 'place' => 10, 'region' => 'Korea South', 'population' => 48508972, 'users' => 37475800, 'worldUsers' => 2.2],
        ];

        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPut($path)
    {
        $response = $this->sendRequest('/' . $path, 'PUT');

        $actual = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $actual);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testDelete($path)
    {
        $response = $this->sendRequest('/' . $path, 'DELETE');

        $actual = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $actual);
    }

    public function routeDataProvider()
    {
        return [
            ['population/popo'],
            ['population/jsonschema'],
            ['population/raml'],
            ['population/openapi'],
        ];
    }
}
