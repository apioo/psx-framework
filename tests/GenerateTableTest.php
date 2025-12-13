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

namespace PSX\Framework\Tests;

use Fusio\Impl\Backend;
use Fusio\Impl\Tests\Fixture;
use PSX\Framework\Test\DbTestCase;
use PSX\Sql\Generator\Generator;

/**
 * GenerateTableTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GenerateTableTest extends DbTestCase
{
    public function getDataSet(): array
    {
        return $this->createFromFile(__DIR__ . '/table_fixture.php');
    }

    public function testGenerate()
    {
        $this->markTestSkipped();

        /** @phpstan-ignore deadCode.unreachable */
        $target = __DIR__ . '/Table';
        $namespace = 'PSX\Framework\Tests\Table';

        $generator = new Generator($this->connection, $namespace, 'psx_');
        $count = 0;
        foreach ($generator->generate() as $className => $source) {
            file_put_contents($target . '/' . $className . '.php', '<?php' . "\n\n" . $source);
            $count++;
        }

        $this->assertNotEmpty($count);
    }
}