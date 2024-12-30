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

namespace PSX\Framework\Filter;

use PSX\Api\ApiManagerInterface;
use PSX\Api\OperationInterface;
use PSX\Api\Parser\Attribute;
use PSX\Data\Body;
use PSX\Data\Reader;
use PSX\DateTime\Exception\InvalidFormatException;
use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\LocalTime;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Loader\Context;
use PSX\Http\Exception\UnsupportedMediaTypeException;
use PSX\Http\FilterChainInterface;
use PSX\Http\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Schema\ContentType;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Format;
use PSX\Schema\Schema;
use PSX\Schema\Type\AnyPropertyType;
use PSX\Schema\Type\BooleanPropertyType;
use PSX\Schema\Type\IntegerPropertyType;
use PSX\Schema\Type\NumberPropertyType;
use PSX\Schema\Type\PropertyTypeAbstract;
use PSX\Schema\Type\ReferencePropertyType;
use PSX\Schema\Type\StringPropertyType;

/**
 * ControllerExecutor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ControllerExecutor implements FilterInterface
{
    private object $controller;
    private string $methodName;
    private Context $context;
    private RequestReader $requestReader;
    private ResponseWriter $responseWriter;
    private ApiManagerInterface $apiManager;
    private Attribute\BuilderInterface $builder;

    public function __construct(object $controller, string $methodName, Context $context, RequestReader $requestReader, ResponseWriter $responseWriter, ApiManagerInterface $apiManager, Attribute\BuilderInterface $builder)
    {
        $this->controller = $controller;
        $this->methodName = $methodName;
        $this->context = $context;
        $this->requestReader = $requestReader;
        $this->responseWriter = $responseWriter;
        $this->apiManager = $apiManager;
        $this->builder = $builder;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain): void
    {
        $specification = $this->apiManager->getApi(get_class($this->controller));

        $operationId = $this->builder->buildOperationId(get_class($this->controller), $this->methodName);
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

    private function buildArguments(OperationInterface $operation, RequestInterface $request, DefinitionsInterface $definitions): array
    {
        $result = [];
        $arguments = $this->builder->buildArguments($this->controller::class, $this->methodName);
        foreach ($arguments as $parameterName => $realName) {
            $argument = $operation->getArguments()->get($realName);
            if ($argument->getIn() === 'path') {
                $value = $this->context->getParameter($realName);
                $result[$parameterName] = $this->castToType($argument->getSchema(), $value);
            } elseif ($argument->getIn() === 'header') {
                $value = $request->getHeader($realName);
                $result[$parameterName] = $this->castToType($argument->getSchema(), $value);
            } elseif ($argument->getIn() === 'query') {
                $value = $request->getUri()->getParameter($realName);
                $result[$parameterName] = $this->castToType($argument->getSchema(), $value);
            } elseif ($argument->getIn() === 'body') {
                $result[$parameterName] = $this->parseRequest($argument->getSchema(), $request, $definitions);
            }
        }

        return $result;
    }

    /**
     * @throws InvalidFormatException
     */
    private function castToType(PropertyTypeAbstract $type, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($type instanceof StringPropertyType) {
            return match ($type->getFormat()) {
                Format::DATE => LocalDate::parse($value),
                Format::DATETIME => LocalDateTime::parse($value),
                Format::TIME => LocalTime::parse($value),
                default => (string) $value,
            };
        } elseif ($type instanceof IntegerPropertyType) {
            return (int) $value;
        } elseif ($type instanceof NumberPropertyType) {
            return (float) $value;
        } elseif ($type instanceof BooleanPropertyType) {
            return (bool) $value;
        } elseif ($type instanceof AnyPropertyType) {
            return $value;
        }

        return $value;
    }

    private function parseRequest(PropertyTypeAbstract|ContentType $type, RequestInterface $request, DefinitionsInterface $definitions): mixed
    {
        if ($type instanceof ContentType) {
            return match ($type->toString()) {
                ContentType::BINARY => $request->getBody(),
                ContentType::FORM => Body\Form::from($this->requestReader->getBody($request, Reader\Form::class)),
                ContentType::JSON => Body\Json::from($this->requestReader->getBody($request, Reader\Json::class)),
                ContentType::MULTIPART => $this->getMultipart($this->requestReader->getBody($request, Reader\Multipart::class)),
                ContentType::TEXT => (string) $request->getBody(),
            };
        }

        if (!$type instanceof ReferencePropertyType) {
            return null;
        }

        if ($type->getTarget() === 'Passthru') {
            $data = $this->requestReader->getBody($request);
        } else {
            $data = $this->requestReader->getBodyAs($request, new Schema($definitions, $type->getTarget()));
        }

        return $data;
    }

    private function getMultipart(mixed $return): Body\Multipart
    {
        if (!$return instanceof Body\Multipart) {
            throw new UnsupportedMediaTypeException('Provided an invalid content type, must be multipart/form-data');
        }

        return $return;
    }
}
