<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Annotation;

use Doctrine\Common\Annotations;
use Doctrine\Common\Cache\Cache;

/**
 * Factory to create an annotation reader for a specific namespace
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ReaderFactory
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var array
     */
    private $container;
    
    public function __construct(Cache $cache, bool $debug)
    {
        $this->cache = $cache;
        $this->debug = $debug;
        $this->container = [];
    }

    /**
     * @param string $namespace
     * @return Annotations\Reader
     */
    public function factory(string $namespace): Annotations\Reader
    {
        if (isset($this->container[$namespace])) {
            return $this->container[$namespace];
        }

        $reader = new Annotations\SimpleAnnotationReader();
        $reader->addNamespace($namespace);

        if (!$this->debug) {
            $reader = new Annotations\CachedReader(
                $reader,
                $this->cache,
                $this->debug
            );
        }

        return $this->container[$namespace] = $reader;
    }
}
