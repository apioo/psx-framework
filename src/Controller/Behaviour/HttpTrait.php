<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Schema\Validation\ValidatorInterface;
use PSX\Validate\Validate;

/**
 * Provides methods to read and set values from the request/response. It is 
 * recommended to use theses methods inside the controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait HttpTrait
{
    /**
     * @Inject
     * @var \PSX\Validate\Validate
     * @deprecated
     */
    protected $validate;

    /**
     * @Inject
     * @var \PSX\Data\Processor
     * @deprecated
     */
    protected $io;

    /**
     * Returns the request method. Note the X-HTTP-Method-Override header
     * replaces the actually request method if available
     *
     * @deprecated
     * @return string
     */
    protected function getMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * Returns the request uri
     *
     * @deprecated
     * @return \PSX\Uri\Uri
     */
    protected function getUri()
    {
        return $this->request->getUri();
    }

    /**
     * Sets the response status code
     *
     * @deprecated
     * @param integer $code
     */
    protected function setResponseCode($code)
    {
        $this->response->setStatus($code);
    }

    /**
     * Sets a response header
     *
     * @deprecated
     * @param string $name
     * @param string $value
     */
    protected function setHeader($name, $value)
    {
        $this->response->setHeader($name, $value);
    }

    /**
     * Returns a specific request header
     *
     * @deprecated
     * @param string $key
     * @return string
     */
    protected function getHeader($key)
    {
        return $this->request->getHeader($key);
    }

    /**
     * Returns whether a header is available
     *
     * @deprecated
     * @param string $key
     * @return boolean
     */
    protected function hasHeader($key)
    {
        return $this->request->hasHeader($key);
    }

    /**
     * Returns a parameter from the query fragment of the request url
     *
     * @deprecated
     * @param string $key
     * @param string $type
     * @param array $filter
     * @param string $title
     * @param boolean $required
     * @return mixed
     */
    protected function getParameter($key, $type = Validate::TYPE_STRING, array $filter = array(), $title = null, $required = true)
    {
        $parameter = $this->request->getUri()->getParameter($key);

        if (isset($parameter)) {
            return $this->validate->apply($parameter, $type, $filter, $title, $required);
        } else {
            return null;
        }
    }

    /**
     * Returns all available request parameters
     *
     * @deprecated
     * @return array
     */
    protected function getParameters()
    {
        return $this->request->getUri()->getParameters();
    }

    /**
     * Returns the result of the reader for the request
     *
     * @deprecated
     * @param string $readerType
     * @return mixed
     */
    protected function getBody($readerType = null)
    {
        return $this->requestReader->getBody($this->request, $readerType);
    }

    /**
     * @deprecated
     * @param string $schema
     * @param \PSX\Schema\Validation\ValidatorInterface $validator
     * @param string $readerType
     * @return mixed
     */
    protected function getBodyAs($schema, ValidatorInterface $validator = null, $readerType = null)
    {
        return $this->requestReader->getBodyAs($this->request, $schema, $validator, $readerType);
    }

    /**
     * Method to set a response body
     *
     * @deprecated
     * @param mixed $data
     * @param string $writerType
     */
    protected function setBody($data, $writerType = null)
    {
        $this->responseWriter->setBody($this->response, $data, $this->getWriterOptions($this->request, $writerType));
    }
}
