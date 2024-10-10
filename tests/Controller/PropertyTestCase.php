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

namespace PSX\Framework\Tests\Controller;

use PHPUnit\Framework\Assert;
use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\LocalTime;
use PSX\DateTime\Period;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Tests\Controller\Foo\Model\Any;
use PSX\Framework\Tests\Controller\Foo\Model\ChoiceA;
use PSX\Framework\Tests\Controller\Foo\Model\ChoiceB;
use PSX\Framework\Tests\Controller\Foo\Model\Complex;
use PSX\Framework\Tests\Controller\Foo\Model\Property;
use PSX\Record\Record;
use PSX\Record\RecordInterface;

/**
 * PropertyTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class PropertyTestCase extends ControllerTestCase
{
    /**
     * @dataProvider getDataTypes
     */
    public function testGet($type)
    {
        $response = $this->sendRequest($this->getPath() . '/1?type=' . $type, 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString(self::getExpected(), $body, $body);
    }

    public function testPost()
    {
        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], self::getExpected());
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString(self::getExpected(), $body, $body);
    }

    public function testPostInvalidAny()
    {
        $data = <<<JSON
{
    "any": {
        "foo": {
        }
    }
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/any/foo must be of type string', substr($data->message, 0, 31), $body);
    }

    public function testPostInvalidArray()
    {
        $data = <<<JSON
{
    "array": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/array must be of type array', substr($data->message, 0, 28), $body);
    }

    public function testPostInvalidArrayComplex()
    {
        $data = <<<JSON
{
    "arrayComplex": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/arrayComplex must be of type array', substr($data->message, 0, 35), $body);
    }

    public function testPostInvalidBoolean()
    {
        $data = <<<JSON
{
    "boolean": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/boolean must be of type boolean', substr($data->message, 0, 32), $body);
    }

    public function testPostInvalidDateTime()
    {
        $data = <<<JSON
{
    "dateTime": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/dateTime must be a valid date-time format [RFC3339]', substr($data->message, 0, 52), $body);
    }

    public function testPostInvalidDate()
    {
        $data = <<<JSON
{
    "date": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/date must be a valid full-date format [RFC3339]', substr($data->message, 0, 48), $body);
    }

    public function testPostInvalidFloat()
    {
        $data = <<<JSON
{
    "float": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/float must be of type float', substr($data->message, 0, 28), $body);
    }

    public function testPostInvalidInteger()
    {
        $data = <<<JSON
{
    "integer": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/integer must be of type integer', substr($data->message, 0, 32), $body);
    }

    public function testPostInvalidString()
    {
        $data = <<<JSON
{
    "string": []
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/string must be of type string', substr($data->message, 0, 30), $body);
    }

    public function testPostInvalidTime()
    {
        $data = <<<JSON
{
    "time": "foo"
}
JSON;

        $response = $this->sendRequest($this->getPath() . '/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(400, $response->getStatusCode(), $body);
        $this->assertEquals('/time must be a valid full-time format [RFC3339]', substr($data->message, 0, 48), $body);
    }

    /**
     * Checks whether the data we received as post is converted to the right types
     */
    public static function assertRecord(RecordInterface $record): void
    {
        Assert::assertInstanceOf(RecordInterface::class, $record->any);
        Assert::assertEquals(['foo' => 'bar'], $record->any->getAll());
        Assert::assertIsArray($record->array);
        Assert::assertEquals(1, count($record->array));
        Assert::assertEquals(['bar'], $record->array);
        Assert::assertIsArray($record->arrayComplex);
        Assert::assertEquals(2, count($record->arrayComplex));
        Assert::assertInstanceOf(RecordInterface::class, $record->arrayComplex[0]);
        Assert::assertEquals(['foo' => 'bar'], $record->arrayComplex[0]->getAll());
        Assert::assertInstanceOf(RecordInterface::class, $record->arrayComplex[1]);
        Assert::assertEquals(['foo' => 'foo'], $record->arrayComplex[1]->getAll());
        Assert::assertIsBool($record->boolean);
        Assert::assertEquals(true, $record->boolean);
        Assert::assertInstanceOf(RecordInterface::class, $record->complex);
        Assert::assertEquals(['foo' => 'bar'], $record->complex->getAll());
        Assert::assertInstanceOf(LocalDate::class, $record->date);
        Assert::assertEquals('2015-05-01', $record->date->toString());
        Assert::assertInstanceOf(LocalDateTime::class, $record->dateTime);
        Assert::assertEquals('2015-05-01T13:37:14Z', $record->dateTime->toString());
        Assert::assertIsFloat($record->float);
        Assert::assertEquals(13.37, $record->float);
        Assert::assertIsInt($record->integer);
        Assert::assertEquals(7, $record->integer);
        Assert::assertIsString($record->string);
        Assert::assertEquals('bar', $record->string);
        Assert::assertInstanceOf(LocalTime::class, $record->time);
        Assert::assertEquals('13:37:14', $record->time->toString());
    }

    /**
     * Returns all available data types which can be used as data provider
     */
    public static function getDataTypes(): array
    {
        return [
            [1],
            [2],
            [3],
            [4],
        ];
    }

    /**
     * Returns different responses. The assimilator should convert all these types to the same response format
     */
    public static function getDataByType(int $type): array|\stdClass|RecordInterface|Property
    {
        switch ($type) {
            case 1:
                return self::getArrayStructure();

            case 2:
                return self::getObjectStructure();

            case 3:
                return self::getRecordStructure();

            case 4:
                return self::getPopoStructure();
        }

        throw new \InvalidArgumentException('Provided an invalid type');
    }

    private static function getArrayStructure(): array
    {
        return [
            'any' => [
                'foo' => 'bar'
            ],
            'array' => ['bar'],
            'arrayComplex' => [[
                'foo' => 'bar'
            ],[
                'foo' => 'foo'
            ]],
            'boolean' => true,
            'complex' => [
                'foo' => 'bar'
            ],
            'date' => LocalDate::of(2015, 5, 1),
            'dateTime' => LocalDateTime::of(2015, 5, 1, 13, 37, 14),
            'float' => 13.37,
            'integer' => 7,
            'string' => 'bar',
            'time' => LocalTime::of(13, 37, 14),
        ];
    }

    private static function getObjectStructure(): \stdClass
    {
        return (object) [
            'any' => (object) [
                'foo' => 'bar'
            ],
            'array' => ['bar'],
            'arrayComplex' => [(object) [
                'foo' => 'bar'
            ], (object) [
                'foo' => 'foo'
            ]],
            'boolean' => true,
            'complex' => (object) [
                'foo' => 'bar'
            ],
            'date' => LocalDate::of(2015, 5, 1),
            'dateTime' => LocalDateTime::of(2015, 5, 1, 13, 37, 14),
            'float' => 13.37,
            'integer' => 7,
            'string' => 'bar',
            'time' => LocalTime::of(13, 37, 14),
        ];
    }

    private static function getRecordStructure(): RecordInterface
    {
        return Record::fromArray([
            'any' => Record::fromArray([
                'foo' => 'bar'
            ]),
            'array' => ['bar'],
            'arrayComplex' => [
                Record::fromArray([
                    'foo' => 'bar'
                ]),
                Record::fromArray([
                    'foo' => 'foo'
                ])
            ],
            'boolean' => true,
            'complex' => Record::fromArray([
                'foo' => 'bar'
            ]),
            'date' => LocalDate::of(2015, 5, 1),
            'dateTime' => LocalDateTime::of(2015, 5, 1, 13, 37, 14),
            'float' => 13.37,
            'integer' => 7,
            'string' => 'bar',
            'time' => LocalTime::of(13, 37, 14),
        ]);
    }

    private static function getPopoStructure(): Property
    {
        $any = new Any();
        $any['foo'] = 'bar';

        $object = new Property();
        $object->setAny($any);
        $object->setArray(['bar']);
        $object->setArrayComplex([new ChoiceA('bar'), new ChoiceA('foo')]);
        $object->setBoolean(true);
        $object->setComplex(new Complex('bar'));
        $object->setDate(LocalDate::of(2015, 5, 1));
        $object->setDateTime(LocalDateTime::of(2015, 5, 1, 13, 37, 14));
        $object->setFloat(13.37);
        $object->setInteger(7);
        $object->setString('bar');
        $object->setTime(LocalTime::of(13, 37, 14));

        return $object;
    }

    /**
     * The JSON format which we expect as response
     */
    public static function getExpected(): string
    {
        return <<<JSON
{
    "any": {
        "foo": "bar"
    },
    "array": [
        "bar"
    ],
    "arrayComplex": [{
        "foo": "bar"
    },{
        "foo": "foo"
    }],
    "boolean": true,
    "complex": {
        "foo": "bar"
    },
    "date": "2015-05-01",
    "dateTime": "2015-05-01T13:37:14Z",
    "float": 13.37,
    "integer": 7,
    "string": "bar",
    "time": "13:37:14"
}
JSON;
    }

    abstract protected function getPath(): string;
}
