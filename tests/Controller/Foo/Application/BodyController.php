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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use DOMDocument;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Writer;
use PSX\Record\Record;
use PSX\Schema\Type;
use SimpleXMLElement;
use stdClass;

/**
 * SetBodyController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Path('/tests/body')]
#[QueryParam('type', Type::STRING)]
class BodyController extends ControllerAbstract
{
    #[Get]
    public function doGet(string $type): mixed
    {
        $data = null;
        switch ($type) {
            case 'array':
                $data = ['foo' => ['bar']];
                break;

            case 'stdclass':
                $body = new stdClass();
                $body->foo = array('bar');

                $data = $body;
                break;

            case 'record':
                $data = new Record(['foo' => ['bar']]);
                break;

            case 'dom':
                $dom = new DOMDocument();
                $dom->appendChild($dom->createElement('foo', 'bar'));

                $data = $dom;
                break;

            case 'simplexml':
                $data = new SimpleXMLElement('<foo>bar</foo>');
                break;

            case 'string':
                $data = 'foobar';
                break;

            case 'stream':
                $data = new Writer\File(__DIR__ . '/../Resource/test_file', 'foo.txt', 'application/octet-stream');
                break;

            case 'body':
                $data = new Writer\Json(['foo' => 'bar']);
                break;
        }

        return $data;
    }
}
