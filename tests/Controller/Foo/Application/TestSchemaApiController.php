<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\DateTime\LocalDate;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Schema;
use PSX\Framework\Tests\TestTable;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Environment\HttpResponse;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Sql\TableManagerInterface;

/**
 * TestSchemaApiController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
#[Description('lorem ipsum')]
#[PathParam(name: 'name', type: 'string', description: 'Name parameter', minLength: 0, maxLength: 16, pattern: '[A-z]+')]
#[PathParam(name: 'type', type: 'string', enum: ['foo', 'bar'])]
class TestSchemaApiController extends ControllerAbstract
{
    #[Inject]
    private TableManagerInterface $tableManager;

    #[Description('Returns a collection')]
    #[QueryParam(name: 'startIndex', type: 'integer', description: 'startIndex parameter', minimum: 0, maximum: 32)]
    #[QueryParam(name: 'float', type: 'number')]
    #[QueryParam(name: 'boolean', type: 'boolean')]
    #[QueryParam(name: 'date', type: 'date')]
    #[QueryParam(name: 'datetime', type: 'datetime')]
    #[Outgoing(code: 200, schema: Schema\Collection::class)]
    protected function doGet(HttpContextInterface $context): array
    {
        return array(
            'entry' => $this->tableManager->getTable(TestTable::class)->findAll()
        );
    }

    #[Method('GET')]
    #[Return()]
    #[Throws(500, Message::class)]
    public function myAction(int $startIndex, float $float, bool $boolean, LocalDate $date, \DateTimeInterface $datetime): array
    {
        return [];
    }

    #[Incoming(schema: Schema\Create::class)]
    #[Outgoing(code: 201, schema: Schema\SuccessMessage::class)]
    protected function doPost($record, HttpContextInterface $context): HttpResponse
    {
        Assert::assertEquals('', $context->getUriFragment('name'));
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('test', $record->title);
        Assert::assertInstanceOf('DateTime', $record->date);

        return new HttpResponse(201, [], [
            'success' => true,
            'message' => 'You have successful post a record'
        ]);
    }

    #[Incoming(schema: Schema\Update::class)]
    #[Outgoing(code: 200, schema: Schema\SuccessMessage::class)]
    protected function doPut($record, HttpContextInterface $context): array
    {
        Assert::assertEquals('', $context->getUriFragment('name'));
        Assert::assertEquals(1, $record->id);
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful put a record'
        );
    }

    #[Incoming(schema: Schema\Delete::class)]
    #[Outgoing(code: 200, schema: Schema\SuccessMessage::class)]
    protected function doDelete(HttpContextInterface $context): array
    {
        Assert::assertEquals('', $context->getUriFragment('name'));

        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }

    #[Incoming(schema: Schema\Patch::class)]
    #[Outgoing(code: 200, schema: Schema\SuccessMessage::class)]
    protected function doPatch($record, HttpContextInterface $context): array
    {
        Assert::assertEquals(1, $record->id);
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful patch a record'
        );
    }
}
