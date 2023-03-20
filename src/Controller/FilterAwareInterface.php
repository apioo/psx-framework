<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

/**
 * A controller can implement this interface to add a middleware before or after the controller invocation. It receives
 * the raw HTTP request and response and can add specific behaviour to it. Those middlewares are only attached to the
 * local controller, if you want to add a global middleware you can register a service with the "psx.pre_filter" or
 * "psx.post_filter" tag, those middlewares are then globally applied to every controller
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface FilterAwareInterface
{
    /**
     * Returns an array of service ids as middleware, the service must implement the PSX\Http\FilterInterface. The
     * service is invoked before the controller
     *
     * @return string[]
     */
    public function getPreFilter(): array;

    /**
     * Returns an array of service ids as middleware, the service must implement the PSX\Http\FilterInterface. The
     * service is invoked after the controller
     *
     * @return string[]
     */
    public function getPostFilter(): array;
}
