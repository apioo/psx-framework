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

namespace PSX\Framework\Loader\LocationFinder;

use Closure;
use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Http\RequestInterface;

/**
 * Calls an Closure method in order to resolve a path
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CallbackMethod implements LocationFinderInterface
{
    protected $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    public function resolve(RequestInterface $request, Context $context)
    {
        return call_user_func_array($this->callback, array($request, $context));
    }
}
