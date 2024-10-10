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

namespace PSX\Framework\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PSX\Sql\Test\DatabaseTestCaseTrait;
use Symfony\Component\Messenger\Transport\SetupableTransportInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * Base test class for database test cases
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class DbTestCase extends TestCase
{
    use DatabaseTestCaseTrait;

    public function getConnection(): Connection
    {
        global $environment;
        if (!$environment->hasConnection()) {
            $this->markTestSkipped('Database connection not available');
        }

        return $environment->getConnection();
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
        $this->setUpMessengerTransport();
    }

    protected function createFromFile(string $file): array
    {
        return include $file;
    }

    private function setUpMessengerTransport(): void
    {
        $transport = Environment::getService(TransportInterface::class);
        if ($transport instanceof SetupableTransportInterface) {
            $transport->setup();
        }
    }
}
