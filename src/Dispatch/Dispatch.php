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

namespace PSX\Framework\Dispatch;

use Psr\EventDispatcher\EventDispatcherInterface;
use PSX\Engine\DispatchInterface;
use PSX\Framework\Event\Context\ControllerContext;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Event\RequestIncomingEvent;
use PSX\Framework\Event\ResponseSendEvent;
use PSX\Framework\Exception\ConverterInterface;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\ContextFactoryInterface;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Http\Authentication;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Http;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;
use PSX\Schema\Exception\ValidationException;

/**
 * The dispatcher routes the request to the fitting controller. The route method contains the global try catch for the
 * application
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Dispatch implements DispatchInterface
{
    private ContextFactoryInterface $contextFactory;
    private LoaderInterface $loader;
    private EventDispatcherInterface $eventDispatcher;
    private ConverterInterface $exceptionConverter;
    private ResponseWriter $responseWriter;
    private int $level;

    public function __construct(ContextFactoryInterface $contextFactory, LoaderInterface $loader, EventDispatcherInterface $eventDispatcher, ConverterInterface $exceptionConverter, ResponseWriter $responseWriter)
    {
        $this->contextFactory     = $contextFactory;
        $this->loader             = $loader;
        $this->eventDispatcher    = $eventDispatcher;
        $this->exceptionConverter = $exceptionConverter;
        $this->responseWriter     = $responseWriter;

        $this->level = 0;
    }

    /**
     * Routes the request to the fitting controller and returns the response
     */
    public function route(RequestInterface $request, ResponseInterface $response, ?Context $context = null): ResponseInterface
    {
        $this->level++;

        $this->eventDispatcher->dispatch(new RequestIncomingEvent($request), Event::REQUEST_INCOMING);

        // load controller
        if ($context === null) {
            $context = $this->contextFactory->factory();
        }

        try {
            $this->loader->load($request, $response, $context);
        } catch (StatusCode\NotModifiedException $e) {
            $response->setStatus($e->getStatusCode());
            $response->setBody(new StringStream());
        } catch (StatusCode\RedirectionException $e) {
            $response->setStatus($e->getStatusCode());
            $response->setHeader('Location', $e->getLocation());
            $response->setBody(new StringStream());
        } catch (\Throwable $e) {
            $this->eventDispatcher->dispatch(new ExceptionThrownEvent($e, new ControllerContext($request, $response)), Event::EXCEPTION_THROWN);

            $this->handleException($e, $response);

            $response->setBody(new StringStream(''));

            $this->responseWriter->setBody($response, $this->exceptionConverter->convert($e), $request);
        }

        // for HEAD requests we never return a response body
        if ($request->getMethod() == 'HEAD') {
            $response->setHeader('Content-Length', $response->getBody()->getSize());
            $response->setBody(new StringStream(''));
        }

        $this->eventDispatcher->dispatch(new ResponseSendEvent($response), Event::RESPONSE_SEND);

        $this->level--;

        return $response;
    }

    protected function handleException(\Throwable $e, ResponseInterface $response): void
    {
        if ($e instanceof StatusCode\StatusCodeException) {
            $this->handleStatusCodeException($e, $response);
        } elseif ($e instanceof ValidationException) {
            $response->setStatus(400);
        } elseif (isset(Http::CODES[$e->getCode()])) {
            $response->setStatus($e->getCode());
        } else {
            $response->setStatus(500);
        }
    }

    protected function handleStatusCodeException(StatusCode\StatusCodeException $e, ResponseInterface $response): void
    {
        $response->setStatus($e->getStatusCode());

        if ($e instanceof StatusCode\MethodNotAllowedException) {
            $allowedMethods = $e->getAllowedMethods();
            if (!empty($allowedMethods)) {
                $response->setHeader('Allow', implode(', ', $allowedMethods));
            }
        } elseif ($e instanceof StatusCode\UnauthorizedException) {
            $type = $e->getType();
            $parameters = $e->getParameters();

            if (!empty($type)) {
                if (!empty($parameters)) {
                    $response->setHeader('WWW-Authenticate', $type . ' ' . Authentication::encodeParameters($parameters));
                } else {
                    $response->setHeader('WWW-Authenticate', $type);
                }
            }
        } elseif ($e instanceof StatusCode\TooManyRequestsException) {
            $retryAfter = $e->getRetryAfter();
            if ($retryAfter > 0) {
                $response->setHeader('Retry-After', $retryAfter);
            }
        }
    }
}
