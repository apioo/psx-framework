<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Controller\Tool;

use PSX\Api\Resource;
use PSX\Framework\Controller\Generator\OpenAPIController;
use PSX\Framework\Controller\Generator\RamlController;
use PSX\Framework\Controller\Generator\SwaggerController;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Schema;
use PSX\Http\Environment\HttpContextInterface;

/**
 * DiscoveryController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    /**
     * @inheritdoc
     */
    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->getPath());

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addResponse(200, $this->schemaManager->getSchema(Schema\Discovery\Collection::class))
        );

        return $resource;
    }

    public function doGet(HttpContextInterface $httpContext)
    {
        $links = [];

        $apiPath = $this->reverseRouter->getDispatchUrl();
        if ($apiPath !== null) {
            $links[] = [
                'rel'  => 'api',
                'href' => $apiPath,
            ];
        }

        $routingPath = $this->reverseRouter->getUrl(RoutingController::class);
        if ($routingPath !== null) {
            $links[] = [
                'rel'  => 'routing',
                'href' => $routingPath,
            ];
        }

        $documentationPath = $this->reverseRouter->getUrl(Documentation\IndexController::class);
        if ($documentationPath !== null) {
            $links[] = [
                'rel'  => 'documentation',
                'href' => $documentationPath,
            ];
        }

        $generators = [
            'openapi' => OpenAPIController::class,
            'swagger' => SwaggerController::class,
            'raml'    => RamlController::class,
        ];

        foreach ($generators as $rel => $class) {
            $generatorPath = $this->reverseRouter->getUrl($class, ['{version}', '{path}']);
            if ($generatorPath !== null) {
                $links[] = [
                    'rel'  => $rel,
                    'href' => $generatorPath,
                ];
            }
        }

        return [
            'links' => $links,
        ];
    }
}
