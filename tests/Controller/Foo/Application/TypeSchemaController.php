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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use PHPUnit\Framework\Assert;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\PropertyTestCase;
use PSX\Record\RecordInterface;
use PSX\Schema\Type;

/**
 * TypeSchemaController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Path('/tests/typeschema/:id')]
#[PathParam(name: "id", type: Type::INTEGER)]
class TypeSchemaController extends ControllerAbstract
{
    use PropertyControllerTrait;

    #[Get]
    #[QueryParam(name: 'type', type: Type::INTEGER)]
    #[Outgoing(code: 200, schema: __DIR__ . '/../Resource/property.json')]
    public function doGet(int $id, int $type): mixed
    {
        Assert::assertEquals(1, $id);

        return PropertyTestCase::getDataByType($type);
    }

    #[Post]
    #[Incoming(schema: __DIR__ . '/../Resource/property.json')]
    #[Outgoing(code: 200, schema: __DIR__ . '/../Resource/property.json')]
    public function doPost(int $id, RecordInterface $payload): mixed
    {
        PropertyTestCase::assertRecord($payload);

        return $payload;
    }
}
