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

namespace PSX\Framework\Config;

/**
 * BaseUrl
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class BaseUrl implements BaseUrlInterface
{
    private string $url;
    private string $path;
    private ?string $dispatch;

    public function __construct(?string $url, ?string $dispatch)
    {
        $this->url = !empty($url) ? $url : $this->autoDetectUrl();
        $this->path = !empty($url) ? rtrim((string) parse_url($url, PHP_URL_PATH), '/') : '';
        $this->dispatch = $dispatch;
    }

    public function getDispatchUrl(): string
    {
        return $this->url . '/' . $this->dispatch;
    }

    public function getDispatchPath(): string
    {
        return $this->path . '/' . $this->dispatch;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function __toString(): string
    {
        return $this->getDispatchUrl();
    }

    private function autoDetectUrl(): string
    {
        $https = isset($_SERVER['HTTPS']) ? strtolower($_SERVER['HTTPS']) : null;
        $httpsForwarded = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) : null;

        $scheme = $https === 'on' || $httpsForwarded === 'https' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

        return $scheme . '://' . $host;
    }
}
