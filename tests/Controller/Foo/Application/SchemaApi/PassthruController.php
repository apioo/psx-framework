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
use PSX\Http\Environment\HttpContextInterface;

/**
 * PassthruController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PassthruController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    /**
     * @Outgoing(code=200, schema="PSX\Framework\Schema\Passthru")
     */
    protected function doGet(HttpContextInterface $context)
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
    }

    /**
     * @Incoming(schema="PSX\Framework\Schema\Passthru")
     * @Outgoing(code=200, schema="PSX\Framework\Schema\Passthru")
     */
    protected function doPost($record, HttpContextInterface $context)
    {
        $this->testCase->assertInstanceOf('stdClass', $record->any);
        $this->testCase->assertEquals(['foo' => 'bar'], (array) $record->any);
        $this->testCase->assertInternalType('array', $record->array);
        $this->testCase->assertEquals(1, count($record->array));
        $this->testCase->assertEquals(['bar'], $record->array);
        $this->testCase->assertInternalType('array', $record->arrayComplex);
        $this->testCase->assertEquals(2, count($record->arrayComplex));
        $this->testCase->assertInstanceOf('stdClass', $record->arrayComplex[0]);
        $this->testCase->assertEquals(['foo' => 'bar'], (array) $record->arrayComplex[0]);
        $this->testCase->assertInstanceOf('stdClass', $record->arrayComplex[1]);
        $this->testCase->assertEquals(['foo' => 'foo'], (array) $record->arrayComplex[1]);
        $this->testCase->assertInternalType('array', $record->arrayChoice);
        $this->testCase->assertEquals(3, count($record->arrayChoice));
        $this->testCase->assertInstanceOf('stdClass', $record->arrayChoice[0]);
        $this->testCase->assertEquals(['foo' => 'baz'], (array) $record->arrayChoice[0]);
        $this->testCase->assertInstanceOf('stdClass', $record->arrayChoice[1]);
        $this->testCase->assertEquals(['bar' => 'bar'], (array) $record->arrayChoice[1]);
        $this->testCase->assertInstanceOf('stdClass', $record->arrayChoice[2]);
        $this->testCase->assertEquals(['foo' => 'foo'], (array) $record->arrayChoice[2]);
        $this->testCase->assertInternalType('boolean', $record->boolean);
        $this->testCase->assertEquals(true, $record->boolean);
        $this->testCase->assertInstanceOf('stdClass', $record->choice);
        $this->testCase->assertEquals(['foo' => 'bar'], (array) $record->complex);
        $this->testCase->assertInstanceOf('stdClass', $record->complex);
        $this->testCase->assertEquals(['foo' => 'bar'], (array) $record->complex);
        $this->testCase->assertEquals('2015-05-01', $record->date);
        $this->testCase->assertEquals('2015-05-01T13:37:14Z', $record->dateTime);
        $this->testCase->assertEquals('P1M', $record->duration);
        $this->testCase->assertInternalType('float', $record->float);
        $this->testCase->assertEquals(13.37, $record->float);
        $this->testCase->assertInternalType('integer', $record->integer);
        $this->testCase->assertEquals(7, $record->integer);
        $this->testCase->assertInternalType('string', $record->string);
        $this->testCase->assertEquals('bar', $record->string);
        $this->testCase->assertEquals('13:37:14', $record->time);

        return $record;
    }
}
