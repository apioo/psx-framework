<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Listener;

use PHPUnit\Framework\Exception;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * In our test cases we sometimes assert a value inside a controller which was called by a test case, this listener
 * simply redirects all PHPUnit exceptions
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class PHPUnitExceptionListener implements EventSubscriberInterface
{
    public function onExceptionThrown(ExceptionThrownEvent $event)
    {
        if ($event->getException() instanceof Exception) {
            throw $event->getException();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event::EXCEPTION_THROWN => 'onExceptionThrown',
        ];
    }
}
