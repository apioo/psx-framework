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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PSX\Api\Attribute\Outgoing;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo;
use PSX\Framework\Tests\TestTable;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Schema\SchemaManagerInterface;

/**
 * TestSchemaApiV2Controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestSchemaApiV2Controller extends ControllerAbstract
{
    #[Outgoing(code: 200, schema: Foo\Schema\CollectionV2::class)]
    protected function doGet(HttpContextInterface $context): array
    {
        return array(
            'entry' => Environment::getService('table_manager')->getTable(TestTable::class)->getAll()
        );
    }

    protected function doPost($record, HttpContextInterface $context): array
    {
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('test', $record->title);
        Assert::assertInstanceOf('DateTime', $record->date);

        return array(
            'success' => true,
            'message' => 'You have successful post a record'
        );
    }

    protected function doPut($record, HttpContextInterface $context): array
    {
        Assert::assertEquals(1, $record->id);
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful put a record'
        );
    }

    protected function doDelete(HttpContextInterface $context): array
    {
        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }

    protected function doPatch($record, HttpContextInterface $context): array
    {
        Assert::assertEquals(1, $record->id);
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful patch a record'
        );
    }
}
