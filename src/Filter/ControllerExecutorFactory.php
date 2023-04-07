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

namespace PSX\Framework\Filter;

use PSX\Api\ApiManagerInterface;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Loader\Context;

/**
 * ControllerExecutorFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ControllerExecutorFactory
{
    private RequestReader $requestReader;
    private ResponseWriter $responseWriter;
    private ApiManagerInterface $apiManager;

    public function __construct(RequestReader $requestReader, ResponseWriter $responseWriter, ApiManagerInterface $apiManager)
    {
        $this->requestReader = $requestReader;
        $this->responseWriter = $responseWriter;
        $this->apiManager = $apiManager;
    }

    public function factory(object $controller, string $methodName, Context $context): ControllerExecutor
    {
        return new ControllerExecutor(
            $controller,
            $methodName,
            $context,
            $this->requestReader,
            $this->responseWriter,
            $this->apiManager
        );
    }
}
