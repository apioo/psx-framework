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
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\TestTable;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Record\RecordInterface;
use PSX\Schema\Property;

/**
 * EntityController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class EntityController extends SchemaApiAbstract
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
        $resource->addPathParameter(Property::getInteger('fooId'));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addQueryParameter(Property::getInteger('startIndex'))
            ->addQueryParameter(Property::getInteger('count'))
            ->addResponse(200, $this->schemaManager->getSchema(Schema\Collection::class)));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($this->schemaManager->getSchema(Schema\Update::class))
            ->addResponse(200, $this->schemaManager->getSchema(Schema\SuccessMessage::class)));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($this->schemaManager->getSchema(Schema\Delete::class))
            ->addResponse(200, $this->schemaManager->getSchema(Schema\SuccessMessage::class)));

        return $resource;
    }

    protected function doGet(HttpContextInterface $context)
    {
        $this->testCase->assertEquals(12, $context->getParameter('startIndex'));
        $this->testCase->assertEmpty($context->getParameter('bar'));
        $this->testCase->assertEquals(8, $context->getParameter('fooId'));
        $this->testCase->assertEmpty($context->getParameter('bar'));

        return array(
            'entry' => Environment::getService('table_manager')->getTable(TestTable::class)->getAll()
        );
    }

    protected function doPost(RecordInterface $record, HttpContextInterface $context)
    {
    }

    protected function doPut(RecordInterface $record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(8, $context->getUriFragment('fooId'));
        $this->testCase->assertEmpty($context->getUriFragment('bar'));

        $this->testCase->assertEquals(1, $record->getId());
        $this->testCase->assertEquals(3, $record->getUserId());
        $this->testCase->assertEquals('foobar', $record->getTitle());

        return array(
            'success' => true,
            'message' => 'You have successful update a record'
        );
    }

    protected function doDelete(RecordInterface $record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(8, $context->getUriFragment('fooId'));
        $this->testCase->assertEmpty($context->getUriFragment('bar'));

        $this->testCase->assertEquals(1, $record->getId());

        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }
}
