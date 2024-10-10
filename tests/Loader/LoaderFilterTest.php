<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Filter\TestFilter;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Uri;

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
        $request  = new Request(Uri::parse('/tests/filter'), 'GET');
        $response = new Response();

        $this->loadController($request, $response);

        $this->assertEquals(['pre_filter' => true, 'post_filter' => true], $request->getAttributes());
        $this->assertEquals(TestFilter::class, $response->getHeader('X-Middleware'));
    }
}
