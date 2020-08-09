<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\SpecificationInterface;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\TestTable;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Schema\Property;
use PSX\Framework\Tests\Controller\Foo\Schema;

/**
 * TestSchemaApiController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestSchemaApiController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    /**
     * @Inject
     * @var \PHPUnit\Framework\TestCase
     */
    protected $testCase;

    public function getDocumentation(?string $version = null): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder(Resource::STATUS_ACTIVE, $this->context->getPath());
        $builder->setTitle('foo');
        $builder->setDescription('lorem ipsum');

        $path = $builder->setPathParameters('Path');
        $path->addString('name')
            ->setDescription('Name parameter')
            ->setMinLength(0)
            ->setMaxLength(16)
            ->setPattern('[A-z]+');
        $path->addString('type')
            ->setEnum(['foo', 'bar']);

        $get = $builder->addMethod('GET');
        $get->setDescription('Returns a collection');
        $get->addResponse(200, Schema\Collection::class);

        $query = $get->setQueryParameters('Get_Query');
        $query->addInteger('startIndex')
                ->setDescription('startIndex parameter')
                ->setMinimum(0)
                ->setMaximum(32);
        $query->addNumber('float');
        $query->addBoolean('boolean');
        $query->addDate('date');
        $query->addDateTime('datetime');

        $post = $builder->addMethod('POST');
        $post->setRequest(Schema\Create::class);
        $post->addResponse(201, Schema\SuccessMessage::class);

        $put = $builder->addMethod('PUT');
        $put->setRequest(Schema\Update::class);
        $put->addResponse(200, Schema\SuccessMessage::class);

        $delete = $builder->addMethod('DELETE');
        $delete->setRequest(Schema\Delete::class);
        $delete->addResponse(200, Schema\SuccessMessage::class);

        $patch = $builder->addMethod('PATCH');
        $patch->setRequest(Schema\Patch::class);
        $patch->addResponse(200, Schema\SuccessMessage::class);

        return $builder->getSpecification();
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
