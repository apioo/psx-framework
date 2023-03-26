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

namespace PSX\Framework\Http\Writer;

use PSX\Data\GraphTraverser;
use PSX\Data\Visitor\StdClassSerializeVisitor;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Template\Engine;
use PSX\Http\ResponseInterface;
use PSX\Http\Writer\Writer;

/**
 * Template
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Template extends Writer
{
    private string $templateFile;
    private ReverseRouter $reverseRouter;

    public function __construct(mixed $data, string $templateFile, ReverseRouter $reverseRouter)
    {
        parent::__construct($data);

        $this->templateFile  = $templateFile;
        $this->reverseRouter = $reverseRouter;
    }

    public function writeTo(ResponseInterface $response): void
    {
        $path = pathinfo($this->templateFile, PATHINFO_DIRNAME);

        $template = $this->newEngine();
        $template->set($this->templateFile);

        // assign default values
        $self   = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
        $render = isset($_SERVER['REQUEST_TIME_FLOAT']) ? round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 6) : 0;

        $template->assign('self', htmlspecialchars($self));
        $template->assign('url', $this->reverseRouter->getDispatchUrl());
        $template->assign('base', $this->reverseRouter->getBasePath());
        $template->assign('render', $render);
        $template->assign('location', $path);
        $template->assign('router', $this->reverseRouter);

        // assign data
        $fields = $this->getNormalizedData($this->data);
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                $template->assign($key, $value);
            }
        }

        $response->getBody()->write($template->transform());
    }

    protected function newEngine()
    {
        return new Engine\Php();
    }

    private function getNormalizedData($data)
    {
        $visitor = new StdClassSerializeVisitor();
        $graph   = new GraphTraverser();
        $graph->traverse($data, $visitor);

        return $visitor->getObject();
    }
}
