<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Generator;
use PSX\Api\GeneratorFactory;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Framework\Controller\Generator\GeneratorController;
use PSX\Framework\Controller\Generator\OpenAPIController;
use PSX\Framework\Controller\Generator\RamlController;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Schema;
use PSX\Http\Environment\HttpContextInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Schema\TypeFactory;

/**
 * DetailController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DetailController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    /**
     * @inheritdoc
     */
    public function getDocumentation(?string $version = null): ?SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder(Resource::STATUS_ACTIVE, $this->context->getPath());

        $path = $builder->setPathParameters('Documentation_Path');
        $path->addString('version');
        $path->addString('path');

        $get = $builder->addMethod('GET');
        $get->addResponse(200, Schema\Documentation\Detail::class);

        return $builder->getSpecification();
    }

    public function doGet(HttpContextInterface $httpContext)
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

        // links
        $links = $this->getLinks($version, $resource->getPath());
        if (!empty($links)) {
            $api->links = $links;
        }

        return $api;
    }

    protected function getLinks($version, $path)
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
