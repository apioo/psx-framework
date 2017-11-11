<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Generator;
use PSX\Api\Resource;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as StatusCode;

/**
 * RamlController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    public function onGet()
    {
        $version   = (int) $this->getUriFragment('version');
        $path      = $this->getUriFragment('path');
        $generator = $this->newGenerator($version);

        if ($path == '*') {
            $collection = $this->resourceListing->getResourceCollection($version);
            $raml = $generator->generateAll($collection);
        } else {
            $resource = $this->resourceListing->getResource($path, $version);

            if (!$resource instanceof Resource) {
                throw new StatusCode\NotFoundException('Invalid resource');
            }

            $raml = $generator->generate($resource);
        }

        $this->setHeader('Content-Type', 'application/raml+yaml');
        $this->setBody($raml);
    }

    private function newGenerator($version)
    {
        $title     = parse_url($this->config['psx_url'], PHP_URL_HOST) . ' API';
        $baseUri   = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
        $namespace = $this->config['psx_json_namespace'];
        $generator = new Generator\Raml($title, $version, $baseUri, $namespace);

        return $generator;
    }
}
