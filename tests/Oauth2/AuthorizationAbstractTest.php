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

namespace PSX\Framework\Tests\Oauth2;

use PSX\Framework\Oauth2\GrantTypeFactory;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Oauth2\GrantType\TestImplicit;
use PSX\Json\Parser;

/**
 * AuthorizationAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AuthorizationAbstractTest extends ControllerTestCase
{
    const CLIENT_ID     = 's6BhdRkqt3';
    const CLIENT_SECRET = 'gX1fBat3bV';

    protected function setUp()
    {
        parent::setUp();

        $grantTypeFactory = new GrantTypeFactory();
        $grantTypeFactory->add(new TestImplicit());

        Environment::getContainer()->set('oauth2_grant_type_factory', $grantTypeFactory);
    }

    public function testHandleCodeGrant()
    {
        $response = $this->callEndpoint(array(
            'response_type' => 'code',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',

            // test implementation specific parameters
            'has_grant' => 1,
            'code' => 'foobar',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://foo.com?code=foobar&state=random', $response->getHeader('Location'));
    }

    public function testHandleCodeNoGrant()
    {
        $response = $this->callEndpoint(array(
            'response_type' => 'code',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',

            // test implementation specific parameters
            'has_grant' => 0,
            'code' => 'foobar',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://foo.com?error=unauthorized_client&error_description=Client+is+not+authenticated', $response->getHeader('Location'));
    }

    public function testHandleTokenGrant()
    {
        $response = $this->callEndpoint(array(
            'response_type' => 'token',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',

            // test implementation specific parameters
            'has_grant' => 1,
            'code' => 'foobar',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://foo.com#access_token=2YotnFZFEjr1zCsicMWpAA&token_type=example&state=random', $response->getHeader('Location'));
    }

    public function testHandleTokenNoGrant()
    {
        $response = $this->callEndpoint(array(
            'response_type' => 'token',
            'client_id' => 'foo',
            'redirect_uri' => 'http://foo.com',
            'scope' => '',
            'state' => 'random',

            // test implementation specific parameters
            'has_grant' => 0,
            'code' => 'foobar',
        ));

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://foo.com?error=unauthorized_client&error_description=Client+is+not+authenticated', $response->getHeader('Location'));
    }

    public function testHandleNoParameter()
    {
        $response = $this->callEndpoint(array());
        $data     = Parser::decode((string) $response->getBody(), true);

        $this->assertEquals(false, $data['success']);
        $this->assertEquals('PSX\Oauth2\Authorization\Exception\InvalidRequestException', $data['title']);
    }

    protected function callEndpoint(array $params)
    {
        return $this->sendRequest('http://127.0.0.1/auth?' . http_build_query($params, '', '&'), 'GET');
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/auth', 'PSX\Framework\Tests\Oauth2\TestAuthorizationAbstract'],
        );
    }
}
