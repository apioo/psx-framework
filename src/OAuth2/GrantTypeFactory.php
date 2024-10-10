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

namespace PSX\Framework\OAuth2;

use PSX\OAuth2\Exception\UnsupportedGrantTypeException;

/**
 * GrantTypeFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GrantTypeFactory
{
    /**
     * @var GrantTypeInterface[]
     */
    private iterable $types;

    public function __construct(iterable $types)
    {
        $this->types = $types;
    }

    /**
     * @throws UnsupportedGrantTypeException
     */
    public function get(string $type): GrantTypeInterface
    {
        foreach ($this->types as $grantType) {
            if ($type == $grantType->getType()) {
                return $grantType;
            }
        }

        throw new UnsupportedGrantTypeException('Invalid grant type');
    }
}
