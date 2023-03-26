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
use PSX\Framework\Tests\OAuth2\TestAuthorizer;
use PSX\Json\Parser;

/**
 * AuthorizationControllerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class AuthorizationControllerTest extends ControllerTestCase
{
    const CLIENT_ID     = 's6BhdRkqt3';
    const CLIENT_SECRET = 'gX1fBat3bV';

    public function testHandleCodeGrant()
    {
        TestAuthorizer::setHasGrant(true);
        TestAuthorizer::setCode('foobar');

        $response = $this->callEndpoint(array(
            'response_type' => 'code',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://foo.com?code=foobar&state=random', $response->getHeader('Location'));
    }

    public function testHandleCodeNoGrant()
    {
        TestAuthorizer::setHasGrant(false);
        TestAuthorizer::setCode('foobar');

        $response = $this->callEndpoint(array(
            'response_type' => 'code',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://foo.com?error=unauthorized_client&error_description=Client+is+not+authenticated', $response->getHeader('Location'));
    }

    public function testHandleTokenGrant()
    {
        TestAuthorizer::setHasGrant(true);
        TestAuthorizer::setCode('foobar');

        $response = $this->callEndpoint(array(
            'response_type' => 'token',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        // we have removed support for the implicit grant which is deprecated
        $this->assertEquals('http://foo.com?error=unsupported_response_type&error_description=Invalid+response+type', $response->getHeader('Location'));
    }

    public function testHandleTokenNoGrant()
    {
        TestAuthorizer::setHasGrant(false);
        TestAuthorizer::setCode('foobar');

        $response = $this->callEndpoint(array(
            'response_type' => 'token',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://foo.com?error=unauthorized_client&error_description=Client+is+not+authenticated', $response->getHeader('Location'));
    }

    public function testHandleNoParameter()
    {
        $response = $this->callEndpoint(array());
        $data     = Parser::decode((string) $response->getBody(), true);

        $this->assertEquals(false, $data['success']);
        $this->assertEquals('PSX\OAuth2\Exception\InvalidRequestException', $data['title']);
    }

    protected function callEndpoint(array $params)
    {
        return $this->sendRequest('/authorization/authorize?' . http_build_query($params, '', '&'), 'GET');
    }
}
