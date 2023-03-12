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

namespace PSX\Framework\Event;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use PSX\Framework\Config\Config;
use PSX\Framework\Log\LogListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * EventDispatcherFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class EventDispatcherFactory
{
    private iterable $eventSubscribers;

    public function __construct(iterable $eventSubscribers)
    {
        $this->eventSubscribers = $eventSubscribers;
    }

    public function factory(): EventDispatcherInterface
    {
        $eventDispatcher = new EventDispatcher();

        foreach ($this->eventSubscribers as $eventSubscriber) {
            $eventDispatcher->addSubscriber($eventSubscriber);
        }

        return $eventDispatcher;
    }
}