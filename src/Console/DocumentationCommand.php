<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\ListingInterface;
use PSX\Api\Resource;
use PSX\Api\Generator;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Schema\Generator\Html;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DocumentationCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationCommand extends Command
{
    /**
     * @var \PSX\Framework\Loader\RoutingParserInterface
     */
    protected $routingParser;

    /**
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    public function __construct(RoutingParserInterface $routingParser, ListingInterface $resourceListing)
    {
        parent::__construct();

        $this->routingParser   = $routingParser;
        $this->resourceListing = $resourceListing;
    }

    protected function configure()
    {
        $this
            ->setName('documentation')
            ->setDescription('Generates an API documentation for every endpoint')
            ->addArgument('dir', InputArgument::REQUIRED, 'The target directory')
            ->addArgument('format', InputArgument::OPTIONAL, 'Optional the output format possible values are: html, markdown, php');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $this->routingParser->getCollection();
        $progress   = new ProgressBar($output, count($collection));

        $dir    = $input->getArgument('dir');
        $format = $input->getArgument('format') ?: 'markdown';

        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('Directory does not exist');
        }

        $progress->start();

        foreach ($collection as $route) {
            $progress->setMessage('Generating ' . $route[1]);

            $file    = $dir . '/' . $this->getFileName($route[1], $format);
            $content = $this->generateResource($route[1], $format);
            file_put_contents($file, $content);

            $progress->advance();
        }

        $progress->finish();

        $output->writeln('Successful!');
    }
    
    private function generateResource($path, $format)
    {
        $resource = $this->resourceListing->getResource($path);

        if ($resource instanceof Resource) {
            return $this->getGenerator($format)->generate($resource);
        } else {
            return null;
        }
    }

    private function getGenerator($format)
    {
        switch ($format) {
            case 'php':
                return new Generator\Php();

            case 'html':
                return new Generator\Html();

            case 'markdown':
            default:
                return new Generator\Markdown(new Html(4));
        }
    }

    private function getFileName($route, $format)
    {
        $route = trim($route, '/');
        $route = preg_replace('/[^A-Za-z0-9]/', '_', $route);
        $ext   = $this->getFileExtension($format);

        return $route . '.' . $ext;
    }

    private function getFileExtension($format)
    {
        switch ($format) {
            case 'php':
                return 'php';

            case 'html':
                return 'html';

            case 'markdown':
                return 'md';

            default:
                return 'txt';
        }
    }
}
