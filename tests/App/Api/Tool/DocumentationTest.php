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
 * DocumentationTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('/tool/doc', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "routings": [
        {
            "path": "\/population\/popo",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/popo\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/jsonschema",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/jsonschema\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/raml",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/raml\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/openapi",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/openapi\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        }
    ],
    "links": [
        {
            "rel": "self",
            "href": "http:\/\/127.0.0.1\/tool\/doc"
        },
        {
            "rel": "detail",
            "href": "http:\/\/127.0.0.1\/tool\/doc\/{version}\/{path}"
        },
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetDetail()
    {
        $response = $this->sendRequest('/tool/doc/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "path": "\/population\/popo",
    "version": "*",
    "status": 1,
    "description": "Collection endpoint",
    "schema": {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "urn:schema.phpsx.org#",
        "definitions": {
            "GET-query": {
                "type": "object",
                "title": "query",
                "properties": {
                    "startIndex": {
                        "type": "integer"
                    },
                    "count": {
                        "type": "integer"
                    }
                },
                "required": []
            },
            "Entity": {
                "type": "object",
                "title": "entity",
                "description": "Represents an internet population entity",
                "properties": {
                    "id": {
                        "type": "integer",
                        "description": "Unique id for each entry"
                    },
                    "place": {
                        "type": "integer",
                        "description": "Position in the top list",
                        "minimum": 1,
                        "maximum": 64
                    },
                    "region": {
                        "type": "string",
                        "description": "Name of the region",
                        "pattern": "[A-z]+",
                        "minLength": 3,
                        "maxLength": 64
                    },
                    "population": {
                        "type": "integer",
                        "description": "Complete number of population"
                    },
                    "users": {
                        "type": "integer",
                        "description": "Number of internet users"
                    },
                    "worldUsers": {
                        "type": "number",
                        "description": "Percentage users of the world"
                    },
                    "datetime": {
                        "type": "string",
                        "description": "Date when the entity was created",
                        "format": "date-time"
                    }
                },
                "required": [
                    "place",
                    "region",
                    "population",
                    "users",
                    "worldUsers"
                ],
                "class": "PSX\\Project\\Tests\\Model\\Entity"
            },
            "Collection": {
                "type": "object",
                "title": "collection",
                "description": "Collection result",
                "properties": {
                    "totalResults": {
                        "type": "integer"
                    },
                    "entry": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/Entity"
                        }
                    }
                },
                "class": "PSX\\Project\\Tests\\Model\\Collection"
            },
            "Message": {
                "type": "object",
                "title": "message",
                "description": "Operation message",
                "properties": {
                    "success": {
                        "type": "boolean"
                    },
                    "message": {
                        "type": "string"
                    }
                },
                "class": "PSX\\Project\\Tests\\Model\\Message"
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/Collection"
            },
            "POST-request": {
                "$ref": "#\/definitions\/Entity"
            },
            "POST-201-response": {
                "$ref": "#\/definitions\/Message"
            }
        }
    },
    "methods": {
        "GET": {
            "queryParameters": "#\/definitions\/GET-query",
            "responses": {
                "200": "#\/definitions\/GET-200-response"
            }
        },
        "POST": {
            "request": "#\/definitions\/POST-request",
            "responses": {
                "201": "#\/definitions\/POST-201-response"
            }
        }
    },
    "links": [
        {
            "rel": "openapi",
            "href": "\/generator\/openapi\/*\/population\/popo"
        },
        {
            "rel": "swagger",
            "href": "\/generator\/swagger\/*\/population\/popo"
        },
        {
            "rel": "raml",
            "href": "\/generator\/raml\/*\/population\/popo"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}
