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

namespace PSX\Framework\Test;

use PSX\Engine\DispatchInterface;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\Stream;
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
    /**
     * Loads a specific controller
     */
    protected function loadController(Request $request, Response $response): ResponseInterface
    {
        return Environment::getService(DispatchInterface::class)->route($request, $response);
    }

    /**
     * Sends a request to the system and returns the http response
     */
    protected function sendRequest(string|Uri $uri, string $method, array $headers = [], ?string $body = null): ResponseInterface
    {
        $request  = new Request(is_string($uri) ? Uri::parse($uri) : $uri, $method, $headers, $body);
        $response = new Response();
        $response->setBody(new Stream(fopen('php://memory', 'r+')));

        $this->loadController($request, $response);

        return $response;
    }
}
