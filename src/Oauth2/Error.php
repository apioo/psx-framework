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

namespace PSX\Framework\Oauth2;

use PSX\Record\Record;

/**
 * Error
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Error extends Record
{
    public function setError($error)
    {
        $this->setProperty('error', $error);
    }
    
    public function getError()
    {
        return $this->getProperty('error');
    }

    public function setErrorDescription($errorDescription)
    {
        $this->setProperty('error_description', $errorDescription);
    }
    
    public function getErrorDescription()
    {
        return $this->getProperty('error_description');
    }

    public function setErrorUri($errorUri)
    {
        $this->setProperty('error_uri', $errorUri);
    }
    
    public function getErrorUri()
    {
        return $this->getProperty('error_uri');
    }

    public function setState($state)
    {
        $this->setProperty('state', $state);
    }
    
    public function getState()
    {
        return $this->getProperty('state');
    }
}
