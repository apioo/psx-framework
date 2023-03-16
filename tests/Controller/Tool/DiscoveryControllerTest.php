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

namespace PSX\Framework\Tests\Controller\Tool;

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
        $response = $this->sendRequest('/routing', 'GET');

        $actual = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "status": 1,
    "path": "\/discovery",
    "methods": {
        "GET": {
            "operationId": "PSX_Framework_Controller_Tool_DiscoveryController_doGet",
            "tags": [],
            "responses": {
                "200": "PSX_Framework_Controller_Tool_DiscoveryController_doGet_GET_200_Response"
            }
        }
    },
    "definitions": {
        "Discovery_Collection": {
            "type": "object",
            "properties": {
                "links": {
                    "type": "array",
                    "items": {
                        "$ref": "Discovery_Link"
                    }
                }
            }
        },
        "Discovery_Link": {
            "type": "object",
            "properties": {
                "rel": {
                    "type": "string"
                },
                "href": {
                    "type": "string"
                }
            }
        },
        "PSX_Framework_Controller_Tool_DiscoveryController_doGet_GET_200_Response": {
            "$ref": "Discovery_Collection"
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testIndex()
    {
        $response = $this->sendRequest('/system/discovery', 'GET');
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
            "rel": "raml",
            "href": "http:\/\/127.0.0.1\/raml"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }
}
