<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Twig
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Twig extends EngineAbstract
{
    private bool $cache;
    private bool $debug;

    public function __construct(bool $cache = false, bool $debug = false)
    {
        $this->cache = $cache;
        $this->debug = $debug;
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function transform(): string
    {
        $loader = new FilesystemLoader($this->dir);
        $twig   = new Environment($loader, array(
            'cache' => $this->cache,
            'debug' => $this->debug,
        ));

        return $twig->render($this->file, $this->data);
    }
}
