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

namespace PSX\Framework\Dispatch;

use PSX\Framework\Loader\Context;

/**
 * Resolves a controller from a source. A key concept in PSX is that in the end
 * a controller is only an array of middleware instances 
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface ControllerFactoryInterface
{
    /**
     * Returns an array of middleware instances based on the provided source
     *
     * @param string $source
     * @param \PSX\Framework\Loader\Context $context
     * @return array
     */
    public function getController($source, Context $context = null);

    /**
     * Returns a documentation resource for the provided controller
     * 
     * @param string $className
     * @param Context|null $context
     * @param string|null $version
     * @return \PSX\Api\Resource|null
     */
    public function getDocumentation($className, Context $context = null, $version = null);
}
