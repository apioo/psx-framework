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

namespace PSX\Framework\Tests\Log;

use Psr\Log\LoggerInterface;
use PSX\Framework\DisplayException;
use PSX\Framework\Event\Context\ControllerContext;
use PSX\Framework\Event\ControllerExecuteEvent;
use PSX\Framework\Event\ControllerProcessedEvent;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Event\RequestIncomingEvent;
use PSX\Framework\Event\ResponseSendEvent;
use PSX\Framework\Event\RouteMatchedEvent;
use PSX\Framework\Loader\Context;
use PSX\Framework\Log\LogListener;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Application\TestApi\IndexController;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\NotFoundException;
use PSX\Http\Exception\SeeOtherException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Uri;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * LogListenerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LogListenerTest extends ControllerTestCase
{
    public function testEventRequestIncomming()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Incoming request GET /foo.htm'));

        $request = new Request(new Uri('/foo.htm'), 'GET');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));
        $eventDispatcher->dispatch(Event::REQUEST_INCOMING, new RequestIncomingEvent($request));
    }

    public function testEventRouteMatched()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Route matched GET /foo.htm'));

        $context = new Context();
        $context->setSource(\stdClass::class);

        $request = new Request(new Uri('/foo.htm'), 'GET');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));
        $eventDispatcher->dispatch(Event::ROUTE_MATCHED, new RouteMatchedEvent($request, $context));
    }

    public function testEventControllerExecute()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Controller execute'));

        $context    = new Context();
        $request    = new Request(new Uri('/foo.htm'), 'GET');
        $response   = new Response();
        $controller = Environment::getService('controller_factory')->getController(IndexController::class, $context);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));
        $eventDispatcher->dispatch(Event::CONTROLLER_EXECUTE, new ControllerExecuteEvent($controller, $request, $response));
    }

    public function testEventControllerProcessed()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Controller processed'));

        $context    = new Context();
        $request    = new Request(new Uri('/foo.htm'), 'GET');
        $response   = new Response();
        $controller = Environment::getService('controller_factory')->getController(IndexController::class, $context);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));
        $eventDispatcher->dispatch(Event::CONTROLLER_PROCESSED, new ControllerProcessedEvent($controller, $request, $response));
    }

    public function testEventResponseSend()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Send response 200'));

        $response = new Response();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));
        $eventDispatcher->dispatch(Event::RESPONSE_SEND, new ResponseSendEvent($response));
    }

    public function testEventResponseWithErrorStatusCode()
    {
        $logger = $this->getLogger();
        $logger->expects($this->at(0))
            ->method('info')
            ->with($this->equalTo('Send response 500'));

        $response = new Response(500);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));
        $eventDispatcher->dispatch(Event::RESPONSE_SEND, new ResponseSendEvent($response));
    }

    public function testEventResponseWithUnknownStatusCode()
    {
        $logger = $this->getLogger();
        $logger->expects($this->at(0))
            ->method('info')
            ->with($this->equalTo('Send response 200'));

        $response = new Response(299);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));
        $eventDispatcher->dispatch(Event::RESPONSE_SEND, new ResponseSendEvent($response));
    }

    public function testEventExceptionThrownDisplayException()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('notice')
            ->with($this->equalTo('foobar'));

        $request  = new Request(new Uri('/foo.htm'), 'GET');
        $response = new Response();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));

        try {
            throw new DisplayException('foobar');
        } catch (DisplayException $e) {
            $eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
        }
    }

    public function testEventExceptionThrownException()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('error')
            ->with($this->equalTo('foobar'));

        $request  = new Request(new Uri('/foo.htm'), 'GET');
        $response = new Response();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));

        try {
            throw new \Exception('foobar');
        } catch (\Exception $e) {
            $eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
        }
    }

    public function testEventExceptionThrownStatusCodeExceptionClientError()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('notice')
            ->with($this->equalTo('foobar'));

        $request  = new Request(new Uri('/foo.htm'), 'GET');
        $response = new Response();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));

        try {
            throw new NotFoundException('foobar');
        } catch (NotFoundException $e) {
            $eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
        }
    }

    public function testEventExceptionThrownStatusCodeExceptionServerError()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('error')
            ->with($this->equalTo('foobar'));

        $request  = new Request(new Uri('/foo.htm'), 'GET');
        $response = new Response();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));

        try {
            throw new InternalServerErrorException('foobar');
        } catch (InternalServerErrorException $e) {
            $eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
        }
    }

    public function testEventExceptionThrownStatusCodeExceptionSeeOther()
    {
        $logger = $this->getLogger();
        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo('Redirect exception'));

        $request  = new Request(new Uri('/foo.htm'), 'GET');
        $response = new Response();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LogListener($logger));

        try {
            throw new SeeOtherException('/bar.htm');
        } catch (SeeOtherException $e) {
            $eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
        }
    }

    protected function getLogger()
    {
        return $this->createMock(LoggerInterface::class, ['emergency', 'alert', 'critical', 'warning', 'debug', 'log', 'info', 'notice', 'error']);
    }

    protected function getPaths()
    {
        return array();
    }
}
