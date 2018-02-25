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

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Http\Body;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\FileStream;
use PSX\Record\Record;

/**
 * SetBodyController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SetBodyController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $type = $request->getUri()->getParameter('type');
        $data = null;
        
        switch ($type) {
            case 'array':
                $data = ['foo' => ['bar']];
                break;

            case 'stdclass':
                $body = new \stdClass();
                $body->foo = array('bar');

                $data = $body;
                break;

            case 'record':
                $data = new Record('record', ['foo' => ['bar']]);
                break;

            case 'dom':
                $dom = new \DOMDocument();
                $dom->appendChild($dom->createElement('foo', 'bar'));

                $data = $dom;
                break;

            case 'simplexml':
                $data = new \SimpleXMLElement('<foo>bar</foo>');
                break;

            case 'string':
                $data = 'foobar';
                break;

            case 'stream':
                $data = new Body\File(__DIR__ . '/../../Resource/test_file', 'foo.txt', 'application/octet-stream');
                break;

            case 'body':
                $data = new Body\Json(['foo' => 'bar']);
                break;
        }

        $this->responseWriter->setBody($response, $data, $request);
    }
}
