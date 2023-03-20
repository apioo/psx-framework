<?php

return \PSX\Framework\Dependency\ContainerBuilder::build(
    __DIR__,
    true,
    __DIR__ . '/../resources/container.php',
    __DIR__ . '/test_container.php',
    __DIR__ . '/../app/container.php',
);
