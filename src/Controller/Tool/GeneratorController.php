<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\Attribute\OperationId;
use PSX\Api\Attribute\Param;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Query;
use PSX\Api\GeneratorFactory;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Framework\Config\ConfigInterface;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Environment\HttpResponse;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Writer\File;
use PSX\Schema\Generator\Code\Chunks;
use PSX\Schema\Generator\Config;

/**
 * Generator controller which supports automatically every type from the generator factory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GeneratorController extends ControllerAbstract
{
    private ScannerInterface $scanner;
    private FilterFactoryInterface $filterFactory;
    private GeneratorFactory $generatorFactory;
    private ConfigInterface $config;

    public function __construct(ScannerInterface $scanner, FilterFactoryInterface $filterFactory, GeneratorFactory $generatorFactory, ConfigInterface $config)
    {
        $this->scanner = $scanner;
        $this->filterFactory = $filterFactory;
        $this->generatorFactory = $generatorFactory;
        $this->config = $config;
    }

    #[Get]
    #[Path('/system/generator/:type')]
    #[OperationId('system.generate')]
    public function generate(#[Param] string $type, #[Query] ?string $filter = null, #[Query] ?string $config = null): mixed
    {
        $registry = $this->generatorFactory->factory();

        $type      = $this->getType($type, $registry->getPossibleTypes());
        $filter    = $this->filterFactory->getFilter($filter ?? '');
        $config    = Config::fromBase64String($config);
        $generator = $registry->getGenerator($type, $config, $filter);

        $spec   = $this->scanner->generate($filter);
        $result = $generator->generate($spec);

        $headers = [];
        if ($result instanceof Chunks) {
            // write chunks to zip file
            $file = tempnam($this->config->get('psx_path_cache'), 'sdk-' . $type);
            $result->writeToZip($file);

            $result = new File($file, 'sdk.zip', 'application/zip');
        } else {
            $headers['Content-Type'] = $registry->getMime($type);
        }

        return new HttpResponse(200, $headers, $result);
    }

    private function getType(string $type, array $possibleTypes): string
    {
        if (in_array($type, $possibleTypes)) {
            // we have a valid type
            return $type;
        }

        // check whether the sub type matches
        foreach ($possibleTypes as $value) {
            [, $subType] = explode('-', $value);
            if ($subType === $type) {
                return $value;
            }
        }

        throw new StatusCode\BadRequestException('Provided an invalid type');
    }
}
