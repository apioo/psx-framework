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

namespace PSX\Framework\Test;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Loader;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\Stream;
use PSX\Uri\Uri;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * ContainerTestCaseTrait
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait ContainerTestCaseTrait
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->clearServices();

        // set void logger
        $logger = new Logger('psx');
        $logger->pushHandler(new NullHandler());

        Environment::getContainer()->set(LoggerInterface::class, $logger);

        // we replace the routing parser and location finder so that the test
        // cases work with the routes defined in getPaths
        $this->setUpRoutes();

        // assign the phpunit test case
        Environment::getContainer()->set('test_case', $this);

        // use null cache
        Environment::getContainer()->set(CacheItemPoolInterface::class, new ArrayAdapter());

        // add event listener which redirects PHPUnit exceptions. Because of
        // this we can make assertions inside an controller
        $eventDispatcher = Environment::getContainer()->get(EventDispatcherInterface::class);
        $eventDispatcher->addListener(Event::EXCEPTION_THROWN, function (ExceptionThrownEvent $event) {
            if ($event->getException() instanceof Exception) {
                throw $event->getException();
            }
        });
    }

    /**
     * Sets the routing parser and location finder so that the tests uses the provided routes
     */
    protected function setUpRoutes(): void
    {
        $paths = $this->getPaths();
        if (!empty($paths)) {
            Environment::getContainer()->set(Loader\RoutingParserInterface::class, new Loader\RoutingParser\ArrayCollection($paths));
            Environment::getContainer()->set(Loader\LocationFinderInterface::class, new Loader\LocationFinder\RoutingParser(Environment::getContainer()->get(Loader\RoutingParserInterface::class)));
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->clearServices();
    }

    /**
     * Removes all used services so that a new instance of a service gets
     * created. This ensures that our test has no side effects. This behaviour
     * is for some services unwanted like i.e. the db connection since we dont
     * want to re-establish a db connection for every test. Such services can be
     * listed in the _protectedServices property
     */
    protected function clearServices(): void
    {
    }

    /**
     * Provides an array of available routes for this test case. Uses the system routes if no paths are available
     */
    protected function getPaths(): array
    {
        return [];
    }

    /**
     * Loads a specific controller
     */
    protected function loadController(Request $request, Response $response): ResponseInterface
    {
        return Environment::getService('dispatch')->route($request, $response);
    }

    /**
     * Sends a request to the system and returns the http response
     */
    protected function sendRequest(string|Uri $uri, string $method, array $headers = [], ?string $body = null): ResponseInterface
    {
        $request  = new Request(is_string($uri) ? new Uri($uri) : $uri, $method, $headers, $body);
        $response = new Response();
        $response->setBody(new Stream(fopen('php://memory', 'r+')));

        $this->loadController($request, $response);

        return $response;
    }
}
