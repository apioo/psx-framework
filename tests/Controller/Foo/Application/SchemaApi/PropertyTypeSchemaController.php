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

namespace PSX\Framework\Tests\Controller\Foo\Application\SchemaApi;

use PHPUnit\Framework\Assert;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\SchemaApi\PropertyTestCase;
use PSX\Http\Environment\HttpContextInterface;

/**
 * PropertyAnnotationController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
#[PathParam(name: "id", type: "integer")]
class PropertyTypeSchemaController extends ControllerAbstract
{
    use PropertyControllerTrait;

    #[QueryParam(name: 'type', type: 'integer')]
    #[Outgoing(code: 200, schema: __DIR__ . '/../../Resource/property.json')]
    protected function doGet(HttpContextInterface $context): mixed
    {
        Assert::assertEquals(1, $context->getUriFragment('id'));

        return PropertyTestCase::getDataByType($context->getParameter('type'));
    }

    #[Incoming(schema: __DIR__ . '/../../Resource/property.json')]
    #[Outgoing(code: 200, schema: __DIR__ . '/../../Resource/property.json')]
    protected function doPost($record, HttpContextInterface $context): mixed
    {
        PropertyTestCase::assertRecord($record);

        return $record;
    }
}
