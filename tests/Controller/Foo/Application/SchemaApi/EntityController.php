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
use PHPUnit\Framework\TestCase;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Schema;
use PSX\Framework\Tests\TestTable;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Record\RecordInterface;
use PSX\Schema\Property;
use PSX\Schema\SchemaManagerInterface;

/**
 * EntityController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
#[PathParam(name: 'fooId', type: 'integer')]
class EntityController extends ControllerAbstract
{
    #[QueryParam(name: 'startIndex', type: 'integer')]
    #[QueryParam(name: 'count', type: 'integer')]
    #[Outgoing(code: 200, schema: Schema\Collection::class)]
    protected function doGet(HttpContextInterface $context): array
    {
        Assert::assertEquals(12, $context->getParameter('startIndex'));
        Assert::assertEmpty($context->getParameter('bar'));
        Assert::assertEquals(8, $context->getParameter('fooId'));
        Assert::assertEmpty($context->getParameter('bar'));

        return array(
            'entry' => Environment::getService('table_manager')->getTable(TestTable::class)->getAll()
        );
    }

    #[Incoming(schema: Schema\Update::class)]
    #[Outgoing(code: 200, schema: Schema\SuccessMessage::class)]
    protected function doPut(mixed $record, HttpContextInterface $context): array
    {
        Assert::assertEquals(8, $context->getUriFragment('fooId'));
        Assert::assertEmpty($context->getUriFragment('bar'));

        Assert::assertEquals(1, $record->getId());
        Assert::assertEquals(3, $record->getUserId());
        Assert::assertEquals('foobar', $record->getTitle());

        return array(
            'success' => true,
            'message' => 'You have successful update a record'
        );
    }

    #[Incoming(schema: Schema\Delete::class)]
    #[Outgoing(code: 200, schema: Schema\SuccessMessage::class)]
    protected function doDelete(HttpContextInterface $context): array
    {
        Assert::assertEquals(8, $context->getUriFragment('fooId'));
        Assert::assertEmpty($context->getUriFragment('bar'));

        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }
}
