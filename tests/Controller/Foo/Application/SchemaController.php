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
use PSX\Api\Attribute\Delete;
use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Patch;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\Put;
use PSX\Api\Attribute\QueryParam;
use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\Foo\Model;
use PSX\Framework\Tests\TestTable;
use PSX\Sql\TableManagerInterface;

/**
 * SchemaController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
#[Description('lorem ipsum')]
#[Path('/tests/schema/:name/:type')]
#[PathParam(name: 'name', type: 'string', description: 'Name parameter', minLength: 0, maxLength: 16, pattern: '[A-z]+')]
#[PathParam(name: 'type', type: 'string', enum: ['foo', 'bar'])]
class SchemaController extends ControllerAbstract
{
    private TableManagerInterface $tableManager;

    public function __construct(TableManagerInterface $tableManager)
    {
        $this->tableManager = $tableManager;
    }

    #[Get]
    #[Description('Returns a collection')]
    #[QueryParam(name: 'startIndex', type: 'integer', description: 'startIndex parameter', minimum: 0, maximum: 32)]
    #[QueryParam(name: 'float', type: 'number')]
    #[QueryParam(name: 'boolean', type: 'boolean')]
    #[QueryParam(name: 'date', type: 'string', format: 'date')]
    #[QueryParam(name: 'dateTime', type: 'string', format: 'date-time')]
    #[Outgoing(code: 200, schema: Model\Collection::class)]
    public function doGet(string $name, string $type, ?int $startIndex, ?float $float, ?bool $boolean, ?Date $date, ?DateTime $dateTime): array
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);
        Assert::assertEquals(0, $startIndex);
        Assert::assertEquals(0.0, $float);
        Assert::assertEquals(false, $boolean);
        Assert::assertInstanceOf(Date::class, $date);
        Assert::assertInstanceOf(DateTime::class, $dateTime);

        return [
            'entry' => $this->tableManager->getTable(TestTable::class)->findAll()
        ];
    }

    #[Post]
    #[Incoming(schema: Model\Create::class, name: 'record')]
    #[Outgoing(code: 201, schema: Model\Message::class)]
    public function doPost(string $name, string $type, Model\Create $record): Model\Message
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);
        Assert::assertEquals(3, $record->getUserId());
        Assert::assertEquals('test', $record->getTitle());
        Assert::assertInstanceOf('DateTime', $record->getDate());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful post a record');
        return $message;
    }

    #[Put]
    #[Incoming(schema: Model\Update::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doPut(string $name, string $type, Model\Update $record): Model\Message
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);
        Assert::assertEquals(1, $record->getId());
        Assert::assertEquals(3, $record->getUserId());
        Assert::assertEquals('foobar', $record->getTitle());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful put a record');
        return $message;
    }

    #[Delete]
    #[Incoming(schema: Model\Delete::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doDelete(string $name, string $type): Model\Message
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful delete a record');
        return $message;
    }

    #[Patch]
    #[Incoming(schema: Model\Patch::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doPatch(string $name, string $type, Model\Patch $record): Model\Message
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);
        Assert::assertEquals(1, $record->getId());
        Assert::assertEquals(3, $record->getUserId());
        Assert::assertEquals('foobar', $record->getTitle());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful patch a record');
        return $message;
    }
}