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

namespace PSX\Framework\Tests\Oauth2;

use PSX\Framework\Controller\Tool\Documentation;
use PSX\Framework\Oauth2\GrantTypeFactory;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Oauth2\GrantType\TestAuthorizationCode;
use PSX\Framework\Tests\Oauth2\GrantType\TestClientCredentials;
use PSX\Framework\Tests\Oauth2\GrantType\TestImplicit;
use PSX\Framework\Tests\Oauth2\GrantType\TestPassword;
use PSX\Framework\Tests\Oauth2\GrantType\TestRefreshToken;

/**
 * TokenAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TokenAbstractTest extends ControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $grantTypeFactory = new GrantTypeFactory();
        $grantTypeFactory->add(new TestAuthorizationCode());
        $grantTypeFactory->add(new TestClientCredentials());
        $grantTypeFactory->add(new TestImplicit());
        $grantTypeFactory->add(new TestPassword());
        $grantTypeFactory->add(new TestRefreshToken());

        Environment::getContainer()->set('oauth2_grant_type_factory', $grantTypeFactory);
    }

    public function testDocumentation()
    {
        $response = $this->sendRequest('/doc/*/token', 'GET');

        $actual = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "status": 1,
    "path": "\/token",
    "methods": {
        "POST": {
            "tags": [],
            "request": "OAuth2_Request",
            "responses": {
                "200": "OAuth2_Access_Token",
                "400": "OAuth2_Error"
            }
        }
    },
    "definitions": {
        "OAuth2_Access_Token": {
            "type": "object",
            "properties": {
                "access_token": {
                    "type": "string"
                },
                "token_type": {
                    "type": "string"
                },
                "expires_in": {
                    "type": "string"
                },
                "refresh_token": {
                    "type": "string"
                }
            }
        },
        "OAuth2_Authorization_Code": {
            "type": "object",
            "properties": {
                "grant_type": {
                    "const": "authorization_code",
                    "type": "string"
                },
                "code": {
                    "type": "string"
                },
                "redirect_uri": {
                    "type": "string"
                },
                "client_id": {
                    "type": "string"
                }
            },
            "required": [
                "grant_type",
                "code"
            ]
        },
        "OAuth2_Client_Credentials": {
            "type": "object",
            "properties": {
                "grant_type": {
                    "const": "client_credentials",
                    "type": "string"
                },
                "scope": {
                    "type": "string"
                }
            },
            "required": [
                "grant_type"
            ]
        },
        "OAuth2_Error": {
            "type": "object",
            "properties": {
                "error": {
                    "type": "string"
                },
                "error_description": {
                    "type": "string"
                },
                "error_uri": {
                    "type": "string"
                },
                "state": {
                    "type": "string"
                }
            }
        },
        "OAuth2_Password": {
            "type": "object",
            "properties": {
                "grant_type": {
                    "const": "password",
                    "type": "string"
                },
                "username": {
                    "type": "string"
                },
                "password": {
                    "type": "string"
                },
                "scope": {
                    "type": "string"
                }
            },
            "required": [
                "grant_type",
                "username",
                "password"
            ]
        },
        "OAuth2_Refresh_Token": {
            "type": "object",
            "properties": {
                "grant_type": {
                    "const": "refresh_token",
                    "type": "string"
                },
                "refresh_token": {
                    "type": "string"
                },
                "scope": {
                    "type": "string"
                }
            },
            "required": [
                "grant_type",
                "refresh_token"
            ]
        },
        "OAuth2_Request": {
            "oneOf": [
                {
                    "$ref": "OAuth2_Authorization_Code"
                },
                {
                    "$ref": "OAuth2_Password"
                },
                {
                    "$ref": "OAuth2_Client_Credentials"
                },
                {
                    "$ref": "OAuth2_Refresh_Token"
                }
            ]
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testAuthorizationCodeGrant()
    {
        $response = $this->callEndpoint('foo', 'bar', array(
            'grant_type'   => 'authorization_code',
            'code'         => 'SplxlOBeZQQYbYS6WxSbIA',
            'redirect_uri' => 'https://client.example.com/cb',
        ));

        $expect = <<<JSON
{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

        $body = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testClientCredentialsGrant()
    {
        $response = $this->callEndpoint('foo', 'bar', array(
            'grant_type' => 'client_credentials',
        ));

        $expect = <<<JSON
{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

        $body = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPasswordGrant()
    {
        $response = $this->callEndpoint('foo', 'bar', array(
            'grant_type' => 'password',
            'username'   => 'johndoe',
            'password'   => 'A3ddj3w',
        ));

        $expect = <<<JSON
{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

        $body = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testRefreshTokenGrant()
    {
        $response = $this->callEndpoint('foo', 'bar', array(
            'grant_type'    => 'refresh_token',
            'refresh_token' => 'tGzv3JOkF0XG5Qx2TlKWIA',
        ));

        $expect = <<<JSON
{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

        $body = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testInvalidGrant()
    {
        $response = $this->callEndpoint('foo', 'bar', array(
            'grant_type' => 'foo',
        ));

        $expect = <<<JSON
{
  "error":"server_error",
  "error_description":"/grant_type must match one required schema"
}
JSON;

        $body = (string) $response->getBody();

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testOptionsRequest()
    {
        $response = $this->sendRequest('/token', 'OPTIONS');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['allow' => ['OPTIONS, POST']], $response->getHeaders());
        $this->assertEmpty((string) $response->getBody());
    }

    protected function callEndpoint($clientId, $clientSecret, array $params)
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            'Content-Type'  => 'application/x-www-form-urlencoded'
        ];

        return $this->sendRequest('/token', 'POST', $headers, http_build_query($params, '', '&'));
    }

    protected function getPaths()
    {
        return array(
            [['ANY'], '/token', TestTokenAbstract::class],
            [['ANY'], '/doc/:version/:path', Documentation\DetailController::class],
        );
    }
}
