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

namespace PSX\Framework\Template;

/**
 * Interface which describes a template engine. A template engine uses a 
 * template file to transform the provided data into the response
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface TemplateInterface
{
    /**
     * Sets the dir from where to load the template file
     */
    public function setDir(string $dir): void;

    /**
     * Returns the dir
     */
    public function getDir(): ?string;

    /**
     * Sets the current template file
     */
    public function set(string $file): void;

    /**
     * Returns the template file wich was set
     */
    public function get(): ?string;

    /**
     * Returns whether a template file was set or not
     */
    public function hasFile(): bool;

    /**
     * Returns the path of the template dir and file
     */
    public function getFile(): ?string;

    /**
     * Returns true if the template engine can resolve a template file with the given dir and file parameters
     */
    public function isFileAvailable(): bool;

    /**
     * Returns true if the given file is an absolute file path
     */
    public function isAbsoluteFile(): bool;

    /**
     * Assigns a variable to the template
     */
    public function assign(string $key, mixed $value): void;

    /**
     * Transforms the template file
     */
    public function transform(): string;
}
