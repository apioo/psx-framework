<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Filter\Condition;

use PSX\Framework\Filter\FilterChainInterface;
use PSX\Framework\Filter\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * Applies a filter only for specific request methods. This is useful if you
 * want i.e. authentication only for specific request methods
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestMethodChoice implements FilterInterface
{
    protected $requestMethods;
    protected $filter;

    public function __construct(array $requestMethods, FilterInterface $filter)
    {
        $this->requestMethods = $requestMethods;
        $this->filter         = $filter;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        if (in_array($request->getMethod(), $this->requestMethods)) {
            $this->filter->handle($request, $response, $filterChain);
        } else {
            $filterChain->handle($request, $response);
        }
    }
}
