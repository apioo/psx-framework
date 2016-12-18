<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Tests\Console;

use PSX\Data\Exporter;
use PSX\Framework\Console\ResourceCommand;
use PSX\Framework\Test\Assert;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ResourceCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceCommandTest extends ControllerTestCase
{
    /**
     * @var \PSX\Framework\Console\ResourceCommand
     */
    protected $command;

    protected function setUp()
    {
        parent::setUp();

        $this->command = new ResourceCommand(
            Environment::getService('config'),
            Environment::getService('resource_listing'),
            new Exporter\Popo(Environment::getService('annotation_reader'))
        );
    }

    public function testJsonSchema()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'jsonschema'
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "urn:schema.phpsx.org#",
    "definitions": {
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
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testRaml()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'raml'
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<'YAML'
#%RAML 1.0
---
baseUri: 'http://127.0.0.1/'
version: v1
title: foo
/api:
  description: 'lorem ipsum'
  uriParameters:
    name:
      type: string
      description: 'Name parameter'
      required: false
      minLength: 0
      maxLength: 16
      pattern: '[A-z]+'
    type:
      type: string
      required: false
      enum: [foo, bar]
  get:
    description: 'Returns a collection'
    queryParameters:
      startIndex:
        type: integer
        description: 'startIndex parameter'
        required: false
        minimum: 0
        maximum: 32
      float:
        type: number
        required: false
      boolean:
        type: boolean
        required: false
      date:
        type: date-only
        required: false
      datetime:
        type: datetime-only
        required: false
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "definitions": {
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
                          }
                      }
                  },
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
              }
  post:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
                  "title",
                  "date"
              ]
          }
    responses:
      201:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
              }
  put:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
              }
  delete:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
              }
  patch:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
              }

YAML;

        $expect = str_replace(array("\r\n", "\n", "\r"), "\n", $expect);
        $actual = str_replace(array("\r\n", "\n", "\r"), "\n", $actual);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testSwagger()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'swagger',
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<'JSON'
{
    "swagger": "2.0",
    "info": {
        "title": "PSX",
        "version": "1"
    },
    "basePath": "http:\/\/127.0.0.1\/",
    "paths": {
        "\/api": {
            "get": {
                "description": "Returns a collection",
                "operationId": "getCollection",
                "parameters": [
                    {
                        "description": "startIndex parameter",
                        "name": "startIndex",
                        "in": "query",
                        "required": false,
                        "type": "integer",
                        "maximum": 32,
                        "minimum": 0
                    },
                    {
                        "name": "float",
                        "in": "query",
                        "required": false,
                        "type": "number"
                    },
                    {
                        "name": "boolean",
                        "in": "query",
                        "required": false,
                        "type": "boolean"
                    },
                    {
                        "name": "date",
                        "in": "query",
                        "required": false,
                        "type": "string",
                        "format": "date"
                    },
                    {
                        "name": "datetime",
                        "in": "query",
                        "required": false,
                        "type": "string",
                        "format": "date-time"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "GET 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Collection"
                        }
                    }
                }
            },
            "put": {
                "operationId": "putItem",
                "parameters": [
                    {
                        "description": "PUT request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "PUT 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "post": {
                "operationId": "postItem",
                "parameters": [
                    {
                        "description": "POST request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "201": {
                        "description": "POST 201 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "delete": {
                "operationId": "deleteItem",
                "parameters": [
                    {
                        "description": "DELETE request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "DELETE 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "patch": {
                "operationId": "patchItem",
                "parameters": [
                    {
                        "description": "PATCH request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "PATCH 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "parameters": [
                {
                    "description": "Name parameter",
                    "name": "name",
                    "in": "path",
                    "required": true,
                    "type": "string",
                    "maxLength": 16,
                    "minLength": 0,
                    "pattern": "[A-z]+"
                },
                {
                    "name": "type",
                    "in": "path",
                    "required": true,
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                }
            ]
        }
    },
    "definitions": {
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
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testCommandAvailable()
    {
        $command = Environment::getService('console')->find('resource');

        $this->assertInstanceOf('PSX\Framework\Console\ResourceCommand', $command);
    }

    protected function getPaths()
    {
        return [
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController']
        ];
    }
}
