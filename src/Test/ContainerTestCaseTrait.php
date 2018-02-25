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

namespace PSX\Framework\Test;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\VoidCache;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PSX\Cache\Pool;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Loader;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Schema\SchemaManager;
use PSX\Uri\Uri;

/**
 * ContainerTestCaseTrait
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait ContainerTestCaseTrait
{
    protected $_protectedServices = array('config', 'connection');

    protected function setUp()
    {
        parent::setUp();

        $this->clearServices();

        // set void logger
        $logger = new Logger('psx');
        $logger->pushHandler(new NullHandler());

        Environment::getContainer()->set('logger', $logger);

        // we replace the routing parser and location finder so that the test
        // cases work with the routes deinfed in getPaths
        $this->setUpRoutes();

        // assign the phpunit test case
        Environment::getContainer()->set('test_case', $this);

        // use null cache
        Environment::getContainer()->set('cache', new Pool(new ArrayCache()));

        // schema manager use void cache
        Environment::getContainer()->set('schema_manager', new SchemaManager(
            Environment::getContainer()->get('annotation_reader'),
            new Pool(new VoidCache()),
            true
        ));

        // add event listener which redirects PHPUnit exceptions. Because of
        // this we can make assertions inside an controller
        $eventDispatcher = Environment::getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(Event::EXCEPTION_THROWN, function (ExceptionThrownEvent $event) {

            if ($event->getException() instanceof \PHPUnit_Framework_Exception) {
                throw $event->getException();
            }

        });
    }

    /**
     * Sets the routing parser and location finder so that the tests uses the
     * provided routes
     */
    protected function setUpRoutes()
    {
        $paths = $this->getPaths();
        if (!empty($paths)) {
            Environment::getContainer()->set('routing_parser', new Loader\RoutingParser\ArrayCollection($paths));
            Environment::getContainer()->set('loader_location_finder', new Loader\LocationFinder\RoutingParser(Environment::getContainer()->get('routing_parser')));
        }
    }

    protected function tearDown()
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
    protected function clearServices()
    {
        // set original config
        Environment::getContainer()->set('config', Environment::getConfig());

        // remove services
        $serviceIds = Environment::getContainer()->getServiceIds();
        foreach ($serviceIds as $serviceId) {
            if (!in_array($serviceId, $this->_protectedServices)) {
                Environment::getContainer()->set($serviceId, null);
            }
        }
    }

    /**
     * Provides an array of available routes for this test case. Uses the system
     * routes if no paths are available
     *
     * @return array|null
     */
    protected function getPaths()
    {
        return null;
    }

    /**
     * Loads an specific controller
     *
     * @param \PSX\Http\Request $request
     * @param \PSX\Http\Response $response
     * @return \PSX\Http\ResponseInterface
     */
    protected function loadController(Request $request, Response $response)
    {
        return Environment::getService('dispatch')->route($request, $response);
    }

    /**
     * Sends an request to the system and returns the http response
     *
     * @param string|\PSX\Uri\Uri $url
     * @param string $method
     * @param array $headers
     * @param string $body
     * @return \PSX\Http\ResponseInterface
     */
    protected function sendRequest($uri, $method, $headers = array(), $body = null)
    {
        $request  = new Request(is_string($uri) ? new Uri($uri) : $uri, $method, $headers, $body);
        $response = new Response();
        $response->setBody(new TempStream(fopen('php://memory', 'r+')));

        $this->loadController($request, $response);

        return $response;
    }
}
