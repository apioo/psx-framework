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

namespace PSX\Framework\Tests\Filter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\Filter\FilterChain;
use PSX\Http\FilterChainInterface;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\OAuth2\AccessToken;
use PSX\OAuth2\TokenAbstract;
use PSX\Uri\Url;

/**
 * Oauth2AuthenticationTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OAuth2AuthenticationTest extends TestCase
{
    const ACCESS_TOKEN = '2YotnFZFEjr1zCsicMWpAA';

    public function testSuccessful()
    {
        $handle = new OAuth2TestFilter(function (string $accessToken) {
            return $accessToken == self::ACCESS_TOKEN;
        });

        $value = TokenAbstract::factory($this->newAccessToken())->getHeader();

        $request  = new Request(Url::parse('http://localhost/index.php'), 'GET', ['Authorization' => $value]);
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request), $this->equalTo($response));

        $handle->handle($request, $response, $filterChain);
    }

    public function testFailure()
    {
        $this->expectException(UnauthorizedException::class);

        $handle = new OAuth2TestFilter(function (string $accessToken) {
            return false;
        });

        $value = TokenAbstract::factory($this->newAccessToken())->getHeader();

        $request  = new Request(Url::parse('http://localhost/index.php'), 'GET', ['Authorization' => $value]);
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    public function testFailureEmptyCredentials()
    {
        $this->expectException(UnauthorizedException::class);

        $handle = new OAuth2TestFilter(function (string $accessToken) {
            return $accessToken == self::ACCESS_TOKEN;
        });

        $value = '';

        $request  = new Request(Url::parse('http://localhost/index.php'), 'GET', ['Authorization' => $value]);
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    public function testMissing()
    {
        $handle = new OAuth2TestFilter(function (string $accessToken) {
            return $accessToken == self::ACCESS_TOKEN;
        });

        $request  = new Request(Url::parse('http://localhost/index.php'), 'GET');
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        try {
            $handle->handle($request, $response, $filterChain);

            $this->fail('Must throw an Exception');
        } catch (UnauthorizedException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Bearer', $e->getType());
            $this->assertEquals(['realm' => 'psx'], $e->getParameters());
        }
    }

    public function testMissingWrongType()
    {
        $handle = new OAuth2TestFilter(function (string $accessToken) {
            return $accessToken == self::ACCESS_TOKEN;
        });

        $request  = new Request(Url::parse('http://localhost/index.php'), 'GET', ['Authorization' => 'Foo']);
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        try {
            $handle->handle($request, $response, $filterChain);

            $this->fail('Must throw an Exception');
        } catch (UnauthorizedException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Bearer', $e->getType());
            $this->assertEquals(['realm' => 'psx'], $e->getParameters());
        }
    }

    protected function newAccessToken(): AccessToken
    {
        return new AccessToken(
            '2YotnFZFEjr1zCsicMWpAA',
            'bearer',
            3600,
            'tGzv3JOkF0XG5Qx2TlKWIA'
        );
    }

    protected function getMockFilterChain(): FilterChainInterface&MockObject
    {
        return $this->getMockBuilder(FilterChain::class)
            ->setConstructorArgs([[]])
            ->setMethods(['handle'])
            ->getMock();
    }
}
