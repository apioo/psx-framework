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

namespace PSX\Framework\Controller\Proxy;

use PSX\Data\WriterInterface;
use PSX\Framework\Controller\ApiAbstract;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Uri\Uri;

/**
 * SoapController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SoapController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Dispatch\Dispatch
     */
    protected $dispatch;

    public function onRequest(RequestInterface $request, ResponseInterface $response)
    {
        if ($request->getMethod() != 'POST') {
            throw new StatusCode\MethodNotAllowedException('Only POST requests are allowed', ['POST']);
        }

        $soapAction = $request->getHeader('SOAPAction');

        if (empty($soapAction)) {
            throw new StatusCode\BadRequestException('No SOAPAction header was provided');
        }

        $actionUri = trim(strstr($soapAction . ';', ';', true), '" ');
        $uri       = new Uri($actionUri);
        $method    = $uri->getFragment();

        if (!in_array($method, ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'])) {
            throw new StatusCode\BadRequestException('Invalid request method');
        }

        $headers = $request->getHeaders();
        $headers['Content-Type'] = 'application/soap+xml';
        $headers['Accept']       = 'application/soap+xml';

        $request = new Request($uri, $method, $headers, $request->getBody());

        $this->dispatch->route($request, $response, $this->context);
    }
}
