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

namespace PSX\Framework\Tests\Loader;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\Loader;
use PSX\Framework\Loader\LocationFinder\CallbackMethod;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Dispatch\TestListener;
use PSX\Http\FilterChainInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Uri\Uri;
use PSX\Uri\Url;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * LoaderTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadIndexCall()
    {
        $controller = new ProbeController();

        $locationFinder = new CallbackMethod(function (RequestInterface $request, Context $context) use ($controller) {
            $this->assertEquals('/foobar', $request->getUri()->getPath());

            $context->setSource($controller);

            return $request;
        });

        $eventDispatcher = new EventDispatcher();

        $testListener = new TestListener($this);
        $eventDispatcher->addSubscriber($testListener);

        $request  = new Request(new Uri('/foobar'), 'GET');
        $response = new Response();

        $loader = $this->newLoader($locationFinder, $eventDispatcher);
        $loader->load($request, $response);

        $expect = array(
            'PSX\Framework\Tests\Loader\ProbeController::__construct',
            'PSX\Framework\Tests\Loader\ProbeController::getIterator',
            'PSX\Framework\Tests\Loader\ProbeController::getPreFilter',
            'PSX\Framework\Tests\Loader\ProbeController::getPostFilter',
            'PSX\Framework\Tests\Loader\ProbeController::handle',
            'PSX\Framework\Tests\Loader\ProbeController::onLoad',
            'PSX\Framework\Tests\Loader\ProbeController::onRequest',
            'PSX\Framework\Tests\Loader\ProbeController::onGet',
            'PSX\Framework\Tests\Loader\ProbeController::onFinish',
        );

        $this->assertEquals($expect, $controller->getMethodsCalled());

        $expect = array(
            'onRouteMatched',
            'onControllerExecute',
            'onControllerProcessed',
        );

        $this->assertEquals($expect, $testListener->getCalled());
    }

    public function testLoadDetailCall()
    {
        $controller = new ProbeController();

        $locationFinder = new CallbackMethod(function (RequestInterface $request, Context $context) use ($controller) {
            $this->assertEquals('/foobar/detail/12', $request->getUri()->getPath());

            $context->setSource($controller);
            $context->setParameters(['id' => 12]);

            return $request;
        });

        $eventDispatcher = new EventDispatcher();

        $testListener = new TestListener($this);
        $eventDispatcher->addSubscriber($testListener);

        $request  = new Request(new Uri('/foobar/detail/12'), 'GET');
        $response = new Response();

        $loader = $this->newLoader($locationFinder, $eventDispatcher);
        $loader->load($request, $response);

        $expect = array(
            'PSX\Framework\Tests\Loader\ProbeController::__construct',
            'PSX\Framework\Tests\Loader\ProbeController::getIterator',
            'PSX\Framework\Tests\Loader\ProbeController::getPreFilter',
            'PSX\Framework\Tests\Loader\ProbeController::getPostFilter',
            'PSX\Framework\Tests\Loader\ProbeController::handle',
            'PSX\Framework\Tests\Loader\ProbeController::onLoad',
            'PSX\Framework\Tests\Loader\ProbeController::onRequest',
            'PSX\Framework\Tests\Loader\ProbeController::onGet',
            'PSX\Framework\Tests\Loader\ProbeController::onFinish',
        );

        $this->assertEquals($expect, $controller->getMethodsCalled());

        $expect = array(
            'onRouteMatched',
            'onControllerExecute',
            'onControllerProcessed',
        );

        $this->assertEquals($expect, $testListener->getCalled());
    }

    /**
     * @expectedException \PSX\Framework\Loader\InvalidPathException
     */
    public function testLoadUnknownLocation()
    {
        $locationFinder = new CallbackMethod(function (RequestInterface $request, Context $context) {
            return null;
        });

        $eventDispatcher = new EventDispatcher();

        $testListener = new TestListener($this);
        $eventDispatcher->addSubscriber($testListener);

        $loader   = $this->newLoader($locationFinder, $eventDispatcher);
        $request  = new Request(new Uri('/foobar'), 'GET');
        $response = new Response();

        $loader->load($request, $response);
    }

    public function testFilter()
    {
        $request  = new Request(new Uri('/foobar'), 'GET');
        $response = new Response();
        $context  = new Context();

        $locationFinder = new CallbackMethod(function (RequestInterface $request, Context $context) {
            $context->setSource(FilterController::class);

            return $request;
        });

        $eventDispatcher = new EventDispatcher();

        $loader = $this->newLoader($locationFinder, $eventDispatcher);
        $loader->load($request, $response, $context);

        $this->assertSame(true, $request->getAttribute('pre_filter'));
        $this->assertSame(true, $request->getAttribute('post_filter'));
    }

    public function testGlobalFilter()
    {
        $request  = new Request(new Uri('/foobar'), 'GET');
        $response = new Response();
        $context  = new Context();

        $locationFinder = new CallbackMethod(function (RequestInterface $request, Context $context) {
            $context->setSource(FilterController::class);

            return $request;
        });

        $config = Environment::getService('config');
        $config->set('psx_filter_pre', [function(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain){
            $request->setAttribute('global_pre_filter', true);

            $filterChain->handle($request, $response);
        }]);
        $config->set('psx_filter_post', [function(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain){
            $request->setAttribute('global_post_filter', true);

            $filterChain->handle($request, $response);
        }]);

        $eventDispatcher = new EventDispatcher();

        $loader = $this->newLoader($locationFinder, $eventDispatcher);
        $loader->load($request, $response, $context);

        $this->assertSame(true, $request->getAttribute('global_pre_filter'));
        $this->assertSame(true, $request->getAttribute('global_post_filter'));
        $this->assertSame(true, $request->getAttribute('pre_filter'));
        $this->assertSame(true, $request->getAttribute('post_filter'));
    }

    /**
     * @param \PSX\Framework\Loader\LocationFinderInterface $locationFinder
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @return \PSX\Framework\Loader\LoaderInterface
     */
    private function newLoader(LocationFinderInterface $locationFinder, EventDispatcherInterface $eventDispatcher)
    {
        return new Loader(
            $locationFinder,
            Environment::getService('controller_factory'),
            $eventDispatcher,
            new Logger('psx', [new NullHandler()]),
            Environment::getService('config')
        );
    }
}
