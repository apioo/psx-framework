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

namespace PSX\Framework\OAuth2;

use GuzzleHttp\HandlerStack;
use PSX\Http\Client\Client;
use PSX\OAuth2\AccessToken;
use PSX\OAuth2\Authorization\AuthorizationCode;
use PSX\Uri\Url;
use Throwable;

/**
 * VoidCallback
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class VoidCallback implements CallbackInterface
{
    public function getAuthorizationCode(string $code, string $state): AuthorizationCode
    {
        // TODO: Implement getAuthorizationCode() method.

        $stack = HandlerStack::create();

        $client = new Client(['handler' => $stack]);
        $oauth  = new AuthorizationCode($client, Url::parse('http://127.0.0.1/api'));
        $oauth->setClientPassword('s6BhdRkqt3', 'gX1fBat3bV');

        return $oauth;
    }

    public function onAccessToken(AccessToken $accessToken): mixed
    {
        return null;
    }

    public function onError(Throwable $e): mixed
    {
        return null;
    }
}
