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
     * The loader takes the incoming request and returns an controller. The
     * loader should pass the response to the controller so that the controller
     * can write data to the response
     *
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @param \PSX\Framework\Loader\Context $context
     * @return \PSX\Framework\Controller\ControllerInterface
     */
    public function load(RequestInterface $request, ResponseInterface $response, Context $context = null);
}
