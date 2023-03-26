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

namespace PSX\Framework\Tests\Loader;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\InvalidPathException;
use PSX\Framework\Loader\Loader;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Framework\Loader\LocationFinder\CallbackMethod;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Dispatch\DummyController;
use PSX\Framework\Tests\Dispatch\TestListener;
use PSX\Framework\Tests\Filter\TestFilter;
use PSX\Framework\Tests\Oauth2\AuthorizationCode\TestCallbackController;
use PSX\Http\FilterChainInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Uri\Uri;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * LoaderFilterTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LoaderFilterTest extends ControllerTestCase
{
    public function testFilter()
    {
        $request  = new Request(new Uri('/tests/filter'), 'GET');
        $response = new Response();

        $this->loadController($request, $response);

        $this->assertEquals(['pre_filter' => true, 'post_filter' => true], $request->getAttributes());
        $this->assertEquals(TestFilter::class, $response->getHeader('X-Middleware'));
    }
}
