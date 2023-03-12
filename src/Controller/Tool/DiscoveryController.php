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

namespace PSX\Framework\Controller\Tool;

use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Controller\ControllerInterface;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Schema;
use PSX\Http\Environment\HttpContextInterface;

/**
 * DiscoveryController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryController implements ControllerInterface
{
    private ReverseRouter $reverseRouter;

    public function __construct(ReverseRouter $reverseRouter)
    {
        $this->reverseRouter = $reverseRouter;
    }

    #[Path('/system/discovery')]
    protected function show(): Schema\Discovery\Collection
    {
        $links = new Schema\Discovery\Collection();

        $apiPath = $this->reverseRouter->getDispatchUrl();
        if ($apiPath !== null) {
            $links->addLink(new Schema\Discovery\Link());
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
