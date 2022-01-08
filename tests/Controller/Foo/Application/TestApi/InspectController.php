<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Tests\Controller\Foo\Application\TestApi;

use PHPUnit\Framework\Assert;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Util\Api\FilterParameter;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Sql\Sql;

/**
 * InspectController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class InspectController extends ControllerAbstract
{
    protected function doGet(HttpContextInterface $context): mixed
    {
        $params = FilterParameter::extract($context->getParameters());

        Assert::assertEquals(array('foo', 'bar'), $params->getFields());
        Assert::assertEquals('2014-01-26', $params->getUpdatedSince()->format('Y-m-d'));
        Assert::assertEquals(8, $params->getCount());
        Assert::assertEquals('id', $params->getFilterBy());
        Assert::assertEquals('equals', $params->getFilterOp());
        Assert::assertEquals('12', $params->getFilterValue());
        Assert::assertEquals('id', $params->getSortBy());
        Assert::assertEquals(Sql::SORT_DESC, $params->getSortOrder());
        Assert::assertEquals(4, $params->getStartIndex());

        $condition = FilterParameter::getCondition($params);

        Assert::assertEquals('WHERE (id = ? AND date > ?)', $condition->getStatement());
        Assert::assertEquals(['12', '2014-01-26 00:00:00'], $condition->getValues());

        return null;
    }

    #[Incoming(schema: NewsRecord::class)]
    #[Outgoing(code: 200, schema: NewsRecord::class)]
    protected function doPost(mixed $record, HttpContextInterface $context): mixed
    {
        return $record;
    }
}
