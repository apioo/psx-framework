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

namespace PSX\Framework\Tests;

use PHPUnit\Framework\TestCase;
use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\LocalTime;
use PSX\Http\MediaType;
use PSX\Uri\Uri;
use PSX\Uri\Url;
use PSX\Uri\Urn;

/**
 * ValueObjectTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ValueObjectTest extends TestCase
{
    /**
     * This test ensures that the toString method of an value object creates an
     * string representation containing all components from the state. And that
     * this string can be parsed by the same object without lossing any
     * information
     */
    public function testObject()
    {
        $objects = $this->getObjects();

        foreach ($objects as $object) {
            $voClass = new \ReflectionClass(get_class($object));
            $newObject = $voClass->getMethod('parse')->invoke(null, $object->toString());

            $this->assertEquals($object->toString(), $newObject->toString());
            $this->assertEquals($object->toString(), (string) $newObject);
        }
    }

    protected function getObjects()
    {
        return [
            Uri::parse('foo://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose'),
            Urn::parse('urn:uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6'),
            Url::parse('http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker'),
            MediaType::parse('text/plain; q=0.5'),
            LocalDate::parse('2015-04-25'),
            LocalTime::parse('19:35:20.1234+01:00'),
            LocalDateTime::parse('2015-04-25T19:35:20.1234+01:00'),
        ];
    }
}
