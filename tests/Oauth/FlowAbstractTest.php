<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Framework\Test\ControllerTestCase;
use PSX\Http\Client as HttpClient;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Callback;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Oauth\Consumer;
use PSX\Uri\Url;

/**
 * Flow which test the complete oauth stack
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FlowAbstractTest extends ControllerTestCase
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
        $testCase = $this;
        $httpClient = new HttpClient(new Callback(function ($request) use ($testCase) {

            $body     = new TempStream(fopen('php://memory', 'r+'));
            $response = new Response();
            $response->setBody($body);

            $testCase->loadController($request, $response);

            return $response;

        }));

        $oauth = new Consumer($httpClient);

        // request token
        $response = $oauth->requestToken(new Url('http://127.0.0.1/request'), self::CONSUMER_KEY, self::CONSUMER_SECRET);

        $this->assertInstanceOf('PSX\Oauth\Data\Response', $response);
        $this->assertEquals(self::TMP_TOKEN, $response->getToken());
        $this->assertEquals(self::TMP_TOKEN_SECRET, $response->getTokenSecret());

        // authorize the user gets redirected and approves the application

        // access token
        $response = $oauth->accessToken(new Url('http://127.0.0.1/access'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TMP_TOKEN, self::TMP_TOKEN_SECRET, self::VERIFIER);

        $this->assertInstanceOf('PSX\Oauth\Data\Response', $response);
        $this->assertEquals(self::TOKEN, $response->getToken());
        $this->assertEquals(self::TOKEN_SECRET, $response->getTokenSecret());

        // api request
        $url      = new Url('http://127.0.0.1/api');
        $auth     = $oauth->getAuthorizationHeader($url, self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET, 'HMAC-SHA1', 'GET');
        $request  = new GetRequest($url, array('Authorization' => $auth));
        $response = $httpClient->request($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SUCCESS', (string) $response->getBody());
    }

    protected function getPaths()
    {
        return array(
            [['POST'], '/request', 'PSX\Framework\Tests\Oauth\TestRequestAbstract'],
            [['POST'], '/access', 'PSX\Framework\Tests\Oauth\TestAccessAbstract'],
            [['GET'], '/api', 'PSX\Framework\Tests\Oauth\TestOauth::doIndex'],
        );
    }
}
