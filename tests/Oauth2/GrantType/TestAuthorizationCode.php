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

namespace PSX\Framework\Tests\Oauth2\GrantType;

use PSX\Framework\Oauth2\Credentials;
use PSX\Framework\Oauth2\GrantType\AuthorizationCodeAbstract;
use PSX\Oauth2\AccessToken;

/**
 * TestAuthorizationCode
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestAuthorizationCode extends AuthorizationCodeAbstract
{
    protected function generate(Credentials $credentials, $code, $redirectUri, $clientId)
    {
        $accessToken = new AccessToken();
        $accessToken->setAccessToken('2YotnFZFEjr1zCsicMWpAA');
        $accessToken->setTokenType('example');
        $accessToken->setExpiresIn(3600);
        $accessToken->setRefreshToken('tGzv3JOkF0XG5Qx2TlKWIA');

        return $accessToken;
    }
}
