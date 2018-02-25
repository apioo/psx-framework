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

namespace PSX\Framework\Tests\Loader\RoutingParser;

use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParser\PhpFile;
use PSX\Framework\Loader\RoutingParser\RoutingFile;

/**
 * PhpFileTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PhpFileTest extends RoutingParserTestCase
{
    public function getRoutingCollection()
    {
        $routingFile = new PhpFile(__DIR__ . '/../routes.php');
        $collection  = $routingFile->getCollection();
        
        return $collection;
    }
}
