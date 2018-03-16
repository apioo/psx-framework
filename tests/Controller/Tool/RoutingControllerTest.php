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

namespace PSX\Framework\Tests\Controller\Tool;

use PSX\Framework\Controller\Tool\RoutingController;
use PSX\Framework\Controller\Tool\Documentation;
use PSX\Framework\Test\ControllerTestCase;

/**
 * RoutingControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingControllerTest extends ControllerTestCase
{
    public function testDocumentation()
    {
        $response = $this->sendRequest('/doc/*/routing', 'GET');

        $actual = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "path": "\/routing",
    "version": "*",
    "status": 1,
    "description": null,
    "schema": {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "urn:schema.phpsx.org#",
        "definitions": {
            "Route": {
                "type": "object",
                "title": "route",
                "properties": {
                    "methods": {
                        "type": "array",
                        "items": {
                            "type": "string"
                        }
                    },
                    "path": {
                        "type": "string"
                    },
                    "source": {
                        "type": "string"
                    }
                }
            },
            "Collection": {
                "type": "object",
                "title": "collection",
                "properties": {
                    "routings": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/Route"
                        }
                    }
                }
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/Collection"
            }
        }
    },
    "methods": {
        "GET": {
            "responses": {
                "200": "#\/definitions\/GET-200-response"
            }
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testIndex()
    {
        $response = $this->sendRequest('/routing', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "routings": [
        {
            "methods": [
                "GET"
            ],
            "path": "\/routing",
            "source": "PSX\\Framework\\Controller\\Tool\\RoutingController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/doc\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Tool\\Documentation\\DetailController"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/routing', RoutingController::class],
            [['GET'], '/doc/:version/*path', Documentation\DetailController::class],
        );
    }
}
