<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Console;

use PSX\Api\GeneratorInterface as ApiGeneratorInterface;
use PSX\Api\Parser\Raml;
use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\Generator;
use PSX\Api\ListingInterface;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Data\ExporterInterface;
use PSX\Schema\Builder;
use PSX\Schema\Generator\Php;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

/**
 * GenerateCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generates an API controller based on a schema definition')
            ->addArgument('file', InputArgument::REQUIRED, 'The absolute file path of the schema definition')
            ->addArgument('path', InputArgument::REQUIRED, 'The API endpoint path which should be used from the schema definition')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Namespace under which the classes are generated')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Executes no file operations if set and prints all actions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $this->getNamespace($input->getArgument('namespace'));
        $resource  = $this->getResource($input->getArgument('file'), $input->getArgument('path'));
        $dryRun    = $input->getOption('dry-run');

        if ($resource instanceof Resource) {
            // generate php code
            $controllerSource = $this->getControllerSource($resource, $namespace);
            $schemaSource     = $this->getSchemaSource($resource, $namespace);

            // path settings
            $basePath       = $this->getPath($namespace);
            $controllerFile = $this->getClassFile($namespace, 'Endpoint');
            $schemaFile     = $this->getClassFile($namespace, 'Schema');

            if (!$this->isDir($basePath)) {
                $output->writeln('Created directory: ' . $basePath);
                if (!$dryRun) {
                    $this->makeDir($basePath);
                }
            }

            if (!$this->isFile($controllerFile)) {
                $output->writeln('Created file: ' . $controllerFile);
                if (!$dryRun) {
                    $this->writeFile($controllerFile, $controllerSource);
                }
            }

            if (!$this->isFile($schemaFile)) {
                $output->writeln('Created file: ' . $schemaFile);
                if (!$dryRun) {
                    $this->writeFile($schemaFile, $schemaSource);
                }
            }

            $output->writeln('Generation successful!');
        } else {
            throw new RuntimeException('Invalid resource');
        }
    }

    /**
     * @param string $file
     * @param string $path
     * @return \PSX\Api\Resource
     */
    protected function getResource($file, $path)
    {
        if (is_file($file)) {
            $content = file_get_contents($file);

            if (strpos($content, '#%RAML') !== false) {
                return Raml::fromFile($file, $path);
            } else {
                throw new InvalidArgumentException('Only RAML files are supported');
            }
        } else {
            throw new InvalidArgumentException('Schema file does not exist');
        }
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param string $namespace
     * @return string
     */
    protected function getControllerSource(Resource $resource, $namespace)
    {
        $generator = new Generator\Php($namespace);

        return $generator->generate($resource);
    }
    
    /**
     * @param \PSX\Api\Resource $resource
     * @param string $namespace
     * @return string
     */
    protected function getSchemaSource(Resource $resource, $namespace)
    {
        $methods = $resource->getMethods();
        $root    = new Builder('root');

        foreach ($methods as $methodName => $method) {
            $request = $method->getRequest();
            if ($request instanceof SchemaInterface) {
                $root->objectType(strtolower($methodName) . 'Request', $request->getDefinition());
            }

            $responses = $method->getResponses();
            foreach ($responses as $response) {
                if ($response instanceof SchemaInterface) {
                    $root->objectType(strtolower($methodName) . 'Response', $response->getDefinition());
                }
            }
        }

        $generator = new Php($namespace);

        return $generator->generate(new Schema($root->getProperty()));
    }

    protected function makeDir($path)
    {
        return mkdir($path, 0744, true);
    }

    protected function writeFile($file, $content)
    {
        return file_put_contents($file, $content);
    }

    protected function isFile($path)
    {
        return is_file($path);
    }

    protected function isDir($path)
    {
        return is_dir($path);
    }

    protected function getClassFile($namespace, $class)
    {
        return $this->getPath($namespace) . DIRECTORY_SEPARATOR . $class . '.php';
    }

    protected function getPath($namespace)
    {
        return PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }

    protected function getNamespace($namespace)
    {
        $parts  = explode('\\', $namespace);
        $result = [];

        foreach ($parts as $part) {
            if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $part)) {
                $result[] = ucfirst($part);
            } else {
                throw new \InvalidArgumentException('Namespace contains an invalid part');
            }
        }

        if (empty($result)) {
            throw new \InvalidArgumentException('Namespace must have at least a vendor name');
        }

        return implode('\\', $result);
    }
}
