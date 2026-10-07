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

namespace PSX\Framework\Command\Generate;

use PSX\Api\Repository\LocalRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;

/**
 * FrontendCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[AsCommand(name: 'generate:frontend', description: 'Generates a frontend SDK')]
class FrontendCommand extends SdkCommand
{
    protected function getType(InputInterface $input): ?string
    {
        $type = parent::getType($input);
        if (empty($type)) {
            $type = LocalRepository::CLIENT_TYPESCRIPT;
        }

        return $type;
    }

    protected function getFilter(InputInterface $input): ?string
    {
        $filter = parent::getFilter($input);
        if (empty($filter)) {
            $filter = 'frontend';
        }

        return $filter;
    }

    protected function getOutput(InputInterface $input): ?string
    {
        $output = parent::getOutput($input);
        if (empty($output)) {
            $output = '../frontend/src/app/generated';
        }

        return $output;
    }

    protected function isRaw(InputInterface $input): bool
    {
        return true;
    }
}
