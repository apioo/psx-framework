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

namespace PSX\Framework\Loader;

use ArrayIterator;
use Countable;
use Traversable;

/**
 * RoutingCollection
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 * @template-implements \IteratorAggregate<array>
 */
class RoutingCollection implements \IteratorAggregate, Countable
{
    public const ROUTING_METHODS = 0;
    public const ROUTING_PATH    = 1;
    public const ROUTING_SOURCE  = 2;

    private array $routings;

    public function __construct(array $routings = [])
    {
        $this->routings = $routings;
    }

    public function add(array $methods, string $path, mixed $source, ...$args)
    {
        $this->routings[] = [$methods, $path, $source, ...array_values($args)];
    }

    public function getAll(): array
    {
        return $this->routings;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->routings);
    }

    public function count(): int
    {
        return count($this->routings);
    }
}
