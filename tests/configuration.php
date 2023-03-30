<?php

/*
This is the configuration file of PSX. Every parameter can be used inside your
application or in the DI container. Which configuration file gets loaded depends
on the DI container parameter "config.file". See the container.php if you want
load a different configuration depending on the environment.
*/

return [

    // The url to the psx public folder (i.e. http://127.0.0.1/psx/public,
    // http://localhost.com or //localhost.com)
    'psx_url'                 => 'http://127.0.0.1',

    // The input path 'index.php/' or '' if you use mod_rewrite
    'psx_dispatch'            => '',

    // Whether PSX runs in debug mode or not. If not error reporting is set to 0
    // Also several caches are used if the debug mode is false
    'psx_debug'               => true,

    // Database parameters which are used for the doctrine DBAL connection
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
    'psx_connection'          => [
        'memory'              => true,
        'driver'              => 'pdo_sqlite',
    ],

    'psx_log_level'           => \Monolog\Logger::ERROR,

    // Folder locations
    'psx_path_cache'          => __DIR__ . '/cache',
    'psx_path_log'            => __DIR__ . '/log',
    'psx_path_src'            => __DIR__,

];
