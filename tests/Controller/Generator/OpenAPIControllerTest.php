<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Controller\Generator;

use PSX\Framework\Controller\Generator\OpenAPIController;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiV2Controller;

/**
 * OpenAPIControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPIControllerTest extends ControllerTestCase
{
    public function testIndex()
    {
        $response = $this->sendRequest('/openapi/1/api', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();
        $expect   = file_get_contents(__DIR__ . '/resource/openapi.json');

        $this->assertEquals(200, $response->getStatusCode(), $json);
        $this->assertEquals('application/json', $response->getHeader('Content-Type'), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    public function testCollection()
    {
        $response = $this->sendRequest('/openapi/1/*', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();
        $expect   = file_get_contents(__DIR__ . '/resource/openapi_collection.json');

        $this->assertEquals(200, $response->getStatusCode(), $json);
        $this->assertEquals('application/json', $response->getHeader('Content-Type'), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/openapi/:version/*path', OpenAPIController::class],
            [['ANY'], '/api', TestSchemaApiController::class],
            [['ANY'], '/endpoint', TestSchemaApiV2Controller::class],
        );
    }
}
