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

namespace PSX\Framework\Test;

use PHPUnit\Framework\TestCase;
use PSX\Sql\Test\DatabaseTestCaseTrait;

/**
 * Base test class for database test cases
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class DbTestCase extends TestCase
{
    use DatabaseTestCaseTrait;

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        if (!Environment::hasConnection()) {
            $this->markTestSkipped('Database connection not available');
        }

        return Environment::getService('connection');
    }

    protected function setUp(): void
    {
        parent::setup();

        // since we have moved away from DbUnit this allows old packages to
        // still use this class which now maps to our own implementation
        if (!class_exists('PHPUnit_Extensions_Database_DataSet_ArrayDataSet')) {
            class_alias(ArrayDataSet::class, 'PHPUnit_Extensions_Database_DataSet_ArrayDataSet');
        }

        $this->setUpFixture();
    }

    /**
     * @param string $file
     * @return array
     */
    protected function createFromFile($file)
    {
        return include $file;
    }
}
