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

namespace PSX\Framework\Controller;

use PSX\Framework\Http\Writer;
use PSX\Http\ResponseInterface;

/**
 * Controller which has helper methods to render a HTML view using a template
 * engine
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ViewAbstract extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    /**
     * @param \PSX\Http\ResponseInterface $response
     * @param string $templateFile
     * @param mixed $data
     */
    public function render(ResponseInterface $response, $templateFile, $data)
    {
        $body = new Writer\Template($data, $templateFile, $this->reverseRouter);

        $this->responseWriter->setBody($response, $body);
    }
}
