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

namespace PSX\Framework\Api\Scanner;

use Psr\Log\LoggerInterface;
use PSX\Api\ApiManagerInterface;
use PSX\Api\Exception\ApiException;
use PSX\Api\Operations;
use PSX\Api\Scanner\FilterInterface;
use PSX\Api\ScannerInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Schema\Definitions;

/**
 * Scanner which goes through all registered routes and uses the API parser to get a fitting specification
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class RoutingParser implements ScannerInterface
{
    private RoutingParserInterface $routingParser;
    private ApiManagerInterface $apiManager;
    private LoggerInterface $logger;

    public function __construct(RoutingParserInterface $routingParser, ApiManagerInterface $apiManager, LoggerInterface $logger)
    {
        $this->routingParser = $routingParser;
        $this->apiManager = $apiManager;
        $this->logger = $logger;
    }

    public function generate(?FilterInterface $filter = null): SpecificationInterface
    {
        $result = new Specification(new Operations(), new Definitions());

        $collections = $this->routingParser->getCollection($filter);
        foreach ($collections as $collection) {
            [$methods, $path, $source] = $collection;

            if (is_array($source) && count($source) === 2) {
                $controller = $source[0];
            } else {
                continue;
            }

            try {
                $spec = $this->apiManager->getApi($controller);
            } catch (ApiException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);

                continue;
            }

            if ($filter !== null) {
                $spec->getOperations()->filter($filter);
            }

            $result->merge($spec);
        }

        return $result;
    }
}
