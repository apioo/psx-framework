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

namespace PSX\Framework\Tests\Controller\SchemaApi;

use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Framework\Test\ControllerTestCase;
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
 * @link    http://phpsx.org
 */
abstract class PropertyTestCase extends ControllerTestCase
{
    /**
     * @dataProvider getDataTypes
     */
    public function testGet($type)
    {
        $response = $this->sendRequest('/api/1?type=' . $type, 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString(self::getExpected(), $body, $body);
    }

    public function testPost()
    {
        $response = $this->sendRequest('/api/1', 'POST', [], self::getExpected());
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

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/any/foo must be of type string', substr($data->message, 0, 31), $body);
    }

    public function testPostInvalidArray()
    {
        $data = <<<JSON
{
    "array": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/array must be of type array', substr($data->message, 0, 28), $body);
    }

    public function testPostInvalidArrayComplex()
    {
        $data = <<<JSON
{
    "arrayComplex": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/arrayComplex must be of type array', substr($data->message, 0, 35), $body);
    }

    public function testPostInvalidArrayChoice()
    {
        $data = <<<JSON
{
    "arrayChoice": [{
        "foo": "baz"
    },{
        "baz": "bar"
    },{
        "foo": "foo"
    }]
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/arrayChoice/1 must match one required schema', substr($data->message, 0, 45), $body);
    }

    public function testPostInvalidBoolean()
    {
        $data = <<<JSON
{
    "boolean": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/boolean must be of type boolean', substr($data->message, 0, 32), $body);
    }

    public function testPostInvalidChoice()
    {
        $data = <<<JSON
{
    "choice": {
        "baz": "test"
    }
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/choice must match one required schema', substr($data->message, 0, 38), $body);
    }

    public function testPostInvalidComplex()
    {
        $data = <<<JSON
{
    "complex": {
        "baz": "test"
    }
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/complex property "baz" is not allowed', substr($data->message, 0, 38), $body);
    }

    public function testPostInvalidDateTime()
    {
        $data = <<<JSON
{
    "dateTime": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/dateTime must be a valid date-time format [RFC3339]', substr($data->message, 0, 52), $body);
    }

    public function testPostInvalidDate()
    {
        $data = <<<JSON
{
    "date": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/date must be a valid full-date format [RFC3339]', substr($data->message, 0, 48), $body);
    }

    public function testPostInvalidDuration()
    {
        $data = <<<JSON
{
    "duration": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/duration must be a valid duration format [ISO8601]', substr($data->message, 0, 51), $body);
    }

    public function testPostInvalidFloat()
    {
        $data = <<<JSON
{
    "float": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/float must be of type number', substr($data->message, 0, 29), $body);
    }

    public function testPostInvalidInteger()
    {
        $data = <<<JSON
{
    "integer": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/integer must be of type integer', substr($data->message, 0, 32), $body);
    }

    public function testPostInvalidString()
    {
        $data = <<<JSON
{
    "string": []
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/string must be of type string', substr($data->message, 0, 30), $body);
    }

    public function testPostInvalidTime()
    {
        $data = <<<JSON
{
    "time": "foo"
}
JSON;

        $response = $this->sendRequest('/api/1', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/time must be a valid full-time format [RFC3339]', substr($data->message, 0, 48), $body);
    }

    /**
     * Checks whether the data we received as post is converted to the right
     * types
     *
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param \PSX\Record\RecordInterface $record
     */
    public static function assertRecord(\PHPUnit_Framework_TestCase $testCase, RecordInterface $record)
    {
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->any);
        $testCase->assertEquals(['foo' => 'bar'], $record->any->getProperties());
        $testCase->assertInternalType('array', $record->array);
        $testCase->assertEquals(1, count($record->array));
        $testCase->assertEquals(['bar'], $record->array);
        $testCase->assertInternalType('array', $record->arrayComplex);
        $testCase->assertEquals(2, count($record->arrayComplex));
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->arrayComplex[0]);
        $testCase->assertEquals(['foo' => 'bar'], $record->arrayComplex[0]->getProperties());
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->arrayComplex[1]);
        $testCase->assertEquals(['foo' => 'foo'], $record->arrayComplex[1]->getProperties());
        $testCase->assertInternalType('array', $record->arrayChoice);
        $testCase->assertEquals(3, count($record->arrayChoice));
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->arrayChoice[0]);
        $testCase->assertEquals(['foo' => 'baz'], $record->arrayChoice[0]->getProperties());
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->arrayChoice[1]);
        $testCase->assertEquals(['bar' => 'bar'], $record->arrayChoice[1]->getProperties());
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->arrayChoice[2]);
        $testCase->assertEquals(['foo' => 'foo'], $record->arrayChoice[2]->getProperties());
        $testCase->assertInternalType('boolean', $record->boolean);
        $testCase->assertEquals(true, $record->boolean);
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->choice);
        $testCase->assertEquals(['foo' => 'bar'], $record->complex->getProperties());
        $testCase->assertInstanceOf('PSX\Record\RecordInterface', $record->complex);
        $testCase->assertEquals(['foo' => 'bar'], $record->complex->getProperties());
        $testCase->assertInstanceOf('PSX\DateTime\Date', $record->date);
        $testCase->assertEquals('2015-05-01', $record->date->format('Y-m-d'));
        $testCase->assertInstanceOf('DateTime', $record->dateTime);
        $testCase->assertEquals('2015-05-01T13:37:14Z', $record->dateTime->format('Y-m-d\TH:i:s\Z'));
        $testCase->assertInstanceOf('PSX\DateTime\Duration', $record->duration);
        $testCase->assertEquals('000100000000', $record->duration->format('%Y%M%D%H%I%S'));
        $testCase->assertInternalType('float', $record->float);
        $testCase->assertEquals(13.37, $record->float);
        $testCase->assertInternalType('integer', $record->integer);
        $testCase->assertEquals(7, $record->integer);
        $testCase->assertInternalType('string', $record->string);
        $testCase->assertEquals('bar', $record->string);
        $testCase->assertInstanceOf('PSX\DateTime\Time', $record->time);
        $testCase->assertEquals('13:37:14', $record->time->format('H:i:s'));
    }

    /**
     * Returns all available data types which can be used as data provider
     *
     * @return array
     */
    public static function getDataTypes()
    {
        return [
            [1],
            [2],
            [3],
            [4],
        ];
    }

    /**
     * Returns different responses. The assimilator should convert all these
     * types to the same response format
     *
     * @param integer $type
     * @return array
     */
    public static function getDataByType($type)
    {
        switch ($type) {
            case 1:
                // we return actual types
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
                    'arrayChoice' => [[
                        'foo' => 'baz'
                    ],[
                        'bar' => 'bar'
                    ],[
                        'foo' => 'foo'
                    ]],
                    'boolean' => true,
                    'choice' => [
                        'bar' => 'test'
                    ],
                    'complex' => [
                        'foo' => 'bar'
                    ],
                    'date' => new Date(2015, 5, 1),
                    'dateTime' => new DateTime(2015, 5, 1, 13, 37, 14),
                    'duration' => new Duration('P1M'),
                    'float' => 13.37,
                    'integer' => 7,
                    'string' => 'bar',
                    'time' => new Time(13, 37, 14),
                ];
                break;

            case 2:
                // we return stdClass
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
                    'arrayChoice' => [(object) [
                        'foo' => 'baz'
                    ], (object) [
                        'bar' => 'bar'
                    ], (object) [
                        'foo' => 'foo'
                    ]],
                    'boolean' => true,
                    'choice' => (object) [
                        'bar' => 'test'
                    ],
                    'complex' => (object) [
                        'foo' => 'bar'
                    ],
                    'date' => new Date(2015, 5, 1),
                    'dateTime' => new DateTime(2015, 5, 1, 13, 37, 14),
                    'duration' => new Duration('P1M'),
                    'float' => 13.37,
                    'integer' => 7,
                    'string' => 'bar',
                    'time' => new Time(13, 37, 14),
                ];
                break;

            case 3:
                // we return records
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
                    'arrayChoice' => [
                        Record::fromArray([
                            'foo' => 'baz'
                        ]),
                        Record::fromArray([
                            'bar' => 'bar'
                        ]),
                        Record::fromArray([
                            'foo' => 'foo'
                        ])
                    ],
                    'boolean' => true,
                    'choice' => Record::fromArray([
                        'bar' => 'test'
                    ]),
                    'complex' => Record::fromArray([
                        'foo' => 'bar'
                    ]),
                    'date' => new Date(2015, 5, 1),
                    'dateTime' => new DateTime(2015, 5, 1, 13, 37, 14),
                    'duration' => new Duration('P1M'),
                    'float' => 13.37,
                    'integer' => 7,
                    'string' => 'bar',
                    'time' => new Time(13, 37, 14),
                ]);
                break;

            case 4:
                // we return POPOs
                $object = new Property();
                $object->setAny(new ChoiceA('bar'));
                $object->setArray(['bar']);
                $object->setArrayComplex([new ChoiceA('bar'), new ChoiceA('foo')]);
                $object->setArrayChoice([new ChoiceA('baz'), new ChoiceB('bar'), new ChoiceA('foo')]);
                $object->setBoolean(true);
                $object->setChoice(new ChoiceB('test'));
                $object->setComplex(new Complex('bar'));
                $object->setDate(new Date(2015, 5, 1));
                $object->setDateTime(new DateTime(2015, 5, 1, 13, 37, 14));
                $object->setDuration(new Duration('P1M'));
                $object->setFloat(13.37);
                $object->setInteger(7);
                $object->setString('bar');
                $object->setTime(new Time(13, 37, 14));

                return $object;
                break;
        }
    }

    /**
     * The JSON format which we expect as response
     *
     * @return string
     */
    public static function getExpected()
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
    "arrayChoice": [{
        "foo": "baz"
    },{
        "bar": "bar"
    },{
        "foo": "foo"
    }],
    "boolean": true,
    "choice": {
        "bar": "test"
    },
    "complex": {
        "foo": "bar"
    },
    "date": "2015-05-01",
    "dateTime": "2015-05-01T13:37:14Z",
    "duration": "P1M",
    "float": 13.37,
    "integer": 7,
    "string": "bar",
    "time": "13:37:14"
}
JSON;
    }
}
