<?php

require_once __DIR__ . '/../vendor/autoload.php';

$container = require_once __DIR__ . '/container.php';

/** @var \PSX\Framework\Test\Environment $environment */
global $environment;
$environment = $container->get(\PSX\Framework\Test\Environment::class);
$environment->setup();
