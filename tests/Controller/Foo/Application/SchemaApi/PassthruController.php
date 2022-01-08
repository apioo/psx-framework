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

namespace PSX\Framework\Tests\Controller\Foo\Application\SchemaApi;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Schema\Passthru;
use PSX\Http\Environment\HttpContextInterface;

/**
 * PassthruController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PassthruController extends ControllerAbstract
{
    #[Outgoing(code: 200, schema: Passthru::class)]
    protected function doGet(HttpContextInterface $context): array
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
            'date' => new Date('2015-05-01'),
            'dateTime' => new DateTime('2015-05-01T13:37:14'),
            'duration' => new Duration('P1M'),
            'float' => 13.37,
            'integer' => 7,
            'string' => 'bar',
            'time' => new Time('13:37:14'),
        ];
    }

    #[Incoming(schema: Passthru::class)]
    #[Outgoing(code: 200, schema: Passthru::class)]
    protected function doPost($record, HttpContextInterface $context): \stdClass
    {
        Assert::assertInstanceOf('stdClass', $record->any);
        Assert::assertEquals(['foo' => 'bar'], (array) $record->any);
        Assert::assertIsArray($record->array);
        Assert::assertEquals(1, count($record->array));
        Assert::assertEquals(['bar'], $record->array);
        Assert::assertIsArray($record->arrayComplex);
        Assert::assertEquals(2, count($record->arrayComplex));
        Assert::assertInstanceOf('stdClass', $record->arrayComplex[0]);
        Assert::assertEquals(['foo' => 'bar'], (array) $record->arrayComplex[0]);
        Assert::assertInstanceOf('stdClass', $record->arrayComplex[1]);
        Assert::assertEquals(['foo' => 'foo'], (array) $record->arrayComplex[1]);
        Assert::assertIsArray($record->arrayChoice);
        Assert::assertEquals(3, count($record->arrayChoice));
        Assert::assertInstanceOf('stdClass', $record->arrayChoice[0]);
        Assert::assertEquals(['foo' => 'baz'], (array) $record->arrayChoice[0]);
        Assert::assertInstanceOf('stdClass', $record->arrayChoice[1]);
        Assert::assertEquals(['bar' => 'bar'], (array) $record->arrayChoice[1]);
        Assert::assertInstanceOf('stdClass', $record->arrayChoice[2]);
        Assert::assertEquals(['foo' => 'foo'], (array) $record->arrayChoice[2]);
        Assert::assertIsBool($record->boolean);
        Assert::assertEquals(true, $record->boolean);
        Assert::assertInstanceOf('stdClass', $record->choice);
        Assert::assertEquals(['bar' => 'test'], (array) $record->choice);
        Assert::assertInstanceOf('stdClass', $record->complex);
        Assert::assertEquals(['foo' => 'bar'], (array) $record->complex);
        Assert::assertEquals('2015-05-01', $record->date);
        Assert::assertEquals('2015-05-01T13:37:14Z', $record->dateTime);
        Assert::assertEquals('P1M', $record->duration);
        Assert::assertIsFloat($record->float);
        Assert::assertEquals(13.37, $record->float);
        Assert::assertIsInt($record->integer);
        Assert::assertEquals(7, $record->integer);
        Assert::assertIsString($record->string);
        Assert::assertEquals('bar', $record->string);
        Assert::assertEquals('13:37:14', $record->time);

        return $record;
    }
}
