<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\OAuth2;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use PSX\Framework\OAuth2\CallbackInterface;
use PSX\Framework\Tests\Controller\OAuth2\AuthorizationControllerTest;
use PSX\Http\Client\Client;
use PSX\Http\Environment\HttpResponse;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Authorization\AuthorizationCode;
use PSX\Uri\Url;

/**
 * TestCallback
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestCallback implements CallbackInterface
{
    public function getAuthorizationCode(string $code, string $state): AuthorizationCode
    {
        $payload = <<<JSON
{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA",
  "example_parameter":"example_value"
}
JSON;

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $payload),
        ]);

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $client = new Client(['handler' => $stack]);
        $oauth  = new AuthorizationCode($client, new Url('http://127.0.0.1/api'));
        $oauth->setClientPassword(AuthorizationControllerTest::CLIENT_ID, AuthorizationControllerTest::CLIENT_SECRET);

        return $oauth;
    }

    public function onAccessToken(AccessToken $accessToken): mixed
    {
        Assert::assertEquals('2YotnFZFEjr1zCsicMWpAA', $accessToken->getAccessToken());
        Assert::assertEquals('example', $accessToken->getTokenType());
        Assert::assertEquals(3600, $accessToken->getExpiresIn());
        Assert::assertEquals('tGzv3JOkF0XG5Qx2TlKWIA', $accessToken->getRefreshToken());

        return new HttpResponse(200, [], 'SUCCESS');
    }

    public function onError(\Throwable $e): mixed
    {
        return new HttpResponse(500, [], get_class($e));
    }
}