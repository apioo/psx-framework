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

namespace PSX\Framework\Tests\Loader;

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Loader\Context;
use PSX\Http\FilterChainInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * ProbeController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ProbeController extends ControllerAbstract
{
    protected $methodsCalled = array();

    public function __construct(Context $context = null)
    {
        parent::__construct($context);

        $this->methodsCalled[] = __METHOD__;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $this->methodsCalled[] = __METHOD__;

        return parent::getIterator();
    }

    /**
     * @inheritdoc
     */
    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $this->methodsCalled[] = __METHOD__;

        parent::handle($request, $response, $filterChain);
    }

    public function getPreFilter()
    {
        $this->methodsCalled[] = __METHOD__;

        return parent::getPreFilter();
    }

    public function getPostFilter()
    {
        $this->methodsCalled[] = __METHOD__;

        return parent::getPostFilter();
    }

    public function onLoad()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onRequest(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;

        parent::onRequest($request, $response);
    }

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onHead(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onPost(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onPut(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onDelete(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onOptions(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onPatch(RequestInterface $request, ResponseInterface $response)
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onFinish()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function getMethodsCalled()
    {
        return $this->methodsCalled;
    }

    public function getFragments()
    {
        return $this->uriFragments;
    }
}
