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

namespace PSX\Framework\Schema\Documentation;

use PSX\Framework\Schema\Discovery\Link;
use PSX\Schema\SchemaAbstract;

/**
 * Detail
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Detail extends SchemaAbstract
{
    public function getDefinition()
    {
        $sb = $this->getSchemaBuilder('Documentation Schema');
        $sb->setDescription('Contains the JSON Schema object');
        $sb->setAdditionalProperties(true);
        $schema = $sb->getProperty();

        $sb = $this->getSchemaBuilder('Documentation Detail');
        $sb->string('path');
        $sb->string('version');
        $sb->integer('status');
        $sb->string('description');
        $sb->objectType('schema', $schema);
        $sb->string('pathParameters');
        $sb->objectType('methods')
            ->setTitle('Documentation Methods')
            ->setAdditionalProperties($this->getSchema(Method::class));
        $sb->arrayType('links')
            ->setItems($this->getSchema(Link::class));

        return $sb->getProperty();
    }
}
