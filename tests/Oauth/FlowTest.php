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

namespace PSX\Framework\Tests\Oauth;

use Psr\Http\Message\RequestInterface;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Http\Client\Client;
use PSX\Http\Client\GetRequest;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Oauth\Consumer;
use PSX\Oauth\Data;
use PSX\Uri\Url;

/**
 * Flow which test the complete oauth stack
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FlowTest extends ControllerTestCase
{
    const CONSUMER_KEY      = 'dpf43f3p2l4k3l03';
    const CONSUMER_SECRET   = 'kd94hf93k423kf44';

    const TMP_TOKEN         = 'hh5s93j4hdidpola';
    const TMP_TOKEN_SECRET  = 'hdhd0244k9j7ao03';
    const VERIFIER          = 'hfdp7dh39dks9884';
    const TOKEN             = 'nnch734d00sl2jdk';
    const TOKEN_SECRET      = 'pfkkdhi9sl3r4s00';

    public function testFlow()
    {
        $proxy = function(RequestInterface $request, array $options){
            $request  = new Request($request->getUri(), $request->getMethod(), $request->getHeaders(), $request->getBody());
            $response = new Response();

            $this->loadController($request, $response);

            return $response;
        };

        $client = new Client(['handler' => $proxy]);
        $oauth  = new Consumer($client);

        // request token
        $response = $oauth->requestToken(new Url('http://127.0.0.1/request'), self::CONSUMER_KEY, self::CONSUMER_SECRET);

        $this->assertInstanceOf(Data\Response::class, $response);
        $this->assertEquals(self::TMP_TOKEN, $response->getToken());
        $this->assertEquals(self::TMP_TOKEN_SECRET, $response->getTokenSecret());

        // authorize the user gets redirected and approves the application

        // access token
        $response = $oauth->accessToken(new Url('http://127.0.0.1/access'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TMP_TOKEN, self::TMP_TOKEN_SECRET, self::VERIFIER);

        $this->assertInstanceOf(Data\Response::class, $response);
        $this->assertEquals(self::TOKEN, $response->getToken());
        $this->assertEquals(self::TOKEN_SECRET, $response->getTokenSecret());

        // api request
        $url      = new Url('http://127.0.0.1/api');
        $auth     = $oauth->getAuthorizationHeader($url, self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET, 'HMAC-SHA1', 'GET');
        $request  = new GetRequest($url, array('Authorization' => $auth));
        $response = $client->request($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SUCCESS', (string) $response->getBody());
    }

    public function testFlowInvalid()
    {
        $proxy = function(RequestInterface $request, array $options){
            $request  = new Request($request->getUri(), $request->getMethod(), $request->getHeaders(), $request->getBody());
            $response = new Response();

            $this->loadController($request, $response);

            return $response;
        };

        $client = new Client(['handler' => $proxy]);
        $oauth  = new Consumer($client);

        // request token
        $response = $oauth->requestToken(new Url('http://127.0.0.1/request'), self::CONSUMER_KEY, self::CONSUMER_SECRET);

        $this->assertInstanceOf(Data\Response::class, $response);
        $this->assertEquals(self::TMP_TOKEN, $response->getToken());
        $this->assertEquals(self::TMP_TOKEN_SECRET, $response->getTokenSecret());

        // authorize the user gets redirected and approves the application

        // access token
        $response = $oauth->accessToken(new Url('http://127.0.0.1/access'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TMP_TOKEN, self::TMP_TOKEN_SECRET, self::VERIFIER);

        $this->assertInstanceOf(Data\Response::class, $response);
        $this->assertEquals(self::TOKEN, $response->getToken());
        $this->assertEquals(self::TOKEN_SECRET, $response->getTokenSecret());

        // api request
        $url      = new Url('http://127.0.0.1/api');
        $auth     = $oauth->getAuthorizationHeader($url, self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, 'foobar', 'HMAC-SHA1', 'GET');
        $request  = new GetRequest($url, array('Authorization' => $auth));
        $response = $client->request($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Invalid consumer key or signature', (string) $response->getBody());
    }

    protected function getPaths()
    {
        return array(
            [['POST'], '/request', TestRequestAbstract::class],
            [['POST'], '/access', TestAccessAbstract::class],
            [['GET'], '/api', TestApi::class],
        );
    }
}
