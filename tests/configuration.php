<?php

use Monolog\Logger;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return [

    // The url to the psx public folder (i.e. http://api.acme.com or http://127.0.0.1/psx/public)
    'psx_url'                 => env('APP_URL')->string(),

    // The input path 'index.php/' or '' if every request is served to the index.php file
    'psx_dispatch'            => '',

    // Defines the current environment i.e. prod or dev
    'psx_env'                 => env('APP_ENV')->string(),

    // Whether the app runs in debug mode or not. If not error reporting is set to 0, also several caches are used if
    // the debug mode is false
    'psx_debug'               => env('APP_DEBUG')->bool(),

    // Database parameters which are used for the doctrine DBAL connection
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
    'psx_connection'          => env('APP_CONNECTION')->string(),

    // Mailer connection which is used to send mails
    // https://symfony.com/doc/current/mailer.html#using-built-in-transports
    'psx_mailer'              => env('APP_MAILER')->string(),

    // Messenger transport configuration
    // https://symfony.com/doc/current/messenger.html#transports-async-queued-messages
    'psx_messenger'           => env('APP_MESSENGER')->string(),

    'psx_migration_namespace' => 'PSX\\Framework\\Tests\\Migrations',

    'psx_log_level'           => Logger::ERROR,

    // Folder locations
    'psx_path_cache'          => __DIR__ . '/cache',
    'psx_path_log'            => __DIR__ . '/log',
    'psx_path_src'            => __DIR__,

];
