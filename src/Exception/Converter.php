<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Framework\Exception;

use PSX\Framework\DisplayException;
use PSX\Framework\Template\ErrorException;
use PSX\Http\Exception as StatusCode;
use PSX\Model\Error;
use PSX\Schema;
use PSX\Validate;

/**
 * Converter
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Converter implements ConverterInterface
{
    public const CONTEXT_SIZE = 4;

    private bool $debug;
    private int $contextSize;

    public function __construct(bool $debug, int $contextSize = self::CONTEXT_SIZE)
    {
        $this->debug = $debug;
        $this->contextSize = $contextSize;
    }

    public function convert(\Throwable $exception): Error
    {
        if ($this->debug === true) {
            if ($exception instanceof ErrorException) {
                $exception = $exception->getOriginException();
            }

            $title   = get_class($exception);
            $message = $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine();
            $trace   = $exception->getTraceAsString();
            $context = '';

            if (is_file($exception->getFile())) {
                $offset = $exception->getLine() - ($this->contextSize + 1);
                $length = ($this->contextSize * 2) + 1;
                $length = $offset < 0 ? $length + $offset : $length;
                $offset = $offset < 0 ? 0 : $offset;

                $lines  = file($exception->getFile());
                $lines  = array_slice($lines, $offset, $length);

                foreach ($lines as $number => $line) {
                    $lineNo = $offset + $number + 1;

                    if ($lineNo == $exception->getLine()) {
                        $context.= '<b>' . str_pad($lineNo, 4) . htmlspecialchars($line) . '</b>';
                    } else {
                        $context.= str_pad($lineNo, 4) . htmlspecialchars($line);
                    }
                }
            }
        } else {
            // if we have an display exception we can use the error message else
            // we hide the message with an general error message
            if ($exception instanceof DisplayException || $exception instanceof Schema\Exception\ValidationException || $exception instanceof Validate\ValidationException || $exception instanceof StatusCode\StatusCodeException) {
                $message = $exception->getMessage();
            } else {
                $message = 'The server encountered an internal error and was unable to complete your request.';
            }

            $title   = 'Internal Server Error';
            $trace   = null;
            $context = null;
        }

        $record = new Error();
        $record->setSuccess(false);
        $record->setTitle($title);
        $record->setMessage($message);
        $record->setTrace($trace);
        $record->setContext($context);

        return $record;
    }
}
