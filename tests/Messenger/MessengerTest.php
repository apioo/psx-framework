<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Messenger;

use Psr\EventDispatcher\EventDispatcherInterface;
use PSX\Framework\Messenger\DefaultTransport;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;

/**
 * MessengerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MessengerTest extends ControllerTestCase
{
    public function testSend()
    {
        $messageBus = Environment::getService(MessageBusInterface::class);
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = Environment::getService(EventDispatcherInterface::class);

        $message = new TestMessage('foobar');
        $messageBus->dispatch($message);

        $transport = Environment::getService(TransportInterface::class);
        if (!$transport instanceof InMemoryTransport) {
            $this->fail('In memory transport is required for testing');
        }

        $envelopes = $transport->getSent();
        $this->assertCount(1, $envelopes);
        $this->assertCount(0, $transport->getAcknowledged());

        $envelope = $envelopes[0] ?? null;
        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertInstanceOf(TestMessage::class, $envelope->getMessage());

        $eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(1));

        $worker = new Worker([DefaultTransport::NAME => $transport], $messageBus, $eventDispatcher);
        $worker->run([]);

        $this->assertCount(1, $transport->getAcknowledged());
    }
}
