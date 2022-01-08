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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use PHPUnit\Framework\TestCase;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ApiAbstract;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\TestTable;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Record\Record;
use PSX\Sql\TableManagerInterface;

/**
 * TestApiTableController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestApiTableController extends ControllerAbstract
{
    #[Inject]
    private TableManagerInterface $tableManager;

    protected function doGet(HttpContextInterface $context): array
    {
        return [
            'entry' => $this->tableManager->getTable(TestTable::class)->getAll()
        ];
    }

    public function doRow()
    {
        $this->setBody($this->tableManager->getTable(TestTable::class)->getOneById(1));
    }

    public function doNested()
    {
        $this->setBody(array(
            'entry' => $this->tableManager->getTable(TestTable::class)->getNestedResult()
        ));
    }
}
