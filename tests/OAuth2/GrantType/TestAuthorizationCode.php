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

namespace PSX\Framework\Tests\OAuth2\GrantType;

use PSX\Framework\OAuth2\Credentials;
use PSX\Framework\OAuth2\GrantType\AuthorizationCodeAbstract;
use PSX\OAuth2\AccessToken;
use PSX\OAuth2\Grant\AuthorizationCode;

/**
 * TestAuthorizationCode
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TestAuthorizationCode extends AuthorizationCodeAbstract
{
    protected function generate(Credentials $credentials, AuthorizationCode $grant): AccessToken
    {
        return new AccessToken(
            '2YotnFZFEjr1zCsicMWpAA',
            'example',
            3600,
            'tGzv3JOkF0XG5Qx2TlKWIA'
        );
    }
}
