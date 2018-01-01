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

    // The default timezone
    'psx_timezone'            => 'UTC',

    // Whether PSX runs in debug mode or not. If not error reporting is set to 0
    // Also several caches are used if the debug mode is false
    'psx_debug'               => true,

    // Database parameters which are used for the doctrine DBAL connection
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
    'psx_connection'          => getConnectionParams(getenv('DB')),

    // Path to the routing file
    'psx_routing'             => __DIR__ . '/routes',

    // Folder locations
    'psx_path_cache'          => __DIR__ . '/cache',
    'psx_path_library'        => __DIR__,

    // Class name of the error controller
    //'psx_error_controller'    => null,

    // If you only want to change the appearance of the error page you can
    // specify a custom template
    //'psx_error_template'      => null,

    // Global middleware which are applied before and after every request. Must
    // bei either a classname, closure or PSX\Dispatch\FilterInterface instance
    //'psx_filter_pre'          => [],
    //'psx_filter_post'         => [],

];

function getConnectionParams($db)
{
    switch ($db) {
        case 'mysql':
            return [
                'dbname'   => 'psx',
                'user'     => 'root',
                'password' => '',
                'host'     => 'localhost',
                'driver'   => 'pdo_mysql',
            ];
            break;

        case 'pgsql':
            return [
                'dbname'   => 'psx',
                'user'     => 'postgres',
                'password' => '',
                'host'     => 'localhost',
                'driver'   => 'pdo_pgsql',
            ];
            break;

        default:
        case 'sqlite':
            return [
                'memory' => true,
                'driver' => 'pdo_sqlite',
            ];
            break;
    }
}
