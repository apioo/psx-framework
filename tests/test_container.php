<?php

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use PSX\Framework\Controller\ControllerInterface;
use PSX\Framework\Listener\PHPUnitExceptionListener;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->instanceof(ControllerInterface::class)
        ->tag('psx.controller');

    $services
        ->instanceof(EventSubscriberInterface::class)
        ->tag('psx.event_subscriber');

    $services->set(ArrayAdapter::class);
    $services->alias(CacheItemPoolInterface::class, ArrayAdapter::class)
        ->public();

    // event listener
    $services->set(PHPUnitExceptionListener::class);

    $services->load('PSX\\Framework\\Tests\\Controller\\Foo\\Application\\', __DIR__ . '/Controller/Foo/Application')
        ->public();

    $services->load('PSX\\Framework\\Controller\\Tool\\', __DIR__ . '/../src/Controller/Tool')
        ->public();
};
