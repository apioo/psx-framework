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

use PSX\Api\Attribute\Outgoing;
use PSX\Api\Listing\FilterFactoryInterface;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Schema;
use PSX\Http\Environment\HttpContextInterface;

/**
 * IndexController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class IndexController extends ControllerAbstract
{
    #[Inject]
    private FilterFactoryInterface $listingFilterFactory;

    #[Inject]
    private ReverseRouter $reverseRouter;

    #[Outgoing(code: 200, schema: Schema\Documentation\Index::class)]
    protected function doGet(HttpContextInterface $context): array
    {
        return [
            'routings' => $this->getRoutings($context->getParameter('filter') ?? ''),
            'links'    => [
                [
                    'rel'  => 'self',
                    'href' => $this->reverseRouter->getUrl(IndexController::class),
                ],
                [
                    'rel'  => 'detail',
                    'href' => $this->reverseRouter->getUrl(DetailController::class, array('{version}', '{path}')),
                ],
                [
                    'rel'  => 'api',
                    'href' => $this->reverseRouter->getDispatchUrl(),
                ],
            ]
        ];
    }

    private function getRoutings(string $filter): array
    {
        $filter    = $this->listingFilterFactory->getFilter($filter);
        $routings  = array();
        $resources = $this->resourceListing->getAvailableRoutes($filter);

        foreach ($resources as $resource) {
            $routings[] = [
                'path'    => $resource->getPath(),
                'methods' => $resource->getMethods(),
                'version' => '*',
            ];
        }

        return $routings;
    }
}
