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

namespace PSX\Framework\Loader\RoutingParser;

use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Scanner\FilterInterface;
use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParserInterface;

/**
 * CachedParser
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class CachedParser implements RoutingParserInterface, InvalidateableInterface
{
    public const CACHE_KEY = 'psx-routing-collection';

    private RoutingParserInterface $routingParser;
    private CacheItemPoolInterface $cache;
    private bool $debug;

    public function __construct(RoutingParserInterface $routingParser, CacheItemPoolInterface $cache, bool $debug)
    {
        $this->routingParser = $routingParser;
        $this->cache = $cache;
        $this->debug = $debug;
    }

    public function getCollection(?FilterInterface $filter = null): RoutingCollection
    {
        $item = $this->cache->getItem(self::CACHE_KEY . $filter?->getId());
        if (!$this->debug && $item->isHit()) {
            return $item->get();
        }

        $collection = $this->routingParser->getCollection($filter);

        if (!$this->debug) {
            $item->set($collection);
            $this->cache->save($item);
        }

        return $collection;
    }

    public function invalidate(?FilterInterface $filter = null): void
    {
        $this->cache->deleteItem(self::CACHE_KEY . $filter?->getId());

        if ($this->routingParser instanceof InvalidateableInterface) {
            $this->routingParser->invalidate($filter);
        }
    }
}
