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

namespace PSX\Framework\Connection;

use Doctrine\DBAL;

/**
 * ConnectionFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ConnectionFactory
{
    private ?DBAL\Connection $connection = null;
    private array|string $params;
    private array $schemeMapping;

    public function __construct(array|string $params)
    {
        $this->params = $params;

        $this->schemeMapping = [
            'mysql' => 'mysqli',
            'postgres' => 'pdo_pgsql',
        ];
    }

    public function factory(): DBAL\Connection
    {
        if (isset($this->connection)) {
            return $this->connection;
        }

        if (is_string($this->params)) {
            $params = (new DBAL\Tools\DsnParser($this->schemeMapping))->parse($this->params);
        } else {
            $params = $this->params;
        }

        $config = new DBAL\Configuration();

        return $this->connection = DBAL\DriverManager::getConnection($params, $config);
    }
}
