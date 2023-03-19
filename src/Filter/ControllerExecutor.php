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

namespace PSX\Framework\Filter;

use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\OperationInterface;
use PSX\Api\Parser\Attribute;
use PSX\Api\SpecificationInterface;
use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\DateTime\Duration;
use PSX\DateTime\Exception\InvalidFormatException;
use PSX\DateTime\Time;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Loader\Context;
use PSX\Framework\Util\HeaderName;
use PSX\Http\FilterChainInterface;
use PSX\Http\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\TypeNotFoundException;
use PSX\Schema\Schema;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\AnyType;
use PSX\Schema\Type\BooleanType;
use PSX\Schema\Type\IntegerType;
use PSX\Schema\Type\NumberType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StringType;
use PSX\Schema\Type\TypeAbstract;
use PSX\Schema\TypeInterface;
use PSX\Uri\Uri;

/**
 * ControllerExecutor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerExecutor implements FilterInterface
{
    private object $controller;
    private string $methodName;
    private Context $context;
    private RequestReader $requestReader;
    private ResponseWriter $responseWriter;
    private SchemaManagerInterface $schemaManager;
    private CacheItemPoolInterface $cache;

    public function __construct(object $controller, string $methodName, Context $context, RequestReader $requestReader, ResponseWriter $responseWriter, SchemaManagerInterface $schemaManager, CacheItemPoolInterface $cache)
    {
        $this->controller = $controller;
        $this->methodName = $methodName;
        $this->context = $context;
        $this->requestReader = $requestReader;
        $this->responseWriter = $responseWriter;
        $this->schemaManager = $schemaManager;
        $this->cache = $cache;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain): void
    {
        $specification = $this->getSpecification(get_class($this->controller));

        $operationId = Attribute::buildOperationId(get_class($this->controller), $this->methodName);
        $operation = $specification->getOperations()->get($operationId);

        $response->setStatus($operation->getReturn()->getCode());
        $response->setHeader('X-Operation-Id', $operationId);
        if ($operation->getStability() === OperationInterface::STABILITY_DEPRECATED) {
            $response->setHeader('X-Stability', 'deprecated');
        } elseif ($operation->getStability() === OperationInterface::STABILITY_EXPERIMENTAL) {
            $response->setHeader('X-Stability', 'experimental');
        } elseif ($operation->getStability() === OperationInterface::STABILITY_STABLE) {
            $response->setHeader('X-Stability', 'stable');
        } elseif ($operation->getStability() === OperationInterface::STABILITY_LEGACY) {
            $response->setHeader('X-Stability', 'legacy');
        }

        if ($request->getMethod() === 'OPTIONS') {
            // for OPTIONS requests we dont execute the controller
        } else {
            $arguments = $this->buildArguments($operation, $request, $specification->getDefinitions());

            $result = call_user_func_array([$this->controller, $this->methodName], $arguments);

            $this->responseWriter->setBody($response, $result, $request);
        }

        $filterChain->handle($request, $response);
    }

    /**
     * @param OperationInterface $operation
     * @param RequestInterface $request
     * @param DefinitionsInterface $definitions
     * @throws TypeNotFoundException
     */
    private function buildArguments(OperationInterface $operation, RequestInterface $request, DefinitionsInterface $definitions): array
    {
        $result = [];
        foreach ($operation->getArguments()->getAll() as $name => $argument) {
            if ($argument->getIn() === 'path') {
                $value = $this->context->getParameter($name);
                $result[] = $this->castToType($argument->getSchema(), $value);
            } elseif ($argument->getIn() === 'header') {
                $value = $request->getHeader(HeaderName::convert($name));
                $result[] = $this->castToType($argument->getSchema(), $value);
            } elseif ($argument->getIn() === 'query') {
                $value = $request->getUri()->getParameter($name);
                $result[] = $this->castToType($argument->getSchema(), $value);
            } elseif ($argument->getIn() === 'body') {
                $result[] = $this->parseRequest($argument->getSchema(), $request, $definitions);
            }
        }

        return $result;
    }

    /**
     * @throws InvalidFormatException
     */
    private function castToType(TypeInterface $type, mixed $value): mixed
    {
        if ($type instanceof StringType) {
            return match ($type->getFormat()) {
                TypeAbstract::FORMAT_BINARY => $this->buildResource($value),
                TypeAbstract::FORMAT_DATETIME => new DateTime($value),
                TypeAbstract::FORMAT_DATE => new Date($value),
                TypeAbstract::FORMAT_TIME => new Time($value),
                TypeAbstract::FORMAT_DURATION => new Duration($value),
                TypeAbstract::FORMAT_URI => new Uri($value),
                default => (string) $value,
            };
        } elseif ($type instanceof IntegerType) {
            return (int) $value;
        } elseif ($type instanceof NumberType) {
            return (float) $value;
        } elseif ($type instanceof BooleanType) {
            return (bool) $value;
        } elseif ($type instanceof AnyType) {
            return $value;
        }

        return $value;
    }

    private function buildResource(mixed $value): mixed
    {
        $handle = fopen('php://temp', 'rw');
        if ($handle === false) {
            throw new \RuntimeException('Could not open resource');
        }
        fwrite($handle, base64_decode((string) $value) ?: '');
        fseek($handle, 0);
        return $handle;
    }

    /**
     * @throws TypeNotFoundException
     */
    private function parseRequest(TypeInterface $type, RequestInterface $request, DefinitionsInterface $definitions): mixed
    {
        if (!$type instanceof ReferenceType) {
            return null;
        }

        if ($type->getRef() === 'Passthru') {
            $data = $this->requestReader->getBody($request);
        } else {
            $schema = new Schema($definitions->getType($type->getRef()), $definitions);
            $data = $this->requestReader->getBodyAs($request, $schema);
        }

        return $data;
    }

    private function getSpecification(string $controllerClass): SpecificationInterface
    {
        $item = $this->cache->getItem('psx-spec-' . str_replace('\\', '-', $controllerClass));
        if ($item->isHit()) {
            return $item->get();
        }

        $parser = new Attribute($this->schemaManager, true);
        $spec = $parser->parse($controllerClass);

        $item->set($spec);
        $this->cache->save($item);

        return $spec;
    }
}
