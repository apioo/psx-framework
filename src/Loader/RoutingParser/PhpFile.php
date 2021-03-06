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

namespace PSX\Framework\Loader\RoutingParser;

use PSX\Api\Listing\FilterInterface;
use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParserInterface;

/**
 * PhpFile
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PhpFile implements RoutingParserInterface
{
    protected $file;

    protected $_collection;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getCollection(?FilterInterface $filter = null)
    {
        if ($this->_collection === null) {
            $collection = new RoutingCollection();
            $routes     = include $this->file;

            foreach ($routes as $route) {
                [$allowed, $path, $class] = $route;

                $collection->add($allowed, $path, $class);
            }

            $this->_collection = $collection;
        }

        return $this->_collection;
    }
}
