<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Framework\Controller\Generator\RamlController;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiV2Controller;

/**
 * RamlControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlControllerTest extends ControllerTestCase
{
    public function testIndex()
    {
        $response = $this->sendRequest('/raml/1/api', 'GET', ['Accept' => 'application/json']);
        $raml     = (string) $response->getBody();
        $expect   = file_get_contents(__DIR__ . '/resource/raml.yaml');
        $expect   = str_replace(array("\r\n", "\r"), "\n", $expect);

        $this->assertEquals(null, $response->getStatusCode(), $raml);
        $this->assertEquals('application/raml+yaml', $response->getHeader('Content-Type'), $raml);
        $this->assertEquals($expect, $raml, $raml);
    }

    public function testCollection()
    {
        $response = $this->sendRequest('/raml/1/*', 'GET', ['Accept' => 'application/json']);
        $raml     = (string) $response->getBody();
        $expect   = file_get_contents(__DIR__ . '/resource/raml_collection.yaml');

        $this->assertEquals(null, $response->getStatusCode(), $raml);
        $this->assertEquals('application/raml+yaml', $response->getHeader('Content-Type'), $raml);
        $this->assertEquals(str_replace(array("\r\n", "\r"), "\n", $expect), $raml, $raml);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/raml/:version/*path', RamlController::class],
            [['ANY'], '/api', TestSchemaApiController::class],
            [['ANY'], '/endpoint', TestSchemaApiV2Controller::class],
        );
    }
}
