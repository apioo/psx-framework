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
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\QueryParam;
use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\LocalTime;
use PSX\DateTime\Period;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\Foo\Model;
use PSX\Framework\Tests\Controller\PropertyTestCase;
use PSX\Record\RecordInterface;

/**
 * PopoController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Path('/tests/popo/:id')]
#[PathParam(name: 'id', type: 'integer')]
class PopoController extends ControllerAbstract
{
    use PropertyControllerTrait;

    #[Get]
    #[QueryParam(name: 'type', type: 'integer')]
    #[Outgoing(code: 200, schema: Model\Property::class)]
    public function doGet(int $id, int $type): mixed
    {
        Assert::assertEquals(1, $id);

        return PropertyTestCase::getDataByType($type);
    }

    #[Post]
    #[Incoming(schema: Model\Property::class)]
    #[Outgoing(code: 200, schema: Model\Property::class)]
    public function doPost(int $id, Model\Property $record): mixed
    {
        Assert::assertInstanceOf(Model\Property::class, $record);
        Assert::assertInstanceOf(RecordInterface::class, $record->getAny());
        Assert::assertEquals('bar', $record->getAny()['foo']);
        Assert::assertIsArray($record->getArray());
        Assert::assertEquals(1, count($record->getArray()));
        Assert::assertEquals(['bar'], $record->getArray());
        Assert::assertIsArray($record->getArrayComplex());
        Assert::assertEquals(2, count($record->getArrayComplex()));
        Assert::assertInstanceOf(Model\Complex::class, $record->getArrayComplex()[0]);
        Assert::assertEquals('bar', $record->getArrayComplex()[0]->getFoo());
        Assert::assertInstanceOf(Model\Complex::class, $record->getArrayComplex()[1]);
        Assert::assertEquals('foo', $record->getArrayComplex()[1]->getFoo());
        Assert::assertIsArray($record->getArrayChoice());
        Assert::assertEquals(3, count($record->getArrayChoice()));
        Assert::assertInstanceOf(Model\ChoiceA::class, $record->getArrayChoice()[0]);
        Assert::assertEquals('baz', $record->getArrayChoice()[0]->getFoo());
        Assert::assertInstanceOf(Model\ChoiceB::class, $record->getArrayChoice()[1]);
        Assert::assertEquals('bar', $record->getArrayChoice()[1]->getBar());
        Assert::assertInstanceOf(Model\ChoiceA::class, $record->getArrayChoice()[2]);
        Assert::assertEquals('foo', $record->getArrayChoice()[2]->getFoo());
        Assert::assertIsBool($record->getBoolean());
        Assert::assertEquals(true, $record->getBoolean());
        Assert::assertInstanceOf(Model\ChoiceB::class, $record->getChoice());
        Assert::assertEquals('test', $record->getChoice()->getBar());
        Assert::assertInstanceOf(Model\Complex::class, $record->getComplex());
        Assert::assertEquals('bar', $record->getComplex()->getFoo());
        Assert::assertInstanceOf(LocalDate::class, $record->getDate());
        Assert::assertEquals('2015-05-01', $record->getDate()->toString());
        Assert::assertInstanceOf(LocalDateTime::class, $record->getDateTime());
        Assert::assertEquals('2015-05-01T13:37:14Z', $record->getDateTime()->toString());
        Assert::assertInstanceOf(Period::class, $record->getDuration());
        Assert::assertEquals('P1M', $record->getDuration()->toString());
        Assert::assertIsFloat($record->getFloat());
        Assert::assertEquals(13.37, $record->getFloat());
        Assert::assertIsInt($record->getInteger());
        Assert::assertEquals(7, $record->getInteger());
        Assert::assertIsString($record->getString());
        Assert::assertEquals('bar', $record->getString());
        Assert::assertInstanceOf(LocalTime::class, $record->getTime());
        Assert::assertEquals('13:37:14', $record->getTime()->toString());

        return $record;
    }
}
