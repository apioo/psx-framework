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

namespace PSX\Framework\App\Api\Tool;

use PSX\Framework\App\ApiTestCase;

/**
 * DiscoveryTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('/tool/discovery', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "links": [
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        },
        {
            "rel": "routing",
            "href": "http:\/\/127.0.0.1\/tool\/routing"
        },
        {
            "rel": "documentation",
            "href": "http:\/\/127.0.0.1\/tool\/doc"
        },
        {
            "rel": "openapi",
            "href": "http:\/\/127.0.0.1\/generator\/openapi\/{version}\/{path}"
        },
        {
            "rel": "swagger",
            "href": "http:\/\/127.0.0.1\/generator\/swagger\/{version}\/{path}"
        },
        {
            "rel": "raml",
            "href": "http:\/\/127.0.0.1\/generator\/raml\/{version}\/{path}"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}
