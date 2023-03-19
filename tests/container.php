<?php

return \PSX\Framework\Dependency\ContainerBuilder::build(
    __DIR__,
    __DIR__ . '/../resources/container.php',
    __DIR__ . '/test_container.php',
    __DIR__ . '/../app/container.php',
);
