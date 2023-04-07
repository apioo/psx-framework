<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Loader;

use InvalidArgumentException;

/**
 * ReverseRouter
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ReverseRouter
{
    private RoutingParserInterface $routingParser;
    private string $url;
    private string $dispatch;
    private ?string $basePath;

    public function __construct(RoutingParserInterface $routingParser, string $url, string $dispatch)
    {
        $this->routingParser = $routingParser;
        $this->url           = $url;
        $this->dispatch      = $dispatch;
        $this->basePath      = parse_url($this->url, PHP_URL_PATH);
    }

    public function getPath(array $source, array $parameters = array(), $leadingPath = true): ?string
    {
        $path = $this->getPathBySource($source);
        if ($path === null) {
            return null;
        }

        $path  = explode('/', trim($path, '/'));
        $parts = array();
        $i     = 0;

        foreach ($path as $key => $part) {
            if (isset($part[0]) && ($part[0] == ':' || $part[0] == '*')) {
                $name = substr($part, 1);

                if (isset($parameters[$name])) {
                    $parts[] = $parameters[$name];
                } elseif (isset($parameters[$i])) {
                    $parts[] = $parameters[$i];
                } else {
                    throw new InvalidArgumentException('Missing parameter ' . $name);
                }

                $i++;
            } elseif (isset($part[0]) && $part[0] == '$') {
                $pos  = strpos($part, '<');
                $name = substr($part, 1, $pos - 1);
                $rexp = substr($part, $pos + 1, -1);

                if (isset($parameters[$name]) && preg_match('/' . $rexp . '/', $parameters[$name])) {
                    $parts[] = $parameters[$name];
                } elseif (isset($parameters[$i]) && preg_match('/' . $rexp . '/', $parameters[$i])) {
                    $parts[] = $parameters[$i];
                } else {
                    throw new InvalidArgumentException('Missing parameter ' . $name);
                }

                $i++;
            } else {
                $parts[] = $part;
            }
        }

        $path = implode('/', $parts);

        if ($this->isAbsoluteUrl($path)) {
            return $path;
        }

        return ($leadingPath ? '/' : '') . $path;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getDispatchUrl(): string
    {
        return $this->url . '/' . $this->dispatch;
    }

    public function getAbsolutePath($source, array $parameters = []): ?string
    {
        $path = $this->getPath($source, $parameters, false);
        if ($path === null) {
            return null;
        }

        if ($this->isAbsoluteUrl($path)) {
            return $path;
        } else {
            return $this->basePath . '/' . $this->dispatch . $path;
        }
    }

    public function getUrl($source, array $parameters = []): ?string
    {
        $path = $this->getPath($source, $parameters, false);
        if ($path === null) {
            return null;
        }

        if ($this->isAbsoluteUrl($path)) {
            return $path;
        } else {
            return $this->getDispatchUrl() . $path;
        }
    }

    private function getPathBySource(array $source): ?string
    {
        $routingCollection = $this->routingParser->getCollection();

        foreach ($routingCollection as $routing) {
            if ($routing[RoutingCollection::ROUTING_SOURCE] == $source) {
                return $routing[RoutingCollection::ROUTING_PATH];
            }
        }

        return null;
    }

    private function isAbsoluteUrl(string $path): bool
    {
        return substr($path, 0, 7) == 'http://' || substr($path, 0, 8) == 'https://';
    }
}
