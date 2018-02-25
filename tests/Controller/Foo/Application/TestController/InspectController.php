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

namespace PSX\Framework\Tests\Controller\Foo\Application\TestController;

use PSX\Data\Accessor;
use PSX\Data\ReaderInterface;
use PSX\Framework\Config\Config;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Loader\Context;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Record\Record;
use PSX\Uri\Uri;
use PSX\Validate\Validate;
use PSX\Validate\Filter;

/**
 * InspectController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class InspectController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function onPost(RequestInterface $request, ResponseInterface $response)
    {
        // get uri fragments
        $this->testCase->assertEquals(null, $this->getUriFragment('foo'));

        // set response code
        $response->setStatus(200);

        $this->testCase->assertEquals(200, $response->getStatusCode());

        // set header
        $response->setHeader('Content-Type', 'application/xml');

        $this->testCase->assertEquals('application/xml', $response->getHeader('Content-Type'));

        // get method
        $this->testCase->assertEquals('POST', $request->getMethod());

        // get uri
        $this->testCase->assertInstanceOf(Uri::class, $request->getUri());

        // get header
        $this->testCase->assertEquals(null, $request->getHeader('foo'));

        // has header
        $this->testCase->assertEquals(false, $request->hasHeader('foo'));

        // get parameter
        $this->testCase->assertEquals('bar', $request->getUri()->getParameter('foo'));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, [new Filter\Alnum()]));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, [], 'Foo'));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, [], 'Foo', true));

        // get body
        $data = new \stdClass();
        $data->foo = 'bar';
        $data->bar = new \stdClass();
        $data->bar->foo = 'nested';
        $data->entries = [];
        $data->entries[0] = new \stdClass();
        $data->entries[0]->title = 'bar';
        $data->entries[1] = new \stdClass();
        $data->entries[1]->title = 'foo';

        $this->testCase->assertEquals($data, $this->requestReader->getBody($request));
        $this->testCase->assertEquals($data, $this->requestReader->getBody($request, ReaderInterface::JSON));

        // accessor
        $body = $this->requestReader->getBody($request);

        $this->testCase->assertEquals('bar', Accessor::get($body, '/foo'));
        $this->testCase->assertEquals('nested', Accessor::get($body, '/bar/foo'));
        $this->testCase->assertEquals('bar', Accessor::get($body, '/entries/0/title'));
        $this->testCase->assertEquals('foo', Accessor::get($body, '/entries/1/title'));

        // import
        $body = $this->requestReader->getBodyAs($request, TestBody::class);

        $this->testCase->assertInstanceOf(TestBody::class, $body);
        $this->testCase->assertEquals('bar', $body->getFoo());

        // set response
        $record = new Record('foo', array('bar' => 'foo'));

        $this->responseWriter->setBody($response, $record, $request);

        // test properties
        $this->testCase->assertInstanceOf(Context::class, $this->context);
        $this->testCase->assertEquals(self::class, $this->context->getSource());
        $this->testCase->assertTrue(is_array($this->uriFragments));
        $this->testCase->assertInstanceOf(Config::class, $this->config);
    }
}
