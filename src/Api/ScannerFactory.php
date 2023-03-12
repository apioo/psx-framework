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

namespace PSX\Framework\Api;

use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Listing\CachedScanner;
use PSX\Api\ScannerInterface;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Schema\SchemaManagerInterface;

/**
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ScannerFactory
{
    private RoutingParserInterface $routingParser;
    private SchemaManagerInterface $schemaManager;
    private bool $debug;
    private CacheItemPoolInterface $cache;

    public function __construct(RoutingParserInterface $routingParser, SchemaManagerInterface $schemaManager, bool $debug, CacheItemPoolInterface $cache)
    {
        $this->routingParser = $routingParser;
        $this->schemaManager = $schemaManager;
        $this->debug = $debug;
        $this->cache = $cache;
    }

    public function factory(): ScannerInterface
    {
        $resourceListing = new ControllerAttribute($this->routingParser, $this->schemaManager);

        if ($this->debug) {
            return $resourceListing;
        } else {
            return new CachedScanner($resourceListing, $this->cache);
        }
    }
}
