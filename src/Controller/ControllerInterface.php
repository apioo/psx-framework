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

namespace PSX\Framework\Controller;

use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * ControllerInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface ControllerInterface
{
    /**
     * Method which gets always called before the on* methods are called
     */
    public function onLoad();

    /**
     * @param \PSX\Http\RequestInterface
     * @param \PSX\Http\ResponseInterface
     */
    public function onRequest(RequestInterface $request, ResponseInterface $response);

    /**
     * Is called after the controller action was called
     */
    public function onFinish();
}
