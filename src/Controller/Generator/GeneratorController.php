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

namespace PSX\Framework\Controller\Generator;

use PSX\Api\GeneratorFactory;

/**
 * Generator controller which supports automatically every type from the
 * generator factory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GeneratorController extends GeneratorControllerAbstract
{
    protected function getType()
    {
        $type = $this->context->getParameter('type');

        $types = GeneratorFactory::getPossibleTypes();
        if (in_array($type, $types)) {
            // we have a valid type
            return $type;
        }

        // check whether the sub type matches
        foreach ($types as $value) {
            [, $subType] = explode('-', $value);
            if ($subType === $type) {
                return $value;
            }
        }

        return null;
    }
}
