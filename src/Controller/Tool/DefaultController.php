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

namespace PSX\Framework\Controller\Tool;

use PSX\Framework\Controller\ApiAbstract;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * DefaultController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DefaultController extends ApiAbstract
{
    public function onRequest(RequestInterface $request, ResponseInterface $response)
    {
        $result = [
            'message' => 'This is the default controller of PSX',
            'url'     => 'http://phpsx.org',
        ];

        $this->responseWriter->setBody($response, $result, $this->getWriterOptions($request));
    }
}
