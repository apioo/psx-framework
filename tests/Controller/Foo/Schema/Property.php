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

namespace PSX\Framework\Tests\Controller\Foo\Schema;

use PSX\Schema\SchemaAbstract;
use PSX\Schema\TypeFactory;

/**
 * Property
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Property extends SchemaAbstract
{
    protected function build(): void
    {
        $any = $this->newMap('Any');
        $any->setAdditionalProperties(TypeFactory::getString());

        $complexA = $this->newStruct('ChoiceA');
        $complexA->addString('foo');
        $complexA->setRequired(['foo']);

        $complexB = $this->newStruct('ChoiceB');
        $complexB->addString('bar');
        $complexB->setRequired(['bar']);

        $complex = $this->newStruct('Complex');
        $complex->addString('foo');

        $sb = $this->newStruct('Property');
        $sb->addReference('any', 'Any');
        $sb->addArray('array', TypeFactory::getString());
        $sb->addArray('arrayComplex', TypeFactory::getReference('Complex'));
        $sb->addArray('arrayChoice', TypeFactory::getUnion([TypeFactory::getReference('ChoiceA'), TypeFactory::getReference('ChoiceB')]));
        $sb->addBoolean('boolean');
        $sb->add('choice', TypeFactory::getUnion([TypeFactory::getReference('ChoiceA'), TypeFactory::getReference('ChoiceB')]));
        $sb->addReference('complex', 'Complex');
        $sb->addDate('date');
        $sb->addDateTime('dateTime');
        $sb->addDuration('duration');
        $sb->addNumber('float');
        $sb->addInteger('integer');
        $sb->addString('string');
        $sb->addTime('time');
    }
}
