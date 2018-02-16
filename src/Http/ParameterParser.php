<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Http;

use PSX\Http\RequestInterface;
use PSX\Record\Record;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertyType;
use PSX\Schema\Schema;
use PSX\Schema\SchemaTraverser;
use PSX\Validate\Validate;

/**
 * ParameterParser
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ParameterParser
{
    /**
     * @var \PSX\Schema\SchemaTraverser
     */
    protected $traverser;

    /**
     * @var \PSX\Validate\Validate
     */
    protected $validate;

    public function __construct()
    {
        $this->traverser = new SchemaTraverser();
        $this->validate  = new Validate();
    }

    /**
     * @param \PSX\Http\RequestInterface $request
     * @param string $key
     * @param string $type
     * @param array $filter
     * @param string|null $title
     * @param boolean $required
     * @return mixed|null
     */
    public function get(RequestInterface $request, $key, $type = Validate::TYPE_STRING, array $filter = array(), $title = null, $required = true)
    {
        $parameter = $request->getUri()->getParameter($key);

        if (isset($parameter)) {
            return $this->validate->apply($parameter, $type, $filter, $title, $required);
        } else {
            return null;
        }
    }

    /**
     * @param array $data
     * @param \PSX\Schema\PropertyInterface $property
     * @return mixed
     */
    public function parse(array $data, PropertyInterface $property)
    {
        $data = $this->traverser->traverse(
            $this->convertParameterTypes($data, $property),
            new Schema($property)
        );

        return Record::fromStdClass($data);
    }

    /**
     * @param array $parameters
     * @param \PSX\Schema\PropertyInterface $property
     * @return \stdClass
     */
    private function convertParameterTypes(array $parameters, PropertyInterface $property)
    {
        $data = new \stdClass();
        $keys = [];

        $properties = $property->getProperties();
        if (!empty($properties)) {
            foreach ($properties as $name => $property) {
                if (isset($parameters[$name])) {
                    $data->{$name} = $this->convertPropertyType($parameters[$name], $property);

                    $keys[] = $name;
                }
            }
        }

        $additionalProperties = $property->getAdditionalProperties();
        if ($additionalProperties === true) {
            $diff = array_diff(array_keys($parameters), $keys);
            foreach ($diff as $name) {
                $data->{$name} = $parameters[$name];
            }
        } elseif ($additionalProperties instanceof PropertyInterface) {
            $diff = array_diff(array_keys($parameters), $keys);
            foreach ($diff as $name) {
                $data->{$name} = $this->convertPropertyType($parameters[$name], $additionalProperties);
            }
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param \PSX\Schema\PropertyInterface $property
     * @return mixed
     */
    private function convertPropertyType($data, PropertyInterface $property)
    {
        if ($property->getType() === PropertyType::TYPE_INTEGER) {
            return (int) $data;
        } elseif ($property->getType() === PropertyType::TYPE_NUMBER) {
            return (float) $data;
        } elseif ($property->getType() === PropertyType::TYPE_BOOLEAN) {
            return (bool) $data;
        } elseif ($property->getType() === PropertyType::TYPE_STRING) {
            return (string) $data;
        } elseif ($property->getType() === PropertyType::TYPE_NULL) {
            return null;
        } else {
            return $data;
        }
    }
}
