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

namespace PSX\Framework\Controller\Tool\Documentation;

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Record\Record;

/**
 * IndexController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class IndexController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \PSX\Api\Listing\FilterFactoryInterface
     */
    protected $listingFilterFactory;

    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $data = [
            'routings' => $this->getRoutings($request->getUri()->getParameter('filter')),
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

        $this->responseWriter->setBody($response, $data, $request);
    }

    protected function getRoutings($filter)
    {
        $filter    = $this->listingFilterFactory->getFilter($filter);
        $routings  = array();
        $resources = $this->resourceListing->getResourceIndex($filter);

        foreach ($resources as $resource) {
            $routings[] = new Record('routing', [
                'path'    => $resource->getPath(),
                'methods' => $resource->getAllowedMethods(),
                'version' => '*',
            ]);
        }

        return $routings;
    }
}
