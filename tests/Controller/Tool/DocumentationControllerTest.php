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
use PSX\Framework\Controller\Tool\Documentation;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController;

/**
 * DocumentationControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationControllerTest extends ControllerTestCase
{
    public function testDocumentationIndex()
    {
        $response = $this->sendRequest('/doc/*/doc', 'GET');

        $actual = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "path": "\/doc",
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
                    "path": {
                        "type": "string"
                    },
                    "methods": {
                        "type": "array",
                        "items": {
                            "type": "string"
                        }
                    },
                    "version": {
                        "type": "string"
                    }
                }
            },
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
            "Index": {
                "type": "object",
                "title": "index",
                "properties": {
                    "routings": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/Route"
                        }
                    },
                    "links": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/Link"
                        }
                    }
                }
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/Index"
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

    public function testDocumentationDetail()
    {
        $response = $this->sendRequest('/doc/*/doc/*', 'GET');

        $actual = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "path": "\/doc\/:version\/*path",
    "version": "*",
    "status": 1,
    "description": null,
    "schema": {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "urn:schema.phpsx.org#",
        "definitions": {
            "Schema": {
                "type": "object",
                "title": "schema",
                "description": "Contains the JSON Schema object"
            },
            "Methods": {
                "type": "object",
                "title": "methods",
                "additionalProperties": {
                    "$ref": "#\/definitions\/Method"
                }
            },
            "Method": {
                "type": "object",
                "title": "method",
                "properties": {
                    "description": {
                        "type": "string"
                    },
                    "queryParameters": {
                        "type": "string"
                    },
                    "request": {
                        "type": "string"
                    },
                    "responses": {
                        "type": "array",
                        "additionalProperties": {
                            "type": "string"
                        }
                    }
                }
            },
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
            "Detail": {
                "type": "object",
                "title": "detail",
                "properties": {
                    "path": {
                        "type": "string"
                    },
                    "version": {
                        "type": "string"
                    },
                    "status": {
                        "type": "integer"
                    },
                    "description": {
                        "type": "string"
                    },
                    "schema": {
                        "$ref": "#\/definitions\/Schema"
                    },
                    "pathParameters": {
                        "type": "string"
                    },
                    "methods": {
                        "$ref": "#\/definitions\/Methods"
                    },
                    "links": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/Link"
                        }
                    }
                }
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/Detail"
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
        $response = $this->sendRequest('/doc', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "routings": [
        {
            "path": "\/doc",
            "methods": [
                "GET"
            ],
            "version": "*"
        },
        {
            "path": "\/doc\/:version\/*path",
            "methods": [
                "GET"
            ],
            "version": "*"
        },
        {
            "path": "\/api",
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
            "href": "http:\/\/127.0.0.1\/doc"
        },
        {
            "rel": "detail",
            "href": "http:\/\/127.0.0.1\/doc\/{version}\/{path}"
        },
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    public function testDetail()
    {
        $response = $this->sendRequest('/doc/1/api', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "path": "\/api",
    "version": "1",
    "status": 1,
    "description": "lorem ipsum",
    "schema": {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "urn:schema.phpsx.org#",
        "definitions": {
            "path-template": {
                "type": "object",
                "title": "path",
                "properties": {
                    "name": {
                        "type": "string",
                        "description": "Name parameter",
                        "pattern": "[A-z]+",
                        "minLength": 0,
                        "maxLength": 16
                    },
                    "type": {
                        "type": "string",
                        "enum": [
                            "foo",
                            "bar"
                        ]
                    }
                }
            },
            "GET-query": {
                "type": "object",
                "title": "query",
                "properties": {
                    "startIndex": {
                        "type": "integer",
                        "description": "startIndex parameter",
                        "minimum": 0,
                        "maximum": 32
                    },
                    "float": {
                        "type": "number"
                    },
                    "boolean": {
                        "type": "boolean"
                    },
                    "date": {
                        "type": "string",
                        "format": "date"
                    },
                    "datetime": {
                        "type": "string",
                        "format": "date-time"
                    }
                }
            },
            "Item": {
                "type": "object",
                "title": "item",
                "properties": {
                    "id": {
                        "type": "integer"
                    },
                    "userId": {
                        "type": "integer"
                    },
                    "title": {
                        "type": "string",
                        "pattern": "[A-z]+",
                        "minLength": 3,
                        "maxLength": 16
                    },
                    "date": {
                        "type": "string",
                        "format": "date-time"
                    }
                },
                "required": [
                    "id"
                ]
            },
            "Collection": {
                "type": "object",
                "title": "collection",
                "properties": {
                    "entry": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                }
            },
            "Message": {
                "type": "object",
                "title": "message",
                "properties": {
                    "success": {
                        "type": "boolean"
                    },
                    "message": {
                        "type": "string"
                    }
                }
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/Collection"
            },
            "POST-request": {
                "$ref": "#\/definitions\/Item"
            },
            "POST-201-response": {
                "$ref": "#\/definitions\/Message"
            },
            "PUT-request": {
                "$ref": "#\/definitions\/Item"
            },
            "PUT-200-response": {
                "$ref": "#\/definitions\/Message"
            },
            "DELETE-request": {
                "$ref": "#\/definitions\/Item"
            },
            "DELETE-200-response": {
                "$ref": "#\/definitions\/Message"
            },
            "PATCH-request": {
                "$ref": "#\/definitions\/Item"
            },
            "PATCH-200-response": {
                "$ref": "#\/definitions\/Message"
            }
        }
    },
    "pathParameters": "#\/definitions\/path-template",
    "methods": {
        "GET": {
            "description": "Returns a collection",
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
        },
        "PUT": {
            "request": "#\/definitions\/PUT-request",
            "responses": {
                "200": "#\/definitions\/PUT-200-response"
            }
        },
        "DELETE": {
            "request": "#\/definitions\/DELETE-request",
            "responses": {
                "200": "#\/definitions\/DELETE-200-response"
            }
        },
        "PATCH": {
            "request": "#\/definitions\/PATCH-request",
            "responses": {
                "200": "#\/definitions\/PATCH-200-response"
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

        $this->assertEquals(200, $response->getStatusCode(), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/doc', Documentation\IndexController::class],
            [['GET'], '/doc/:version/*path', Documentation\DetailController::class],
            [['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/api', TestSchemaApiController::class],
            [['GET'], '/openapi', OpenAPIController::class],
            [['GET'], '/swagger', SwaggerController::class],
            [['GET'], '/raml', RamlController::class],
        );
    }
}
