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

use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\QueryParam;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Api\SpecificationInterface;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Loader\Context;
use PSX\Http\Environment\HttpResponse;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Writer\File;
use PSX\Schema\Generator\Code\Chunks;

/**
 * Generator controller which supports automatically every type from the generator factory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GeneratorController extends ControllerAbstract
{
    private ScannerInterface $scanner;
    private FilterFactoryInterface $filterFactory;
    private GeneratorFactoryInterface $generatorFactory;

    public function __construct(ScannerInterface $scanner, FilterFactoryInterface $filterFactory, GeneratorFactoryInterface $generatorFactory)
    {
        $this->scanner = $scanner;
        $this->filterFactory = $filterFactory;
        $this->generatorFactory = $generatorFactory;
    }

    #[Path('/system/generator/:type')]
    #[Post]
    public function generate(string $type, ?string $filter = null): mixed
    {
        $type      = $this->getType($type);
        $filter    = $this->filterFactory->getFilter($filter ?? '');
        $generator = $this->generatorFactory->getGenerator($type, null, $filter);

        $spec   = $this->scanner->generate($filter);
        $result = $generator->generate($spec);

        $headers = [];
        if ($result instanceof Chunks) {
            // write chunks to zip file
            $file = tempnam($this->config->get('psx_path_cache'), 'sdk');
            $result->writeTo($file);

            $result = new File($file, 'sdk.zip', 'application/zip');
        } else {
            $headers['Content-Type'] = $this->generatorFactory->getMime($type);
        }

        return new HttpResponse(200, $headers, $result);
    }

    private function getType(string $type): string
    {
        $types = GeneratorFactory::getPossibleTypes();
        if (in_array($type, $types)) {
            // we have a valid type
            return $type;
        }

        // check whether the sub type matches
        foreach ($types as $value) {
            [, $subType] = explode('-', $value);
            if ($subType === $type) {
                return $value;
            }
        }

        throw new StatusCode\BadRequestException('Provided an invalid type');
    }
}
