<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\Foo\Model;
use PSX\Framework\Tests\TestTable;
use PSX\Schema\Format;
use PSX\Schema\Type;
use PSX\Sql\TableManagerInterface;

/**
 * SchemaController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Description('lorem ipsum')]
#[Path('/tests/schema/:name/:type')]
#[PathParam(name: 'name', type: Type::STRING, description: 'Name parameter')]
#[PathParam(name: 'type', type: Type::STRING)]
class SchemaController extends ControllerAbstract
{
    private TableManagerInterface $tableManager;

    public function __construct(TableManagerInterface $tableManager)
    {
        $this->tableManager = $tableManager;
    }

    #[Get]
    #[Description('Returns a collection')]
    #[QueryParam(name: 'startIndex', type: Type::STRING, description: 'startIndex parameter')]
    #[QueryParam(name: 'float', type: Type::NUMBER)]
    #[QueryParam(name: 'boolean', type: Type::BOOLEAN)]
    #[QueryParam(name: 'date', type: Type::STRING, format: Format::DATE)]
    #[QueryParam(name: 'dateTime', type: Type::STRING, format: Format::DATETIME)]
    #[Outgoing(code: 200, schema: Model\Collection::class)]
    public function doGet(string $name, string $type, ?int $startIndex, ?float $float, ?bool $boolean, ?LocalDate $date, ?LocalDateTime $dateTime): array
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);
        Assert::assertNull($startIndex);
        Assert::assertNull($float);
        Assert::assertNull($boolean);
        Assert::assertNull($date);
        Assert::assertNull($dateTime);

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
        Assert::assertInstanceOf(LocalDateTime::class, $record->getDate());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful post a record');
        return $message;
    }

    #[Put]
    #[Incoming(schema: Model\Update::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doPut(string $name, string $type, Model\Update $payload): Model\Message
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);
        Assert::assertEquals(1, $payload->getId());
        Assert::assertEquals(3, $payload->getUserId());
        Assert::assertEquals('foobar', $payload->getTitle());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful put a record');
        return $message;
    }

    #[Delete]
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
    public function doPatch(string $name, string $type, Model\Patch $payload): Model\Message
    {
        Assert::assertEquals('foo', $name);
        Assert::assertEquals('bar', $type);
        Assert::assertEquals(1, $payload->getId());
        Assert::assertEquals(3, $payload->getUserId());
        Assert::assertEquals('foobar', $payload->getTitle());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful patch a record');
        return $message;
    }
}
