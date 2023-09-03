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

namespace PSX\Framework\Tests\Migration;

use Doctrine\Migrations\Version\Version;
use PHPUnit\Framework\TestCase;
use PSX\Framework\Migration\MigrationComparator;

/**
 * MigrationComparatorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MigrationComparatorTest extends TestCase
{
    public function testComparator()
    {
        $comparator = new MigrationComparator();

        $availableMigrations = [
            new Version('App\Migrations\Version20201227234115'),
            new Version('App\Migrations\Version20241227234115'),
            new Version('Fusio\Impl\Migrations\Version20230508210151'),
            new Version('Fusio\Impl\Migrations\Version20210508210152'),
        ];

        uasort($availableMigrations, function (Version $a, Version $b) use ($comparator): int {
            return $comparator->compare($a, $b);
        });

        $values = array_values($availableMigrations);

        $this->assertSame('Fusio\Impl\Migrations\Version20210508210152', (string) $values[0]);
        $this->assertSame('Fusio\Impl\Migrations\Version20230508210151', (string) $values[1]);
        $this->assertSame('App\Migrations\Version20201227234115', (string) $values[2]);
        $this->assertSame('App\Migrations\Version20241227234115', (string) $values[3]);
    }
}
