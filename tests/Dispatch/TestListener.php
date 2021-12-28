<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
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
    private array $called = [];

    public function onControllerExecute(ControllerExecuteEvent $event)
    {
        Assert::assertTrue(is_array($event->getController()));
        Assert::assertInstanceOf(RequestInterface::class, $event->getRequest());
        Assert::assertInstanceOf(ResponseInterface::class, $event->getResponse());

        $this->called[] = 'onControllerExecute';
    }

    public function onControllerProcessed(ControllerProcessedEvent $event)
    {
        Assert::assertTrue(is_array($event->getController()));
        Assert::assertInstanceOf(RequestInterface::class, $event->getRequest());
        Assert::assertInstanceOf(ResponseInterface::class, $event->getResponse());

        $this->called[] = 'onControllerProcessed';
    }

    public function onExceptionThrown(ExceptionThrownEvent $event)
    {
        Assert::assertInstanceOf(\Throwable::class, $event->getException());
        Assert::assertInstanceOf(ContextInterface::class, $event->getContext());

        $this->called[] = 'onExceptionThrown';
    }

    public function onRequestIncoming(RequestIncomingEvent $event)
    {
        Assert::assertInstanceOf(RequestInterface::class, $event->getRequest());

        $this->called[] = 'onRequestIncoming';
    }

    public function onResponseSend(ResponseSendEvent $event)
    {
        Assert::assertInstanceOf(ResponseInterface::class, $event->getResponse());

        $this->called[] = 'onResponseSend';
    }

    public function onRouteMatched(RouteMatchedEvent $event)
    {
        Assert::assertInstanceOf(RequestInterface::class, $event->getRequest());
        Assert::assertInstanceOf(Context::class, $event->getContext());

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
