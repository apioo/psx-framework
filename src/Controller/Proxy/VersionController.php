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

namespace PSX\Framework\Controller\Proxy;

use PSX\Api\DocumentedInterface;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use RuntimeException;

/**
 * Through the version controller it is possible to redirect the request to
 * different controllers depending on the provided version
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class VersionController extends ControllerAbstract implements DocumentedInterface
{
    const TYPE_ACCEPT = 0x1;
    const TYPE_URI    = 0x2;
    const TYPE_HEADER = 0x3;

    /**
     * @Inject
     * @var \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    protected $controllerFactory;

    /**
     * @Inject
     * @var \PSX\Framework\Loader\Loader
     */
    protected $loader;

    /**
     * @var string
     */
    protected $acceptPattern = 'application\/vnd\.psx\.v(?<version>[\d]+)\+(json|xml)';

    /**
     * @var string
     */
    protected $uriFragment = 'version';

    /**
     * @var string
     */
    protected $headerName = 'Api-Version';

    public function onRequest(RequestInterface $request, ResponseInterface $response)
    {
        $type     = $this->getVersionType();
        $versions = $this->getVersions();
        $version  = null;

        if ($type === self::TYPE_ACCEPT) {
            $version = (int) $this->getAcceptVersion($request);
        } elseif ($type == self::TYPE_URI) {
            $version = (int) $this->getUriVersion();
        } elseif ($type == self::TYPE_HEADER) {
            $version = (int) $this->getHeaderVersion($request);
        } else {
            throw new RuntimeException('Invalid version type');
        }

        if (empty($version)) {
            // in case we have no version number get the last available version
            $class = end($versions);
        } elseif (isset($versions[$version])) {
            $class = $versions[$version];

            $this->context->setVersion($version);
        } else {
            throw new StatusCode\NotAcceptableException('Version is not available');
        }

        $controller = $this->controllerFactory->getController($class, $this->context);

        $this->loader->execute($controller, $request, $response);
    }

    public function getDocumentation($version = null)
    {
        $versions = $this->getVersions();
        $class    = null;

        if (empty($version)) {
            $class = end($versions);
        } elseif (isset($versions[$version])) {
            $class = $versions[$version];
        }

        if (!empty($class)) {
            return $this->controllerFactory->getDocumentation($class, $this->context, $version);
        }

        return null;
    }

    protected function getAcceptVersion(RequestInterface $request)
    {
        $accept  = $request->getHeader('Accept');
        $matches = array();

        preg_match('/^' . $this->acceptPattern . '$/', $accept, $matches);

        return isset($matches['version']) ? $matches['version'] : null;
    }

    protected function getUriVersion()
    {
        return $this->context->getParameter($this->uriFragment);
    }

    protected function getHeaderVersion(RequestInterface $request)
    {
        return $request->getHeader($this->headerName);
    }

    protected function getVersionType()
    {
        return self::TYPE_ACCEPT;
    }

    /**
     * Must return an array which contains as key the version number and as
     * value the name of the controller class
     *
     * @return array
     */
    abstract protected function getVersions();
}

