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

namespace PSX\Framework\Tests\App\Api\Tool;

use PSX\Framework\Tests\App\ApiTestCase;

/**
 * RoutingTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('/tool/routing', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "routings": [
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/popo",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionPopo"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/popo\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityPopo"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/jsonschema",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionJsonSchema"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/jsonschema\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityJsonSchema"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/raml",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionRaml"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/raml\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityRaml"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/openapi",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionOpenAPI"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/openapi\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityOpenAPI"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population",
            "source": "PSX\\Project\\Tests\\Api\\Population\\Collection"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\Entity"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool",
            "source": "PSX\\Framework\\Controller\\Tool\\DefaultController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/discovery",
            "source": "PSX\\Framework\\Controller\\Tool\\DiscoveryController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/doc",
            "source": "PSX\\Framework\\Controller\\Tool\\DocumentationController::doIndex"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/doc\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Tool\\DocumentationController::doDetail"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/routing",
            "source": "PSX\\Framework\\Controller\\Tool\\RoutingController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/raml\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\RamlController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/swagger\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\SwaggerController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/openapi\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\OpenAPIController"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/proxy\/soap",
            "source": "PSX\\Framework\\Controller\\Proxy\\SoapController"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}
