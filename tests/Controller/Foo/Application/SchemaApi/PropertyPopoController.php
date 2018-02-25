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

use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Tests\Controller\SchemaApi\PropertyTestCase;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Framework\Tests\Controller\Foo\Model;
use PSX\Record\RecordInterface;

/**
 * PropertyPopoController
 *
 * @PathParam(name="id", type="integer")
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PropertyPopoController extends SchemaApiAbstract
{
    use PropertyControllerTrait;

    /**
     * @QueryParam(name="type", type="integer")
     * @Outgoing(code=200, schema="PSX\Framework\Tests\Controller\Foo\Model\Property")
     */
    protected function doGet(HttpContextInterface $context)
    {
        $this->testCase->assertEquals(1, $context->getUriFragment('id'));

        return PropertyTestCase::getDataByType($context->getParameter('type'));
    }

    /**
     * @Incoming(schema="PSX\Framework\Tests\Controller\Foo\Model\Property")
     * @Outgoing(code=200, schema="PSX\Framework\Tests\Controller\Foo\Model\Property")
     */
    protected function doPost($record, HttpContextInterface $context)
    {
        /** @var \PSX\Framework\Tests\Controller\Foo\Model\Property $record */
        $this->testCase->assertInstanceOf(Model\Property::class, $record);
        $this->testCase->assertInstanceOf(RecordInterface::class, $record->getAny());
        $this->testCase->assertEquals('bar', $record->getAny()['foo']);
        $this->testCase->assertInternalType('array', $record->getArray());
        $this->testCase->assertEquals(1, count($record->getArray()));
        $this->testCase->assertEquals(['bar'], $record->getArray());
        $this->testCase->assertInternalType('array', $record->getArrayComplex());
        $this->testCase->assertEquals(2, count($record->getArrayComplex()));
        $this->testCase->assertInstanceOf(Model\Complex::class, $record->getArrayComplex()[0]);
        $this->testCase->assertEquals('bar', $record->getArrayComplex()[0]->getFoo());
        $this->testCase->assertInstanceOf(Model\Complex::class, $record->getArrayComplex()[1]);
        $this->testCase->assertEquals('foo', $record->getArrayComplex()[1]->getFoo());
        $this->testCase->assertInternalType('array', $record->getArrayChoice());
        $this->testCase->assertEquals(3, count($record->getArrayChoice()));
        $this->testCase->assertInstanceOf(Model\ChoiceA::class, $record->getArrayChoice()[0]);
        $this->testCase->assertEquals('baz', $record->getArrayChoice()[0]->getFoo());
        $this->testCase->assertInstanceOf(Model\ChoiceB::class, $record->getArrayChoice()[1]);
        $this->testCase->assertEquals('bar', $record->getArrayChoice()[1]->getBar());
        $this->testCase->assertInstanceOf(Model\ChoiceA::class, $record->getArrayChoice()[2]);
        $this->testCase->assertEquals('foo', $record->getArrayChoice()[2]->getFoo());
        $this->testCase->assertInternalType('boolean', $record->getBoolean());
        $this->testCase->assertEquals(true, $record->getBoolean());
        $this->testCase->assertInstanceOf(Model\ChoiceB::class, $record->getChoice());
        $this->testCase->assertEquals('bar', $record->getComplex()->getFoo());
        $this->testCase->assertInstanceOf(Model\Complex::class, $record->getComplex());
        $this->testCase->assertEquals('bar', $record->getComplex()->getFoo());
        $this->testCase->assertInstanceOf(Date::class, $record->getDate());
        $this->testCase->assertEquals('2015-05-01', $record->getDate()->format('Y-m-d'));
        $this->testCase->assertInstanceOf(DateTime::class, $record->getDateTime());
        $this->testCase->assertEquals('2015-05-01T13:37:14Z', $record->getDateTime()->format('Y-m-d\TH:i:s\Z'));
        $this->testCase->assertInstanceOf(Duration::class, $record->getDuration());
        $this->testCase->assertEquals('000100000000', $record->getDuration()->format('%Y%M%D%H%I%S'));
        $this->testCase->assertInternalType('float', $record->getFloat());
        $this->testCase->assertEquals(13.37, $record->getFloat());
        $this->testCase->assertInternalType('integer', $record->getInteger());
        $this->testCase->assertEquals(7, $record->getInteger());
        $this->testCase->assertInternalType('string', $record->getString());
        $this->testCase->assertEquals('bar', $record->getString());
        $this->testCase->assertInstanceOf(Time::class, $record->getTime());
        $this->testCase->assertEquals('13:37:14', $record->getTime()->format('H:i:s'));

        return $record;
    }
}
