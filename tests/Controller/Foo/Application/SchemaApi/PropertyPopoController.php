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

namespace PSX\Framework\Tests\Controller\Foo\Application\SchemaApi;

use PHPUnit\Framework\Assert;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\Foo\Model;
use PSX\Framework\Tests\Controller\SchemaApi\PropertyTestCase;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Record\RecordInterface;

/**
 * PropertyPopoController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
#[PathParam(name: 'id', type: 'integer')]
class PropertyPopoController extends ControllerAbstract
{
    use PropertyControllerTrait;

    #[QueryParam(name: 'type', type: 'integer')]
    #[Outgoing(code: 200, schema: Model\Property::class)]
    protected function doGet(HttpContextInterface $context): mixed
    {
        Assert::assertEquals(1, $context->getUriFragment('id'));

        return PropertyTestCase::getDataByType($context->getParameter('type'));
    }

    #[Incoming(schema: Model\Property::class)]
    #[Outgoing(code: 200, schema: Model\Property::class)]
    protected function doPost($record, HttpContextInterface $context): mixed
    {
        /** @var \PSX\Framework\Tests\Controller\Foo\Model\Property $record */
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
        Assert::assertInstanceOf(Date::class, $record->getDate());
        Assert::assertEquals('2015-05-01', $record->getDate()->format('Y-m-d'));
        Assert::assertInstanceOf(DateTime::class, $record->getDateTime());
        Assert::assertEquals('2015-05-01T13:37:14Z', $record->getDateTime()->format('Y-m-d\TH:i:s\Z'));
        Assert::assertInstanceOf(Duration::class, $record->getDuration());
        Assert::assertEquals('000100000000', $record->getDuration()->format('%Y%M%D%H%I%S'));
        Assert::assertIsFloat($record->getFloat());
        Assert::assertEquals(13.37, $record->getFloat());
        Assert::assertIsInt($record->getInteger());
        Assert::assertEquals(7, $record->getInteger());
        Assert::assertIsString($record->getString());
        Assert::assertEquals('bar', $record->getString());
        Assert::assertInstanceOf(Time::class, $record->getTime());
        Assert::assertEquals('13:37:14', $record->getTime()->format('H:i:s'));

        return $record;
    }
}
