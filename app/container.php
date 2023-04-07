<?php

use PSX\Framework\App\Service;
use PSX\Framework\App\Table;
use PSX\Framework\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->instanceof(ControllerInterface::class)
        ->tag('psx.controller');

    $services->set(Service\Population::class);
    $services->set(Table\Population::class);

    $services->load('PSX\\Framework\\App\\Controller\\', __DIR__ . '/Controller')
        ->public();
};
