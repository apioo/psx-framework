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

namespace PSX\Framework\Tests\Http;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Data\Configuration;
use PSX\Data\Processor;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Model\Property;
use PSX\Http\Response;
use PSX\Schema\SchemaManager;

/**
 * ResponseWriterTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetBody()
    {
        $response = new Response();

        $data = new Property();
        $data->setArray(['foo', 'bar']);
        $data->setBoolean(true);

        $this->newResponseWriter()->setBody($response, $data);

        $expect = <<<JSON
{
    "array": ["foo", "bar"],
    "boolean": true
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
    }

    public function testSetBodyStdClass()
    {
        $response = new Response();

        $data = new \stdClass();
        $data->array = ['foo', 'bar'];
        $data->boolean = true;

        $this->newResponseWriter()->setBody($response, $data);

        $expect = <<<JSON
{
    "array": ["foo", "bar"],
    "boolean": true
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
    }

    /**
     * @return \PSX\Framework\Http\ResponseWriter
     */
    protected function newResponseWriter()
    {
        return Environment::getService('response_writer');
    }
}
