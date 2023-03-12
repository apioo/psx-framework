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

namespace PSX\Framework\Loader\RoutingParser;

use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Listing\FilterInterface;
use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParserInterface;

/**
 * CachedParser
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CachedParser implements RoutingParserInterface
{
    public const CACHE_KEY = 'psx-routing-collection';

    private RoutingParserInterface $routingParser;
    private CacheItemPoolInterface $cache;
    private ?int $expire;

    public function __construct(RoutingParserInterface $routingParser, CacheItemPoolInterface $cache, ?int $expire = null)
    {
        $this->routingParser = $routingParser;
        $this->cache         = $cache;
        $this->expire        = $expire;
    }

    public function getCollection(?FilterInterface $filter = null): RoutingCollection
    {
        $item = $this->cache->getItem(self::CACHE_KEY);

        if ($item->isHit()) {
            return $item->get();
        } else {
            $collection = $this->routingParser->getCollection();

            $item->set($collection);
            $item->expiresAfter($this->expire);

            $this->cache->save($item);

            return $collection;
        }
    }
}
