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

namespace PSX\Framework\Messenger;

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

/**
 * MessengerPass
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MessengerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerHandlers($container, [DefaultBus::NAME]);
    }

    private function registerHandlers(ContainerBuilder $container, array $busIds): void
    {
        $handlersByBusAndMessage = [];

        foreach ($container->findTaggedServiceIds('psx.messenger_handler', true) as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['bus']) && !\in_array($tag['bus'], $busIds, true)) {
                    throw new RuntimeException(sprintf('Invalid handler service "%s": bus "%s" specified on the tag "psx.messenger_handler" does not exist (known ones are: "%s").', $serviceId, $tag['bus'], implode('", "', $busIds)));
                }

                $className = $this->getServiceClass($container, $serviceId);
                $r = $container->getReflectionClass($className);

                if (null === $r) {
                    throw new RuntimeException(sprintf('Invalid service "%s": class "%s" does not exist.', $serviceId, $className));
                }

                if (isset($tag['handles'])) {
                    $handles = isset($tag['method']) ? [$tag['handles'] => $tag['method']] : [$tag['handles']];
                } else {
                    $handles = $this->guessHandledClasses($r, $serviceId, $tag['method'] ?? '__invoke');
                }

                $message = null;
                $handlerBuses = (array) ($tag['bus'] ?? $busIds);

                foreach ($handles as $message => $options) {
                    $buses = $handlerBuses;

                    if (\is_int($message)) {
                        if (\is_string($options)) {
                            $message = $options;
                            $options = [];
                        } else {
                            throw new RuntimeException(sprintf('The handler configuration needs to return an array of messages or an associated array of message and configuration. Found value of type "%s" at position "%d" for service "%s".', get_debug_type($options), $message, $serviceId));
                        }
                    }

                    if (\is_string($options)) {
                        $options = ['method' => $options];
                    }

                    $options += array_filter($tag);
                    unset($options['handles']);
                    $priority = $options['priority'] ?? 0;
                    $method = $options['method'] ?? '__invoke';

                    if (isset($options['bus'])) {
                        if (!\in_array($options['bus'], $busIds)) {
                            // @deprecated since Symfony 6.2, in 7.0 change to:
                            // $messageLocation = isset($tag['handles']) ? 'declared in your tag attribute "handles"' : sprintf('used as argument type in method "%s::%s()"', $r->getName(), $method);
                            $messageLocation = isset($tag['handles']) ? 'declared in your tag attribute "handles"' : ($r->implementsInterface(MessageSubscriberInterface::class) ? sprintf('returned by method "%s::getHandledMessages()"', $r->getName()) : sprintf('used as argument type in method "%s::%s()"', $r->getName(), $method));

                            throw new RuntimeException(sprintf('Invalid configuration '.$messageLocation.' for message "%s": bus "%s" does not exist.', $message, $options['bus']));
                        }

                        $buses = [$options['bus']];
                    }

                    if ('*' !== $message && !class_exists($message) && !interface_exists($message, false)) {
                        // @deprecated since Symfony 6.2, in 7.0 change to:
                        // $messageLocation = isset($tag['handles']) ? 'declared in your tag attribute "handles"' : sprintf('used as argument type in method "%s::%s()"', $r->getName(), $method);
                        $messageLocation = isset($tag['handles']) ? 'declared in your tag attribute "handles"' : ($r->implementsInterface(MessageSubscriberInterface::class) ? sprintf('returned by method "%s::getHandledMessages()"', $r->getName()) : sprintf('used as argument type in method "%s::%s()"', $r->getName(), $method));

                        throw new RuntimeException(sprintf('Invalid handler service "%s": class or interface "%s" '.$messageLocation.' not found.', $serviceId, $message));
                    }

                    if (!$r->hasMethod($method)) {
                        throw new RuntimeException(sprintf('Invalid handler service "%s": method "%s::%s()" does not exist.', $serviceId, $r->getName(), $method));
                    }

                    foreach ($buses as $handlerBus) {
                        $handlersByBusAndMessage[$handlerBus][$message][$priority][] = [$serviceId, $options];
                    }
                }

                if (null === $message) {
                    throw new RuntimeException(sprintf('Invalid handler service "%s": method "%s::getHandledMessages()" must return one or more messages.', $serviceId, $r->getName()));
                }
            }
        }

        foreach ($handlersByBusAndMessage as $bus => $handlersByMessage) {
            foreach ($handlersByMessage as $message => $handlersByPriority) {
                krsort($handlersByPriority);
                $handlersByBusAndMessage[$bus][$message] = array_merge(...$handlersByPriority);
            }
        }

        $handlersLocatorMappingByBus = [];
        foreach ($handlersByBusAndMessage as $bus => $handlersByMessage) {
            foreach ($handlersByMessage as $message => $handlers) {
                $handlerDescriptors = [];
                foreach ($handlers as $handler) {
                    $handlerDescriptors[] = new Reference($handler[0]);
                }

                $handlersLocatorMappingByBus[$bus][$message] = new IteratorArgument($handlerDescriptors);
            }
        }

        foreach ($busIds as $bus) {
            $container->getDefinition(HandlersLocator::class)
                ->replaceArgument(0, $handlersLocatorMappingByBus[$bus] ?? []);
        }
    }

    private function guessHandledClasses(\ReflectionClass $handlerClass, string $serviceId, string $methodName): iterable
    {
        try {
            $method = $handlerClass->getMethod($methodName);
        } catch (\ReflectionException) {
            throw new RuntimeException(sprintf('Invalid handler service "%s": class "%s" must have an "%s()" method.', $serviceId, $handlerClass->getName(), $methodName));
        }

        if (0 === $method->getNumberOfRequiredParameters()) {
            throw new RuntimeException(sprintf('Invalid handler service "%s": method "%s::%s()" requires at least one argument, first one being the message it handles.', $serviceId, $handlerClass->getName(), $methodName));
        }

        $parameters = $method->getParameters();

        /** @var \ReflectionNamedType|\ReflectionUnionType|null */
        $type = $parameters[0]->getType();

        if (!$type) {
            throw new RuntimeException(sprintf('Invalid handler service "%s": argument "$%s" of method "%s::%s()" must have a type-hint corresponding to the message class it handles.', $serviceId, $parameters[0]->getName(), $handlerClass->getName(), $methodName));
        }

        if ($type instanceof \ReflectionUnionType) {
            $types = [];
            $invalidTypes = [];
            foreach ($type->getTypes() as $type) {
                if (!$type->isBuiltin()) {
                    $types[] = (string) $type;
                } else {
                    $invalidTypes[] = (string) $type;
                }
            }

            if ($types) {
                return ('__invoke' === $methodName) ? $types : array_fill_keys($types, $methodName);
            }

            throw new RuntimeException(sprintf('Invalid handler service "%s": type-hint of argument "$%s" in method "%s::__invoke()" must be a class , "%s" given.', $serviceId, $parameters[0]->getName(), $handlerClass->getName(), implode('|', $invalidTypes)));
        }

        if ($type->isBuiltin()) {
            throw new RuntimeException(sprintf('Invalid handler service "%s": type-hint of argument "$%s" in method "%s::%s()" must be a class , "%s" given.', $serviceId, $parameters[0]->getName(), $handlerClass->getName(), $methodName, $type instanceof \ReflectionNamedType ? $type->getName() : (string) $type));
        }

        return ('__invoke' === $methodName) ? [$type->getName()] : [$type->getName() => $methodName];
    }

    private function getServiceClass(ContainerBuilder $container, string $serviceId): string
    {
        while (true) {
            $definition = $container->findDefinition($serviceId);

            if (!$definition->getClass() && $definition instanceof ChildDefinition) {
                $serviceId = $definition->getParent();

                continue;
            }

            return $definition->getClass();
        }
    }
}
