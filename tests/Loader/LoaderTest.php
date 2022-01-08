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

namespace PSX\Framework\Tests\Loader;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\InvalidPathException;
use PSX\Framework\Loader\Loader;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Framework\Loader\LocationFinder\CallbackMethod;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Dispatch\DummyController;
use PSX\Framework\Tests\Dispatch\TestListener;
use PSX\Framework\Tests\Oauth2\AuthorizationCode\TestCallbackAbstract;
use PSX\Http\FilterChainInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Uri\Uri;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * LoaderTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LoaderTest extends ControllerTestCase
{
    public function testLoadIndexCall()
    {
        ProbeController::clear();

        $testListener = new TestListener();
        $eventDispatcher = Environment::getService('event_dispatcher');
        $eventDispatcher->addSubscriber($testListener);

        $request  = new Request(new Uri('/foobar'), 'GET');
        $response = new Response();

        $this->loadController($request, $response);

        $expect = array(
            'PSX\Framework\Tests\Loader\ProbeController::__construct',
            'PSX\Framework\Tests\Loader\ProbeController::getIterator',
            'PSX\Framework\Tests\Loader\ProbeController::getPreFilter',
            'PSX\Framework\Tests\Loader\ProbeController::getPostFilter',
            'PSX\Framework\Tests\Loader\ProbeController::handle',
            'PSX\Framework\Tests\Loader\ProbeController::doGet',
        );

        $this->assertEquals($expect, ProbeController::getMethodsCalled());

        $expect = array(
            'onRequestIncoming',
            'onRouteMatched',
            'onControllerExecute',
            'onControllerProcessed',
            'onResponseSend',
        );

        $this->assertEquals($expect, $testListener->getCalled());
    }

    public function testLoadDetailCall()
    {
        ProbeController::clear();

        $testListener = new TestListener();
        $eventDispatcher = Environment::getService('event_dispatcher');
        $eventDispatcher->addSubscriber($testListener);

        $request  = new Request(new Uri('/foobar/detail/12'), 'GET');
        $response = new Response();

        $this->loadController($request, $response);

        $expect = array(
            'PSX\Framework\Tests\Loader\ProbeController::__construct',
            'PSX\Framework\Tests\Loader\ProbeController::getIterator',
            'PSX\Framework\Tests\Loader\ProbeController::getPreFilter',
            'PSX\Framework\Tests\Loader\ProbeController::getPostFilter',
            'PSX\Framework\Tests\Loader\ProbeController::handle',
            'PSX\Framework\Tests\Loader\ProbeController::doGet',
        );

        $this->assertEquals($expect, ProbeController::getMethodsCalled());

        $expect = array(
            'onRequestIncoming',
            'onRouteMatched',
            'onControllerExecute',
            'onControllerProcessed',
            'onResponseSend',
        );

        $this->assertEquals($expect, $testListener->getCalled());
    }

    public function testLoadUnknownLocation()
    {
        ProbeController::clear();

        $testListener = new TestListener();
        $eventDispatcher = Environment::getService('event_dispatcher');
        $eventDispatcher->addSubscriber($testListener);

        $request  = new Request(new Uri('/baz'), 'GET');
        $response = new Response();

        $this->loadController($request, $response);

        $expect = array(
        );

        $this->assertEquals($expect, ProbeController::getMethodsCalled());

        $expect = array(
            'onRequestIncoming',
            'onExceptionThrown',
            'onResponseSend',
        );

        $this->assertEquals($expect, $testListener->getCalled());
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/foobar', ProbeController::class],
            [['GET'], '/foobar/detail/12', ProbeController::class],
        );
    }
}
