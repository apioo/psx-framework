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

namespace PSX\Framework\Controller\Behaviour;

use PSX\Http\Exception as StatusCode;
use PSX\Uri\Url;
use RuntimeException;

/**
 * All methods in this trait are deprecated please work either directly on the
 * request/response object or use a fitting service. This class may be removed
 * in future releases
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @deprecated
 */
trait RedirectTrait
{
    /**
     * @Inject
     * @var \PSX\Framework\Loader\Loader
     * @deprecated
     */
    protected $loader;

    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     * @deprecated
     */
    protected $reverseRouter;

    /**
     * Forwards the request to another controller
     *
     * @param string $source
     * @param array $parameters
     * @deprecated
     */
    protected function forward($source, array $parameters = array())
    {
        $path = $this->reverseRouter->getPath($source, $parameters);

        if ($path !== null) {
            $this->request->setUri($this->request->getUri()->withPath($path));

            $this->loader->load($this->request, $this->response, $this->context);
        } else {
            throw new RuntimeException('Could not find route for source ' . $source);
        }
    }

    /**
     * Throws an redirect exception which sends an Location header. If source is
     * not an url the reverse router is used to determine the url
     *
     * @param string $source
     * @param array $parameters
     * @param integer $code
     * @deprecated
     */
    protected function redirect($source, array $parameters = array(), $code = 307)
    {
        if ($source instanceof Url) {
            $url = $source->toString();
        } elseif (filter_var($source, FILTER_VALIDATE_URL)) {
            $url = $source;
        } else {
            $url = $this->reverseRouter->getUrl($source, $parameters);
        }

        if ($code == 301) {
            throw new StatusCode\MovedPermanentlyException($url);
        } elseif ($code == 302) {
            throw new StatusCode\FoundException($url);
        } elseif ($code == 307) {
            throw new StatusCode\TemporaryRedirectException($url);
        } else {
            throw new RuntimeException('Invalid redirect status code');
        }
    }
}
