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

use Psr\Log\LoggerInterface;
use PSX\Framework\DisplayException;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Http\Exception\StatusCodeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * LoggingListener
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LoggingListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onExceptionThrown(ExceptionThrownEvent $event): void
    {
        $exception = $event->getException();
        $severity  = $exception instanceof \ErrorException ? $exception->getSeverity() : null;
        $context   = array(
            'file'     => $exception->getFile(),
            'line'     => $exception->getLine(),
            'trace'    => $exception->getTraceAsString(),
            'code'     => $exception->getCode(),
            'severity' => $severity,
        );

        if ($exception instanceof StatusCodeException) {
            if ($exception->isClientError()) {
                $this->logger->notice($exception->getMessage(), $context);
            } elseif ($exception->isServerError()) {
                $this->logger->error($exception->getMessage(), $context);
            } else {
                $this->logger->info($exception->getMessage(), $context);
            }
        } elseif ($exception instanceof DisplayException) {
            $this->logger->notice($exception->getMessage(), $context);
        } else {
            $this->logger->error($exception->getMessage(), $context);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event::EXCEPTION_THROWN => 'onExceptionThrown',
        ];
    }
}
