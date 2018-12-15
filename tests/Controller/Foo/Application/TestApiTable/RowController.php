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

namespace PSX\Framework\Tests\Controller\Foo\Application\TestApiTable;

use PSX\Framework\Controller\ApiAbstract;
use PSX\Framework\Tests\TestTable;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * RowController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RowController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PHPUnit\Framework\TestCase
     */
    protected $testCase;

    /**
     * @Inject
     * @var \PSX\Sql\TableManager
     */
    protected $tableManager;

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $data = $this->tableManager->getTable(TestTable::class)->getOneById(1);

        $this->responseWriter->setBody($response, $data, $request);
    }
}
