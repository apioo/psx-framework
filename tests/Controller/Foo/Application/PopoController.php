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
use PSX\Schema\Type;

/**
 * PopoController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Path('/tests/popo/:id')]
#[PathParam(name: 'id', type: Type::INTEGER)]
class PopoController extends ControllerAbstract
{
    use PropertyControllerTrait;

    #[Get]
    #[QueryParam(name: 'type', type: Type::INTEGER)]
    #[Outgoing(code: 200, schema: Model\Property::class)]
    public function doGet(int $id, int $type): mixed
    {
        Assert::assertEquals(1, $id);

        return PropertyTestCase::getDataByType($type);
    }

    #[Post]
    #[Incoming(schema: Model\Property::class)]
    #[Outgoing(code: 200, schema: Model\Property::class)]
    public function doPost(int $id, Model\Property $payload): mixed
    {
        Assert::assertInstanceOf(Model\Property::class, $payload);
        Assert::assertInstanceOf(RecordInterface::class, $payload->getAny());
        Assert::assertEquals('bar', $payload->getAny()['foo']);
        Assert::assertIsArray($payload->getArray());
        Assert::assertEquals(1, count($payload->getArray()));
        Assert::assertEquals(['bar'], $payload->getArray());
        Assert::assertIsArray($payload->getArrayComplex());
        Assert::assertEquals(2, count($payload->getArrayComplex()));
        Assert::assertInstanceOf(Model\Complex::class, $payload->getArrayComplex()[0]);
        Assert::assertEquals('bar', $payload->getArrayComplex()[0]->getFoo());
        Assert::assertInstanceOf(Model\Complex::class, $payload->getArrayComplex()[1]);
        Assert::assertEquals('foo', $payload->getArrayComplex()[1]->getFoo());
        Assert::assertIsArray($payload->getArrayChoice());
        Assert::assertEquals(3, count($payload->getArrayChoice()));
        Assert::assertInstanceOf(Model\ChoiceA::class, $payload->getArrayChoice()[0]);
        Assert::assertEquals('baz', $payload->getArrayChoice()[0]->getFoo());
        Assert::assertInstanceOf(Model\ChoiceB::class, $payload->getArrayChoice()[1]);
        Assert::assertEquals('bar', $payload->getArrayChoice()[1]->getBar());
        Assert::assertInstanceOf(Model\ChoiceA::class, $payload->getArrayChoice()[2]);
        Assert::assertEquals('foo', $payload->getArrayChoice()[2]->getFoo());
        Assert::assertIsBool($payload->getBoolean());
        Assert::assertEquals(true, $payload->getBoolean());
        Assert::assertInstanceOf(Model\ChoiceB::class, $payload->getChoice());
        Assert::assertEquals('test', $payload->getChoice()->getBar());
        Assert::assertInstanceOf(Model\Complex::class, $payload->getComplex());
        Assert::assertEquals('bar', $payload->getComplex()->getFoo());
        Assert::assertInstanceOf(LocalDate::class, $payload->getDate());
        Assert::assertEquals('2015-05-01', $payload->getDate()->toString());
        Assert::assertInstanceOf(LocalDateTime::class, $payload->getDateTime());
        Assert::assertEquals('2015-05-01T13:37:14Z', $payload->getDateTime()->toString());
        Assert::assertInstanceOf(Period::class, $payload->getDuration());
        Assert::assertEquals('P1M', $payload->getDuration()->toString());
        Assert::assertIsFloat($payload->getFloat());
        Assert::assertEquals(13.37, $payload->getFloat());
        Assert::assertIsInt($payload->getInteger());
        Assert::assertEquals(7, $payload->getInteger());
        Assert::assertIsString($payload->getString());
        Assert::assertEquals('bar', $payload->getString());
        Assert::assertInstanceOf(LocalTime::class, $payload->getTime());
        Assert::assertEquals('13:37:14', $payload->getTime()->toString());

        return $payload;
    }
}
