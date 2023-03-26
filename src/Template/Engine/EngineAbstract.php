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

namespace PSX\Framework\Template\Engine;

use PSX\Framework\Template\TemplateInterface;

/**
 * EngineAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class EngineAbstract implements TemplateInterface
{
    protected ?string $dir = null;
    protected ?string $file = null;
    protected array $data = [];

    public function setDir(string $dir): void
    {
        $this->dir = $dir;
    }

    public function getDir(): ?string
    {
        return $this->dir;
    }

    public function set(string $file): void
    {
        $this->file = $file;
    }

    public function get(): ?string
    {
        return $this->file;
    }

    public function hasFile(): bool
    {
        return !empty($this->file);
    }

    public function getFile(): ?string
    {
        return $this->dir != null ? $this->dir . '/' . $this->file : $this->file;
    }

    public function isFileAvailable(): bool
    {
        return is_file($this->getFile());
    }

    public function isAbsoluteFile(): bool
    {
        return is_file($this->file);
    }

    public function assign(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }
}
