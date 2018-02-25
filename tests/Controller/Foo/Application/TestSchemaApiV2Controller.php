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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use PSX\Api\Resource;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo;
use PSX\Framework\Tests\TestTable;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Schema\Schema;

/**
 * TestSchemaApiV2Controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestSchemaApiV2Controller extends SchemaApiAbstract
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

        $schema = $this->schemaManager->getSchema(Foo\Schema\Collection::class);

        // remove userId property so we have a different output
        $property = $schema->getDefinition();
        $property->getProperty('entry')->getItems()->removeProperty('userId');

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addResponse(200, new Schema($property)));

        return $resource;
    }

    protected function doGet(HttpContextInterface $context)
    {
        return array(
            'entry' => Environment::getService('table_manager')->getTable(TestTable::class)->getAll()
        );
    }

    protected function doPost($record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('test', $record->title);
        $this->testCase->assertInstanceOf('DateTime', $record->date);

        return array(
            'success' => true,
            'message' => 'You have successful post a record'
        );
    }

    protected function doPut($record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(1, $record->id);
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful put a record'
        );
    }

    protected function doDelete($record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(1, $record->id);

        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }

    protected function doPatch($record, HttpContextInterface $context)
    {
        $this->testCase->assertEquals(1, $record->id);
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful patch a record'
        );
    }
}
