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

namespace PSX\Framework\Tests\Controller\Foo\Schema;

use PSX\Schema\Property as SchemaProperty;
use PSX\Schema\SchemaAbstract;

/**
 * Property
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Property extends SchemaAbstract
{
    public function getDefinition()
    {
        $sb = $this->getSchemaBuilder('choiceA');
        $sb->string('foo');
        $sb->setAdditionalProperties(false);
        $complexA = $sb->getProperty();

        $sb = $this->getSchemaBuilder('choiceB');
        $sb->string('bar');
        $sb->setAdditionalProperties(false);
        $complexB = $sb->getProperty();

        $choice = SchemaProperty::get()->setOneOf([$complexA, $complexB]);

        $sb = $this->getSchemaBuilder('complex');
        $sb->string('foo');
        $sb->setAdditionalProperties(false);
        $complex = $sb->getProperty();

        $sb = $this->getSchemaBuilder('any');
        $sb->setAdditionalProperties(SchemaProperty::getString('foo'));
        $any = $sb->getProperty();

        $sb = $this->getSchemaBuilder('property');
        $sb->objectType('any', $any);
        $sb->arrayType('array')->setItems(SchemaProperty::getString('foo'));
        $sb->arrayType('arrayComplex')->setItems($complex);
        $sb->arrayType('arrayChoice')->setItems($choice);
        $sb->boolean('boolean');
        $sb->add('choice', SchemaProperty::get()->setOneOf([$complexA, $complexB]));
        $sb->objectType('complex', $complex);
        $sb->date('date');
        $sb->dateTime('dateTime');
        $sb->duration('duration');
        $sb->number('float');
        $sb->integer('integer');
        $sb->string('string');
        $sb->time('time');
        $sb->setAdditionalProperties(false);

        return $sb->getProperty();
    }
}
