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

namespace PSX\Framework\Tests\Oauth2\AuthorizationCode;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Oauth2\Authorization\Exception;

/**
 * CallbackAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CallbackAbstractTest extends ControllerTestCase
{
    public function testCallback()
    {
        $response = $this->sendRequest('/cb?code=SplxlOBeZQQYbYS6WxSbIA&state=xyz', 'GET');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SUCCESS', (string) $response->getBody());
    }

    public function testErrorCallback()
    {
        $errors = array(
            'invalid_request'           => Exception\InvalidRequestException::class,
            'unauthorized_client'       => Exception\UnauthorizedClientException::class,
            'access_denied'             => Exception\AccessDeniedException::class,
            'unsupported_response_type' => Exception\UnsupportedResponseTypeException::class,
            'invalid_scope'             => Exception\InvalidScopeException::class,
            'server_error'              => Exception\ServerErrorException::class,
            'temporarily_unavailable'   => Exception\TemporarilyUnavailableException::class,
        );

        foreach ($errors as $error => $exceptionType) {
            $response = $this->sendRequest('/cb?error=' . $error . '&error_description=foobar', 'GET');

            $this->assertEquals(500, $response->getStatusCode());
            $this->assertEquals($exceptionType, (string) $response->getBody());
        }
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/cb', TestCallbackAbstract::class],
        );
    }
}
