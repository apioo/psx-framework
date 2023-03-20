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

namespace PSX\Framework\Tests\Dispatch;

use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Event\RequestIncomingEvent;
use PSX\Framework\Event\ResponseSendEvent;
use PSX\Framework\Loader\Context;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\TempStream;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * DispatchTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DispatchTest extends ControllerTestCase
{
    public function testRoute()
    {
        $response = $this->sendRequest('/tests/dummy', 'GET');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo', (string) $response->getBody());
    }

    public function testRouteException()
    {
        $response = $this->sendRequest('/tests/exception', 'GET');

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @dataProvider statusCodeProvider
     */
    public function testStatusException($code)
    {
        $response = $this->sendRequest('/tests/status/' . $code, 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals($code, $response->getStatusCode());

        if (in_array($code, [301, 302, 303, 307])) {
            $this->assertEquals('http://google.com', $response->getHeader('Location'));
            $this->assertEquals('', $body);
        } elseif ($code == 401) {
            $this->assertEquals('Basic realm="foo"', $response->getHeader('WWW-Authenticate'));
        } elseif ($code == 405) {
            $this->assertEquals('GET, POST', $response->getHeader('Allow'));
        }
    }

    public function statusCodeProvider()
    {
        return [
            [301],
            [302],
            [303],
            [304],
            [307],
            [400],
            [401],
            [403],
            [404],
            [405],
            [406],
            [409],
            [410],
            [415],
            [500],
            [501],
            [503],
        ];
    }
}
