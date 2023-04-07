<?php

use Psr\Cache\CacheItemPoolInterface;
use PSX\Framework\Controller\ControllerInterface;
use PSX\Framework\Listener\PHPUnitExceptionListener;
use PSX\Framework\OAuth2\AuthorizerInterface;
use PSX\Framework\OAuth2\CallbackInterface;
use PSX\Framework\OAuth2\GrantTypeInterface;
use PSX\Framework\Tests\OAuth2\TestAuthorizer;
use PSX\Framework\Tests\OAuth2\TestCallback;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    $services
        ->instanceof(GrantTypeInterface::class)
        ->tag('psx.oauth2_grant');

    $services->set(ArrayAdapter::class);
    $services->alias(CacheItemPoolInterface::class, ArrayAdapter::class)
        ->public();

    $services->set(TestAuthorizer::class);
    $services->alias(AuthorizerInterface::class, TestAuthorizer::class);

    $services->set(TestCallback::class);
    $services->alias(CallbackInterface::class, TestCallback::class);

    // event listener
    $services->set(PHPUnitExceptionListener::class);

    // oauth2
    $services->load('PSX\\Framework\\Tests\\OAuth2\\GrantType\\', __DIR__ . '/OAuth2/GrantType')
        ->public();

    $services->load('PSX\\Framework\\Tests\\Controller\\Foo\\Application\\', __DIR__ . '/Controller/Foo/Application')
        ->public();

    $services->load('PSX\\Framework\\Controller\\OAuth2\\', __DIR__ . '/../src/Controller/OAuth2')
        ->public();

    $services->load('PSX\\Framework\\Controller\\Tool\\', __DIR__ . '/../src/Controller/Tool')
        ->public();
};
