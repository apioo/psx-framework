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

namespace PSX\Framework\Dispatch;

use PSX\Framework\Config\Config;
use PSX\Framework\Controller\ErrorController;
use PSX\Framework\Event\Context\ControllerContext;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Event\RequestIncomingEvent;
use PSX\Framework\Event\ResponseSendEvent;
use PSX\Framework\Exception\ConverterInterface;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Http\Authentication;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Http;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;
use PSX\Json\Parser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The dispatcher routes the request to the fitting controller. The route method
 * contains the global try catch for the application
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Dispatch
{
    /**
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

    /**
     * @var \PSX\Framework\Loader\LoaderInterface
     */
    protected $loader;

    /**
     * @var \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    protected $controllerFactory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \PSX\Framework\Exception\ConverterInterface
     */
    protected $exceptionConverter;

    /**
     * @var integer
     */
    protected $level;

    /**
     * @param \PSX\Framework\Config\Config $config
     * @param \PSX\Framework\Loader\LoaderInterface $loader
     * @param \PSX\Framework\Dispatch\ControllerFactoryInterface $controllerFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \PSX\Framework\Exception\ConverterInterface $exceptionConverter
     */
    public function __construct(Config $config, LoaderInterface $loader, ControllerFactoryInterface $controllerFactory, EventDispatcherInterface $eventDispatcher, ConverterInterface $exceptionConverter)
    {
        $this->config             = $config;
        $this->loader             = $loader;
        $this->controllerFactory  = $controllerFactory;
        $this->eventDispatcher    = $eventDispatcher;
        $this->exceptionConverter = $exceptionConverter;

        $this->level = 0;
    }

    /**
     * Routes the request to the fitting controller and returns the response
     *
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @param \PSX\Framework\Loader\Context $context
     * @return \PSX\Http\ResponseInterface
     */
    public function route(RequestInterface $request, ResponseInterface $response, Context $context = null)
    {
        $this->level++;

        $this->eventDispatcher->dispatch(Event::REQUEST_INCOMING, new RequestIncomingEvent($request));

        // load controller
        if ($context === null) {
            $factory = $this->config->get('psx_context_factory');
            if ($factory instanceof \Closure) {
                $context = $factory();
            } else {
                $context = new Context();
            }
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
            $this->eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));

            $this->handleException($e, $response);

            try {
                $context->setException($e);

                $class      = isset($this->config['psx_error_controller']) ? $this->config['psx_error_controller'] : ErrorController::class;
                $controller = $this->controllerFactory->getController($class, $context);

                $this->loader->execute($controller, $request, $response);
            } catch (\Throwable $e) {
                // in this case the error controller has thrown an exception.
                // This can happen i.e. if we can not represent the error in an
                // fitting media type. In this case we send json to the client

                $this->handleException($e, $response);

                $record = $this->exceptionConverter->convert($e);

                $response->setHeader('Content-Type', 'application/json');
                $response->setBody(new StringStream(Parser::encode($record, JSON_PRETTY_PRINT)));
            }
        }

        // for HEAD requests we never return a response body
        if ($request->getMethod() == 'HEAD') {
            $response->setHeader('Content-Length', $response->getBody()->getSize());
            $response->setBody(new StringStream(''));
        }

        $this->eventDispatcher->dispatch(Event::RESPONSE_SEND, new ResponseSendEvent($response));

        $this->level--;

        return $response;
    }

    protected function handleException(\Throwable $e, ResponseInterface $response)
    {
        if ($e instanceof StatusCode\StatusCodeException) {
            $this->handleStatusCodeException($e, $response);
        } elseif ($response->getStatusCode() == null) {
            if (isset(Http::$codes[$e->getCode()])) {
                $response->setStatus($e->getCode());
            } else {
                $response->setStatus(500);
            }
        }
    }

    protected function handleStatusCodeException(StatusCode\StatusCodeException $e, ResponseInterface $response)
    {
        $response->setStatus($e->getStatusCode());

        if ($e instanceof StatusCode\MethodNotAllowedException) {
            $allowedMethods = $e->getAllowedMethods();

            if (!empty($allowedMethods)) {
                $response->setHeader('Allow', implode(', ', $allowedMethods));
            }
        } elseif ($e instanceof StatusCode\UnauthorizedException) {
            $type       = $e->getType();
            $parameters = $e->getParameters();

            if (!empty($type)) {
                if (!empty($parameters)) {
                    $response->setHeader('WWW-Authenticate', $type . ' ' . Authentication::encodeParameters($parameters));
                } else {
                    $response->setHeader('WWW-Authenticate', $type);
                }
            }
        }
    }
}
