<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Model\Passthru;
use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\LocalTime;
use PSX\DateTime\Period;
use PSX\Framework\Controller\ControllerAbstract;

/**
 * PassthruController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Path('/tests/passthru')]
class PassthruController extends ControllerAbstract
{
    #[Get]
    public function doGet(): mixed
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
            'date' => LocalDate::parse('2015-05-01'),
            'dateTime' => LocalDateTime::parse('2015-05-01T13:37:14'),
            'duration' => Period::parse('P1M'),
            'float' => 13.37,
            'integer' => 7,
            'string' => 'bar',
            'time' => LocalTime::parse('13:37:14'),
        ];
    }

    #[Post]
    #[Incoming(schema: Passthru::class)]
    public function doPost($payload): mixed
    {
        Assert::assertInstanceOf('stdClass', $payload->any);
        Assert::assertEquals(['foo' => 'bar'], (array) $payload->any);
        Assert::assertIsArray($payload->array);
        Assert::assertEquals(1, count($payload->array));
        Assert::assertEquals(['bar'], $payload->array);
        Assert::assertIsArray($payload->arrayComplex);
        Assert::assertEquals(2, count($payload->arrayComplex));
        Assert::assertInstanceOf('stdClass', $payload->arrayComplex[0]);
        Assert::assertEquals(['foo' => 'bar'], (array) $payload->arrayComplex[0]);
        Assert::assertInstanceOf('stdClass', $payload->arrayComplex[1]);
        Assert::assertEquals(['foo' => 'foo'], (array) $payload->arrayComplex[1]);
        Assert::assertIsArray($payload->arrayChoice);
        Assert::assertEquals(3, count($payload->arrayChoice));
        Assert::assertInstanceOf('stdClass', $payload->arrayChoice[0]);
        Assert::assertEquals(['foo' => 'baz'], (array) $payload->arrayChoice[0]);
        Assert::assertInstanceOf('stdClass', $payload->arrayChoice[1]);
        Assert::assertEquals(['bar' => 'bar'], (array) $payload->arrayChoice[1]);
        Assert::assertInstanceOf('stdClass', $payload->arrayChoice[2]);
        Assert::assertEquals(['foo' => 'foo'], (array) $payload->arrayChoice[2]);
        Assert::assertIsBool($payload->boolean);
        Assert::assertEquals(true, $payload->boolean);
        Assert::assertInstanceOf('stdClass', $payload->choice);
        Assert::assertEquals(['bar' => 'test'], (array) $payload->choice);
        Assert::assertInstanceOf('stdClass', $payload->complex);
        Assert::assertEquals(['foo' => 'bar'], (array) $payload->complex);
        Assert::assertEquals('2015-05-01', $payload->date);
        Assert::assertEquals('2015-05-01T13:37:14Z', $payload->dateTime);
        Assert::assertEquals('P1M', $payload->duration);
        Assert::assertIsFloat($payload->float);
        Assert::assertEquals(13.37, $payload->float);
        Assert::assertIsInt($payload->integer);
        Assert::assertEquals(7, $payload->integer);
        Assert::assertIsString($payload->string);
        Assert::assertEquals('bar', $payload->string);
        Assert::assertEquals('13:37:14', $payload->time);

        return $payload;
    }
}
