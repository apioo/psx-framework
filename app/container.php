<?php

use PSX\Framework\App\Service;
use PSX\Framework\App\Table;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(Service\Population::class);
    $services->set(Table\Population::class);
};
