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

namespace PSX\Framework\Tests\Controller\Foo\Application\TestApi;

use PSX\Framework\Controller\ApiAbstract;
use PSX\Framework\Util\Api\FilterParameter;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Sql\Sql;

/**
 * InspectController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class InspectController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $params = FilterParameter::extract($request->getUri()->getParameters());

        $this->testCase->assertEquals(array('foo', 'bar'), $params->getFields());
        $this->testCase->assertEquals('2014-01-26', $params->getUpdatedSince()->format('Y-m-d'));
        $this->testCase->assertEquals(8, $params->getCount());
        $this->testCase->assertEquals('id', $params->getFilterBy());
        $this->testCase->assertEquals('equals', $params->getFilterOp());
        $this->testCase->assertEquals('12', $params->getFilterValue());
        $this->testCase->assertEquals('id', $params->getSortBy());
        $this->testCase->assertEquals(Sql::SORT_DESC, $params->getSortOrder());
        $this->testCase->assertEquals(4, $params->getStartIndex());

        $condition = FilterParameter::getCondition($params);

        $this->testCase->assertEquals('WHERE (id = ? AND date > ?)', $condition->getStatment());
        $this->testCase->assertEquals(['12', '2014-01-26 00:00:00'], $condition->getValues());
    }

    public function onPost(RequestInterface $request, ResponseInterface $response)
    {
        $record = $this->requestReader->getBodyAs($request, NewsRecord::class);

        $this->responseWriter->setBody($response, $record, $request);
    }
}
