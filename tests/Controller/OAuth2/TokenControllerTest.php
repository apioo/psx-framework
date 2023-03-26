<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Controller\OAuth2;

use PSX\Framework\Test\ControllerTestCase;

/**
 * TokenAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TokenControllerTest extends ControllerTestCase
{
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
  "error": "server_error",
  "error_description": "Provided an invalid grant type"
}
JSON;

        $body = (string) $response->getBody();

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testOptionsRequest()
    {
        $response = $this->sendRequest('/authorization/token', 'OPTIONS');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }

    protected function callEndpoint($clientId, $clientSecret, array $params)
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            'Content-Type'  => 'application/x-www-form-urlencoded'
        ];

        return $this->sendRequest('/authorization/token', 'POST', $headers, http_build_query($params, '', '&'));
    }
}
