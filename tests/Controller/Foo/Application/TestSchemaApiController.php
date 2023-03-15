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
use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\Foo\Model;
use PSX\Framework\Tests\TestTable;
use PSX\Sql\TableManagerInterface;

/**
 * TestSchemaApiController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
#[Description('lorem ipsum')]
#[Path('/foo/:name/:type')]
#[PathParam(name: 'name', type: 'string', description: 'Name parameter', minLength: 0, maxLength: 16, pattern: '[A-z]+')]
#[PathParam(name: 'type', type: 'string', enum: ['foo', 'bar'])]
class TestSchemaApiController extends ControllerAbstract
{
    private TableManagerInterface $tableManager;

    public function __construct(TableManagerInterface $tableManager)
    {
        $this->tableManager = $tableManager;
    }

    #[Description('Returns a collection')]
    #[QueryParam(name: 'startIndex', type: 'integer', description: 'startIndex parameter', minimum: 0, maximum: 32)]
    #[QueryParam(name: 'float', type: 'number')]
    #[QueryParam(name: 'boolean', type: 'boolean')]
    #[QueryParam(name: 'date', type: 'date')]
    #[QueryParam(name: 'datetime', type: 'datetime')]
    #[Outgoing(code: 200, schema: Model\Collection::class)]
    public function doGet(string $name, string $type, ?int $startIndex, ?float $float, ?bool $boolean, ?Date $date, ?DateTime $dateTime): array
    {
        Assert::assertEquals('', $name);
        Assert::assertEquals('', $type);
        Assert::assertNull($startIndex);
        Assert::assertNull($float);
        Assert::assertNull($boolean);
        Assert::assertNull($date);
        Assert::assertNull($dateTime);

        return array(
            'entry' => $this->tableManager->getTable(TestTable::class)->findAll()
        );
    }

    #[Incoming(schema: Model\Create::class)]
    #[Outgoing(code: 201, schema: Model\Message::class)]
    public function doPost(string $name, string $type, Model\Create $record): Model\Message
    {
        Assert::assertEquals('', $name);
        Assert::assertEquals('', $type);
        Assert::assertEquals(3, $record->getUserId());
        Assert::assertEquals('test', $record->getTitle());
        Assert::assertInstanceOf('DateTime', $record->getDate());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful post a record');
        return $message;
    }

    #[Incoming(schema: Model\Update::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doPut(string $name, string $type, Model\Update $record): Model\Message
    {
        Assert::assertEquals('', $name);
        Assert::assertEquals('', $type);
        Assert::assertEquals(1, $record->getId());
        Assert::assertEquals(3, $record->getUserId());
        Assert::assertEquals('foobar', $record->getTitle());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful put a record');
        return $message;
    }

    #[Incoming(schema: Model\Delete::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doDelete(string $name, string $type): Model\Message
    {
        Assert::assertEquals('', $name);
        Assert::assertEquals('', $type);

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful delete a record');
        return $message;
    }

    #[Incoming(schema: Model\Patch::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    public function doPatch(string $name, string $type, Model\Patch $record): Model\Message
    {
        Assert::assertEquals('', $name);
        Assert::assertEquals('', $type);
        Assert::assertEquals(1, $record->getId());
        Assert::assertEquals(3, $record->getUserId());
        Assert::assertEquals('foobar', $record->getTitle());

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('You have successful patch a record');
        return $message;
    }
}
