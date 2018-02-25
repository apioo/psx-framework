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

namespace PSX\Framework\Tests\Dispatch;

use PSX\Framework\Event\ContextInterface;
use PSX\Framework\Event\ControllerExecuteEvent;
use PSX\Framework\Event\ControllerProcessedEvent;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Event\RequestIncomingEvent;
use PSX\Framework\Event\ResponseSendEvent;
use PSX\Framework\Event\RouteMatchedEvent;
use PSX\Framework\Loader\Context;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * TestListener
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $called = [];

    /**
     * @var \PHPUnit_Framework_TestCase
     */
    private $testCase;

    public function __construct(\PHPUnit_Framework_TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * @param \PSX\Framework\Event\ControllerExecuteEvent $event
     */
    public function onControllerExecute(ControllerExecuteEvent $event)
    {
        $this->testCase->assertTrue(is_array($event->getController()));
        $this->testCase->assertInstanceOf(RequestInterface::class, $event->getRequest());
        $this->testCase->assertInstanceOf(ResponseInterface::class, $event->getResponse());

        $this->called[] = 'onControllerExecute';
    }

    /**
     * @param \PSX\Framework\Event\ControllerProcessedEvent $event
     */
    public function onControllerProcessed(ControllerProcessedEvent $event)
    {
        $this->testCase->assertTrue(is_array($event->getController()));
        $this->testCase->assertInstanceOf(RequestInterface::class, $event->getRequest());
        $this->testCase->assertInstanceOf(ResponseInterface::class, $event->getResponse());

        $this->called[] = 'onControllerProcessed';
    }

    /**
     * @param \PSX\Framework\Event\ExceptionThrownEvent $event
     */
    public function onExceptionThrown(ExceptionThrownEvent $event)
    {
        $this->testCase->assertInstanceOf(\Throwable::class, $event->getException());
        $this->testCase->assertInstanceOf(ContextInterface::class, $event->getContext());

        $this->called[] = 'onExceptionThrown';
    }

    /**
     * @param \PSX\Framework\Event\RequestIncomingEvent $event
     */
    public function onRequestIncoming(RequestIncomingEvent $event)
    {
        $this->testCase->assertInstanceOf(RequestInterface::class, $event->getRequest());

        $this->called[] = 'onRequestIncoming';
    }

    /**
     * @param \PSX\Framework\Event\ResponseSendEvent $event
     */
    public function onResponseSend(ResponseSendEvent $event)
    {
        $this->testCase->assertInstanceOf(ResponseInterface::class, $event->getResponse());

        $this->called[] = 'onResponseSend';
    }

    /**
     * @param \PSX\Framework\Event\RouteMatchedEvent $event
     */
    public function onRouteMatched(RouteMatchedEvent $event)
    {
        $this->testCase->assertInstanceOf(RequestInterface::class, $event->getRequest());
        $this->testCase->assertInstanceOf(Context::class, $event->getContext());

        $this->called[] = 'onRouteMatched';
    }

    public function getCalled()
    {
        return $this->called;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event::CONTROLLER_EXECUTE   => 'onControllerExecute',
            Event::CONTROLLER_PROCESSED => 'onControllerProcessed',
            Event::EXCEPTION_THROWN     => 'onExceptionThrown',
            Event::REQUEST_INCOMING     => 'onRequestIncoming',
            Event::RESPONSE_SEND        => 'onResponseSend',
            Event::ROUTE_MATCHED        => 'onRouteMatched',
        ];
    }
}
