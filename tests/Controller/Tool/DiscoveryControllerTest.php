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

use PSX\Framework\Controller\Generator\OpenAPIController;
use PSX\Framework\Controller\Generator\RamlController;
use PSX\Framework\Controller\Generator\SwaggerController;
use PSX\Framework\Controller\Tool\DiscoveryController;
use PSX\Framework\Controller\Tool\Documentation;
use PSX\Framework\Controller\Tool\RoutingController;
use PSX\Framework\Test\ControllerTestCase;

/**
 * DiscoveryControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryControllerTest extends ControllerTestCase
{
    public function testDocumentation()
    {
        $response = $this->sendRequest('/doc/*/discovery', 'GET');

        $actual = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "path": "\/discovery",
    "version": "*",
    "status": 1,
    "description": null,
    "schema": {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "urn:schema.phpsx.org#",
        "definitions": {
            "Link": {
                "type": "object",
                "title": "link",
                "properties": {
                    "rel": {
                        "type": "string"
                    },
                    "href": {
                        "type": "string"
                    }
                }
            },
            "Collection": {
                "type": "object",
                "title": "collection",
                "properties": {
                    "links": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/Link"
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
    },
    "links": [
        {
            "rel": "openapi",
            "href": "\/openapi"
        },
        {
            "rel": "swagger",
            "href": "\/swagger"
        },
        {
            "rel": "raml",
            "href": "\/raml"
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testIndex()
    {
        $response = $this->sendRequest('/discovery', 'GET');
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "links": [
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        },
        {
            "rel": "routing",
            "href": "http:\/\/127.0.0.1\/routing"
        },
        {
            "rel": "documentation",
            "href": "http:\/\/127.0.0.1\/doc"
        },
        {
            "rel": "openapi",
            "href": "http:\/\/127.0.0.1\/openapi"
        },
        {
            "rel": "swagger",
            "href": "http:\/\/127.0.0.1\/swagger"
        },
        {
            "rel": "raml",
            "href": "http:\/\/127.0.0.1\/raml"
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
            [['GET'], '/discovery', DiscoveryController::class],
            [['GET'], '/routing', RoutingController::class],
            [['GET'], '/doc', Documentation\IndexController::class],
            [['GET'], '/doc/:version/*path', Documentation\DetailController::class],
            [['GET'], '/openapi', OpenAPIController::class],
            [['GET'], '/swagger', SwaggerController::class],
            [['GET'], '/raml', RamlController::class],
        );
    }
}
