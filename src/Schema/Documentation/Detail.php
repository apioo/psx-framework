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

namespace PSX\Framework\Schema\Documentation;

use PSX\Framework\Schema\Discovery\Link;
use PSX\Schema\SchemaAbstract;
use PSX\Schema\TypeFactory;

/**
 * Detail
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Detail extends SchemaAbstract
{
    public function build(): void
    {
        $type = $this->newMap('Documentation_Schema');
        $type->setAdditionalProperties(true);

        $type = $this->newMap('Documentation_Methods');
        $type->setAdditionalProperties($this->get(Method::class));

        $type = $this->newStruct('Documentation_Detail');
        $type->addString('path');
        $type->addString('version');
        $type->addInteger('status');
        $type->addString('description');
        $type->addReference('schema', 'Documentation_Schema');
        $type->addString('pathParameters');
        $type->addReference('methods', 'Documentation_Methods');
        $type->addArray('links', $this->get(Link::class));
    }
}
