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

namespace PSX\Framework\Loader;

use PSX\Framework\Loader\Context;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * LoaderInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface LoaderInterface
{
    /**
     * Loads the controller instance based on the provided request. Usually this 
     * means we use a router to find the fitting controller class name. Then we
     * execute this instance through the execute method
     * 
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @param \PSX\Framework\Loader\Context $context
     * @return void
     */
    public function load(RequestInterface $request, ResponseInterface $response, Context $context = null);

    /**
     * Executes a specific controller instance. This means that we determine the 
     * middleware stack based on the controller and execute it. Note the load 
     * method also calls this method after the controller was loaded
     *
     * @param mixed $controller
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @return void
     */
    public function execute($controller, RequestInterface $request, ResponseInterface $response);
}
