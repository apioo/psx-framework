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

namespace PSX\Framework;

use ErrorException;

/**
 * Bootstrap
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Bootstrap
{
    /**
     * Setup an environment for PSX according to the provided configuration
     */
    public static function setupEnvironment(): void
    {
        if (!defined('PSX')) {
            set_error_handler('\PSX\Framework\Bootstrap::errorHandler');
            define('PSX', true);
        }
    }

    public static function errorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (error_reporting() == 0) {
            // if someone adds an @ to the function call to suppress an error message the error reporting is 0 so in
            // this case we dont throw an exception
            return false;
        } elseif ($errno == E_DEPRECATED || $errno == E_USER_DEPRECATED) {
            // for deprecation errors we also use the normal PHP error handling
            return false;
        } else {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
    }
}
