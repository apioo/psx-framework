<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * CallbackAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class CallbackControllerTest extends ControllerTestCase
{
    public function testCallback()
    {
        $response = $this->sendRequest('/authorization/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=xyz', 'GET');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SUCCESS', (string) $response->getBody());
    }

    public function testErrorCallback()
    {
        $errors = array(
            'invalid_request'           => \PSX\OAuth2\Exception\InvalidRequestException::class,
            'unauthorized_client'       => \PSX\OAuth2\Exception\UnauthorizedClientException::class,
            'access_denied'             => \PSX\OAuth2\Exception\AccessDeniedException::class,
            'unsupported_response_type' => \PSX\OAuth2\Exception\UnsupportedResponseTypeException::class,
            'invalid_scope'             => \PSX\OAuth2\Exception\InvalidScopeException::class,
            'server_error'              => \PSX\OAuth2\Exception\ServerErrorException::class,
            'temporarily_unavailable'   => \PSX\OAuth2\Exception\TemporarilyUnavailableException::class,
        );

        foreach ($errors as $error => $exceptionType) {
            $response = $this->sendRequest('/authorization/callback?error=' . $error . '&error_description=foobar', 'GET');

            $this->assertEquals(500, $response->getStatusCode());
            $this->assertEquals($exceptionType, (string) $response->getBody());
        }
    }
}
