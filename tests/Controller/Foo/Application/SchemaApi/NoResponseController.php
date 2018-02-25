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

namespace PSX\Framework\Tests\Controller\Foo\Application\SchemaApi;

use PSX\Api\Resource;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Tests\Controller\Foo\Schema;
use PSX\Http\Environment\HttpContextInterface;

/**
 * NoResponseController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class NoResponseController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->getPath());

        $resource->addMethod(Resource\Factory::getMethod('GET'));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema(Schema\Create::class)));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($this->schemaManager->getSchema(Schema\Update::class)));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($this->schemaManager->getSchema(Schema\Delete::class)));

        $resource->addMethod(Resource\Factory::getMethod('PATCH')
            ->setRequest($this->schemaManager->getSchema(Schema\Patch::class)));

        return $resource;
    }

    protected function doGet(HttpContextInterface $context)
    {
    }

    protected function doPost($record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('test', $record->title);
        $this->testCase->assertInstanceOf('DateTime', $record->date);
    }

    protected function doPut($record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(1, $record->id);
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('foobar', $record->title);
    }

    protected function doDelete($record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(1, $record->id);
    }
}
