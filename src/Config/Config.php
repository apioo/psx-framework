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

namespace PSX\Framework\Config;

use ArrayIterator;

/**
 * Config
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @template-extends \ArrayIterator<string, mixed>
 */
class Config extends ArrayIterator
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function set(string $key, mixed $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function get(string $key): mixed
    {
        return $this->offsetGet($key);
    }

    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    public function merge(Config $config): self
    {
        return new static(array_merge($this->getArrayCopy(), $config->getArrayCopy()));
    }

    public function offsetGet(mixed $key): mixed
    {
        return $this->offsetExists($key) ? parent::offsetGet($key) : null;
    }

    /**
     * @throws NotFoundException
     */
    public static function fromFile(string $file): self
    {
        $config = include($file);
        if (is_array($config)) {
            return new static($config);
        } else {
            throw new NotFoundException('Config file must return an array');
        }
    }
}
