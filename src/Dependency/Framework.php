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

namespace PSX\Framework\Dependency;

use PSX\Api\ApiManager;
use PSX\Api\GeneratorFactory;
use PSX\Api\Listing\CachedListing;
use PSX\Api\Listing\FilterFactory;
use PSX\Framework\Api\ControllerDocumentation;
use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\ControllerFactory;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Exception;
use PSX\Framework\Http\CorsPolicy;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Loader;
use PSX\Framework\Session\Session;

/**
 * Controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait Framework
{
    /**
     * @return \PSX\Framework\Config\Config
     */
    public function getConfig()
    {
        $config = new Config($this->appendDefaultConfig());
        $config = $config->merge(Config::fromFile($this->getParameter('config.file')));

        return $config;
    }

    /**
     * @return \PSX\Framework\Exception\ConverterInterface
     */
    public function getExceptionConverter()
    {
        return new Exception\Converter($this->get('config')->get('psx_debug'));
    }

    /**
     * @return \PSX\Framework\Session\Session
     */
    public function getSession()
    {
        $name    = $this->hasParameter('session.name') ? $this->getParameter('session.name') : 'psx';
        $session = new Session($name);

        if (PHP_SAPI != 'cli') {
            $session->start();
        }

        return $session;
    }

    /**
     * @return \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    public function getControllerFactory()
    {
        return new ControllerFactory(
            $this->get('object_builder'),
            $this
        );
    }

    /**
     * @return \PSX\Framework\Loader\LocationFinderInterface
     */
    public function getLoaderLocationFinder()
    {
        return new Loader\LocationFinder\RoutingParser($this->get('routing_parser'));
    }

    /**
     * @return \PSX\Framework\Loader\Loader
     */
    public function getLoader()
    {
        return new Loader\Loader(
            $this->get('loader_location_finder'),
            $this->get('controller_factory'),
            $this->get('event_dispatcher'),
            $this->get('logger'),
            $this->get('config')
        );
    }

    /**
     * @return \PSX\Framework\Dispatch\Dispatch
     */
    public function getDispatch()
    {
        return new Dispatch(
            $this->get('config'),
            $this->get('loader'),
            $this->get('controller_factory'),
            $this->get('event_dispatcher'),
            $this->get('exception_converter')
        );
    }

    /**
     * @return \PSX\Framework\Loader\RoutingParserInterface
     */
    public function getRoutingParser()
    {
        $routingFile = $this->get('config')->get('psx_routing');

        if (substr($routingFile, -4) == '.php') {
            // for php routing files we dont need a cached parser since PHP can
            // use its internal opcache
            return new Loader\RoutingParser\PhpFile($routingFile);
        } else {
            $routingParser = new Loader\RoutingParser\RoutingFile($routingFile);
        }

        if ($this->get('config')->get('psx_debug')) {
            return $routingParser;
        } else {
            return new Loader\RoutingParser\CachedParser($routingParser, $this->get('cache'));
        }
    }

    /**
     * @return \PSX\Framework\Loader\ReverseRouter
     */
    public function getReverseRouter()
    {
        return new Loader\ReverseRouter(
            $this->get('routing_parser'),
            $this->get('config')->get('psx_url'),
            $this->get('config')->get('psx_dispatch')
        );
    }

    /**
     * @return \PSX\Api\ListingInterface
     */
    public function getResourceListing()
    {
        $resourceListing = new ControllerDocumentation($this->get('routing_parser'), $this->get('controller_factory'));

        if ($this->get('config')->get('psx_debug')) {
            return $resourceListing;
        } else {
            return new CachedListing($resourceListing, $this->get('cache'));
        }
    }

    /**
     * @return \PSX\Api\Listing\FilterFactoryInterface
     */
    public function getListingFilterFactory()
    {
        return new FilterFactory();
    }

    /**
     * @return \PSX\Api\GeneratorFactoryInterface
     */
    public function getGeneratorFactory()
    {
        return new GeneratorFactory(
            $this->get('annotation_reader'),
            $this->get('config')->get('psx_json_namespace'),
            $this->get('config')->get('psx_url'),
            $this->get('config')->get('psx_dispatch')
        );
    }

    /**
     * @return \PSX\Api\ApiManager
     */
    public function getApiManager()
    {
        return new ApiManager(
            $this->get('annotation_reader_controller'),
            $this->get('schema_manager'),
            $this->get('cache'),
            $this->get('config')->get('psx_debug')
        );
    }

    /**
     * @return \PSX\Framework\Http\CorsPolicy
     */
    public function getCorsPolicy()
    {
        return new CorsPolicy(
            $this->get('config')
        );
    }

    /**
     * @return \PSX\Framework\Http\RequestReader
     */
    public function getRequestReader()
    {
        return new RequestReader($this->get('io'));
    }

    /**
     * @return \PSX\Framework\Http\ResponseWriter
     */
    public function getResponseWriter()
    {
        return new ResponseWriter(
            $this->get('io'),
            $this->get('config')->get('psx_supported_writer')
        );
    }
}
