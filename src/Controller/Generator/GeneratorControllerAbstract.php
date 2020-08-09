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

namespace PSX\Framework\Controller\Generator;

use PSX\Api\SpecificationInterface;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Writer\File;
use PSX\Schema\Generator\Code\Chunks;

/**
 * GeneratorControllerAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class GeneratorControllerAbstract extends ControllerAbstract
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
     * @var \PSX\Api\GeneratorFactoryInterface
     */
    protected $generatorFactory;

    public function onGet(RequestInterface $request, ResponseInterface $response)
    {
        $version = $this->context->getParameter('version');
        $path    = $this->context->getParameter('path');
        $type    = $this->getType();

        $generator = $this->generatorFactory->getGenerator($type);

        if ($path == '*') {
            $filter = $this->listingFilterFactory->getFilter($request->getUri()->getParameter('filter') ?? '');

            $spec = $this->resourceListing->findAll($version, $filter);
        } else {
            $spec = $this->resourceListing->find($path, $version);
        }

        if (!$spec instanceof SpecificationInterface) {
            throw new StatusCode\NotFoundException('Invalid resource');
        }

        $result = $generator->generate($spec);

        if ($result instanceof Chunks) {
            // write chunks to zip file
            $file = tempnam($this->config->get('psx_path_cache'), 'sdk');
            $result->writeTo($file);

            $result = new File($file, 'sdk.zip', 'application/zip');
        } else {
            $response->setHeader('Content-Type', $this->generatorFactory->getMime($type));
        }

        $this->responseWriter->setBody($response, $result, $request);
    }

    /**
     * @return string
     */
    abstract protected function getType();
}
