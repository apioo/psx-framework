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

namespace PSX\Framework\Tests\Api;

use PSX\Api\OperationInterface;
use PSX\Api\ScannerInterface;
use PSX\Api\SpecificationInterface;
use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Framework\Test\Environment;
use PSX\Framework\Tests\Controller\Foo\Application\SchemaController;

/**
 * ControllerAttributeTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerAttributeTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFromFile(__DIR__ . '/../table_fixture.php');
    }

    public function testGenerate()
    {
        $specification = Environment::getService(ScannerInterface::class)->generate();

        $this->assertInstanceOf(SpecificationInterface::class, $specification);

        $getOperation = $specification->getOperations()->get('PSX.Framework.Tests.Controller.Foo.Application.SchemaController.doGet');
        $postOperation = $specification->getOperations()->get('PSX.Framework.Tests.Controller.Foo.Application.SchemaController.doPost');
        $putOperation = $specification->getOperations()->get('PSX.Framework.Tests.Controller.Foo.Application.SchemaController.doPut');
        $deleteOperation = $specification->getOperations()->get('PSX.Framework.Tests.Controller.Foo.Application.SchemaController.doDelete');
        $patchOperation = $specification->getOperations()->get('PSX.Framework.Tests.Controller.Foo.Application.SchemaController.doPatch');

        $this->assertInstanceOf(OperationInterface::class, $getOperation);
        $this->assertInstanceOf(OperationInterface::class, $postOperation);
        $this->assertInstanceOf(OperationInterface::class, $putOperation);
        $this->assertInstanceOf(OperationInterface::class, $deleteOperation);
        $this->assertInstanceOf(OperationInterface::class, $patchOperation);
    }
}
