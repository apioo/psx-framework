<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Path;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Tests\TestTable;
use PSX\Sql\TableManagerInterface;

/**
 * TestApiTableController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TableController extends ControllerAbstract
{
    private TableManagerInterface $tableManager;

    public function __construct(TableManagerInterface $tableManager)
    {
        $this->tableManager = $tableManager;
    }

    #[Get]
    #[Path('/tests/table')]
    public function doGet(): mixed
    {
        return [
            'entry' => $this->tableManager->getTable(TestTable::class)->getAll()
        ];
    }

    #[Get]
    #[Path('/tests/table/row')]
    public function doRow(): mixed
    {
        return $this->tableManager->getTable(TestTable::class)->getOneById(1);
    }

    #[Get]
    #[Path('/tests/table/nested')]
    public function doNested(): mixed
    {
        return [
            'entry' => $this->tableManager->getTable(TestTable::class)->getNestedResult()
        ];
    }
}
