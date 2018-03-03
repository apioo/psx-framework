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

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Controller\Generator\OpenAPIController;
use PSX\Framework\Controller\Generator\RamlController;
use PSX\Framework\Controller\Generator\SwaggerController;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Record\Record;

/**
 * DiscoveryController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $links = [];

        $apiPath = $this->reverseRouter->getDispatchUrl();
        if ($apiPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'api',
                'href' => $apiPath,
            ]);
        }

        $routingPath = $this->reverseRouter->getUrl(RoutingController::class);
        if ($routingPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'routing',
                'href' => $routingPath,
            ]);
        }

        $documentationPath = $this->reverseRouter->getUrl(Documentation\IndexController::class);
        if ($documentationPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'documentation',
                'href' => $documentationPath,
            ]);
        }

        $generators = [
            'openapi' => OpenAPIController::class,
            'swagger' => SwaggerController::class,
            'raml'    => RamlController::class,
        ];

        foreach ($generators as $rel => $class) {
            $generatorPath = $this->reverseRouter->getUrl($class, ['{version}', '{path}']);
            if ($generatorPath !== null) {
                $links[] = Record::fromArray([
                    'rel'  => $rel,
                    'href' => $generatorPath,
                ]);
            }
        }

        $data = [
            'links' => $links,
        ];

        $this->responseWriter->setBody($response, $data, $request);
    }
}
