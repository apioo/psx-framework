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
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\Controller\Foo\Schema;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Schema\SchemaManagerInterface;

/**
 * NoResponseController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class NoResponseController extends ControllerAbstract
{
    protected function doGet(HttpContextInterface $context): mixed
    {
        return null;
    }

    #[Incoming(schema: Schema\Create::class)]
    protected function doPost($record, HttpContextInterface $context): mixed
    {
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('test', $record->title);
        Assert::assertInstanceOf('DateTime', $record->date);

        return null;
    }

    #[Incoming(schema: Schema\Update::class)]
    protected function doPut($record, HttpContextInterface $context): mixed
    {
        Assert::assertEquals(1, $record->id);
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('foobar', $record->title);

        return null;
    }

    #[Incoming(schema: Schema\Patch::class)]
    protected function doPatch($record, HttpContextInterface $context): mixed
    {
        Assert::assertEquals(1, $record->id);
        Assert::assertEquals(3, $record->userId);
        Assert::assertEquals('foobar', $record->title);

        return null;
    }

    protected function doDelete(HttpContextInterface $context): mixed
    {
        return null;
    }
}
