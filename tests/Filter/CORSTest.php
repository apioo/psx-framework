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

namespace PSX\Framework\Tests\Filter;

use PSX\Framework\Filter\CORS;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Url;

/**
 * CORSTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CORSTest extends \PHPUnit_Framework_TestCase
{
    public function testAllowOrigin()
    {
        $request  = new Request(new Url('http://localhost'), 'GET', array());
        $response = new Response();

        $filterChain = $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();

        $filterChain->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request), $this->equalTo($response));

        $handle = CORS::allowOrigin('*');
        $handle->handle($request, $response, $filterChain);

        $this->assertTrue($response->hasHeader('Access-Control-Allow-Origin'));
        $this->assertEquals('*', $response->getHeader('Access-Control-Allow-Origin'));
    }

    public function testAllowOriginEmpty()
    {
        $request  = new Request(new Url('http://localhost'), 'GET', array());
        $response = new Response();

        $filterChain = $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();

        $filterChain->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request), $this->equalTo($response));

        $handle = CORS::allowOrigin('');
        $handle->handle($request, $response, $filterChain);

        $this->assertFalse($response->hasHeader('Access-Control-Allow-Origin'));
    }
}
