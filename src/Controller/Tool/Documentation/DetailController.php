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

namespace PSX\Framework\Controller\Tool\Documentation;

use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Generator;
use PSX\Api\GeneratorFactory;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Controller\Generator\GeneratorController;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Schema;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Exception as StatusCode;

/**
 * DetailController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
#[PathParam(name: 'version', type: 'string')]
#[PathParam(name: 'path', type: 'string')]
class DetailController extends ControllerAbstract
{
    #[Inject]
    private ReverseRouter $reverseRouter;

    #[Outgoing(code: 200, schema: Schema\Documentation\Detail::class)]
    protected function doGet(HttpContextInterface $context): \stdClass
    {
        $version = $this->context->getParameter('version');
        $path    = $this->context->getParameter('path') ?: '/';

        if (empty($version) || empty($path)) {
            throw new StatusCode\BadRequestException('Version and path not provided');
        }

        $specification = $this->resourceListing->find($path, $version);
        if (!$specification instanceof SpecificationInterface) {
            throw new StatusCode\BadRequestException('Invalid api version');
        }

        $resource = $specification->getResourceCollection()->getFirst();
        if (!$resource instanceof Resource) {
            throw new StatusCode\BadRequestException('Resource not found');
        }

        $generator = new Generator\Spec\TypeSchema();
        $api = \json_decode($generator->generate($specification));
        if (!$api instanceof \stdClass) {
            throw new StatusCode\InternalServerErrorException('Could not generate specification');
        }

        // links
        $links = $this->getLinks($version, $resource->getPath());
        if (!empty($links)) {
            $api->links = $links;
        }

        return $api;
    }

    private function getLinks(string $version, string $path): array
    {
        $path   = ltrim($path, '/');
        $result = [];

        $types = GeneratorFactory::getPossibleTypes();
        foreach ($types as $type) {
            $href = $this->reverseRouter->getUrl(GeneratorController::class, ['type' => $type, 'version' => $version, 'path' => $path]);
            if (empty($href)) {
                continue;
            }

            $result[] = [
                'rel'  => $type,
                'href' => $href,
            ];
        }

        return $result;
    }
}
