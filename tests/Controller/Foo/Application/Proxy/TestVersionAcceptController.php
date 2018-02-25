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

namespace PSX\Framework\Tests\Controller\Foo\Application\Proxy;

use PSX\Framework\Controller\Proxy\VersionController;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController;
use PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiV2Controller;

/**
 * TestVersionAcceptController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestVersionAcceptController extends VersionController
{
    public function getVersions()
    {
        return [
            1 => TestSchemaApiController::class,
            2 => TestSchemaApiV2Controller::class,
        ];
    }

    protected function getVersionType()
    {
        return self::TYPE_ACCEPT;
    }
}

