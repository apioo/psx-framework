<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Framework\Loader\RoutingParser\CachedParser;
use PSX\Framework\Loader\RoutingParser\PhpFile;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * CachedParserTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class CachedParserTest extends RoutingParserTestCase
{
    public function getRoutingCollection()
    {
        $cache   = new ArrayAdapter();
        $routing = new PhpFile(__DIR__ . '/../routes.php');
        $cached  = new CachedParser($routing, $cache);

        return $cached->getCollection();
    }

    public function testGetCollection()
    {
        $cache         = new ArrayAdapter();
        $routing       = new PhpFile(__DIR__ . '/../routes.php');
        $routingParser = new CachedParser($routing, $cache, true);

        // we remove previous cache
        $cache->deleteItems([CachedParser::CACHE_KEY]);

        // get collection from the parser
        $collection = $routingParser->getCollection();

        $this->assertInstanceOf('PSX\Framework\Loader\RoutingCollection', $collection);
        $this->assertEquals(16, count($collection));

        // get collection from the cache
        $collection = $routingParser->getCollection();

        $this->assertInstanceOf('PSX\Framework\Loader\RoutingCollection', $routingParser->getCollection());
        $this->assertEquals(16, count($collection));
    }
}
