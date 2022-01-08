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

namespace PSX\Framework\Tests\Controller\Foo\Application\TestController;

use PHPUnit\Framework\Assert;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\FilterChainInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * FilterController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FilterController extends ControllerAbstract
{
    public function getPreFilter(): array
    {
        return [function(RequestInterface $request, ResponseInterface $response, FilterChainInterface $stack) {
            Assert::assertInstanceOf(RequestInterface::class, $request);
            Assert::assertInstanceOf(ResponseInterface::class, $response);

            $stack->handle($request, $response);
        }];
    }

    public function getPostFilter(): array
    {
        return [function(RequestInterface $request, ResponseInterface $response, FilterChainInterface $stack) {
            Assert::assertInstanceOf(RequestInterface::class, $request);
            Assert::assertInstanceOf(ResponseInterface::class, $response);

            $stack->handle($request, $response);
        }];
    }

    protected function doGet(HttpContextInterface $context): string
    {
        return 'foobar';
    }
}
