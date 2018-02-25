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

namespace PSX\Framework\Tests\Dispatch;

use PSX\Framework\Loader\Context;
use PSX\Framework\Test\Environment;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Uri;

/**
 * ControllerFactoryTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetController()
    {
        $controller = $this->getController(DummyController::class);

        $this->assertTrue(is_array($controller));
        $this->assertInstanceOf(DummyController::class, $controller[0]);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetControllerInvalid()
    {
        $this->getController('Foo\Bar');
    }

    protected function getController($className)
    {
        $factory = Environment::getService('controller_factory');

        return $factory->getController($className, new Context());
    }
}
