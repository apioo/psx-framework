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

namespace PSX\Framework\Migration;

use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;

/**
 * We provide a custom comparator to ensure that the migrations under the app namespace are always executed at least
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MigrationComparator implements Comparator
{
    public function compare(Version $a, Version $b): int
    {
        $left = (string) $a;
        $right = (string) $b;

        if (str_starts_with($left, 'App\\') && !str_starts_with($right, 'App\\')) {
            return 1;
        } elseif (!str_starts_with($left, 'App\\') && str_starts_with($right, 'App\\')) {
            return -1;
        } else {
            return strcmp((string) $a, (string) $b);
        }
    }
}
