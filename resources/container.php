<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider;
use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use PSX\Api\ApiManager;
use PSX\Api\ApiManagerInterface;
use PSX\Api\Console\GenerateCommand;
use PSX\Api\Console\ParseCommand;
use PSX\Api\Console\PushCommand;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
use PSX\Api\Scanner\FilterFactory;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Data\Processor;
use PSX\Engine\DispatchInterface;
use PSX\Framework\Api\ControllerAttribute;
use PSX\Framework\Connection\ConnectionFactory;
use PSX\Framework\Console\ApplicationFactory;
use PSX\Framework\Console\DebugAutowiringCommand;
use PSX\Framework\Console\DebugContainerCommand;
use PSX\Framework\Console\DebugEventDispatcherCommand;
use PSX\Framework\Console\RouteCommand;
use PSX\Framework\Dependency\Directory;
use PSX\Framework\Console\ServeCommand;
use PSX\Framework\Controller\ControllerInterface;
use PSX\Framework\Data\ProcessorFactory;
use PSX\Framework\Dependency\DirectoryInterface;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Event\EventDispatcherFactory;
use PSX\Framework\Exception\Converter;
use PSX\Framework\Exception\ConverterInterface;
use PSX\Framework\Filter\ControllerExecutorFactory;
use PSX\Framework\Filter\PostFilterCollection;
use PSX\Framework\Filter\PreFilterCollection;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Listener\LoggingListener;
use PSX\Framework\Loader\ContextFactory;
use PSX\Framework\Loader\ContextFactoryInterface;
use PSX\Framework\Loader\Loader;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Framework\Loader\LocationFinder\RoutingParser;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Loader\RoutingParser\AttributeParser;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Framework\Logger\LoggerFactory;
use PSX\Framework\OAuth2\AuthorizerInterface;
use PSX\Framework\OAuth2\CallbackInterface;
use PSX\Framework\OAuth2\GrantTypeFactory;
use PSX\Framework\OAuth2\GrantTypeInterface;
use PSX\Framework\OAuth2\VoidAuthorizer;
use PSX\Framework\OAuth2\VoidCallback;
use PSX\Framework\Test\Environment;
use PSX\Http\Client\Client as HttpClient;
use PSX\Http\Client\ClientInterface as HttpClientInterface;
use PSX\Http\Filter;
use PSX\Schema\Console\ParseCommand as SchemaParseCommand;
use PSX\Schema\Parser\TypeSchema\ImportResolver;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Sql\TableManager;
use PSX\Sql\TableManagerInterface;
use PSX\Validate\Validate;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->instanceof(ControllerInterface::class)
        ->tag('psx.controller');

    $services
        ->instanceof(Command::class)
        ->tag('psx.command');

    $services
        ->instanceof(EventSubscriberInterface::class)
        ->tag('psx.event_subscriber');

    $services
        ->instanceof(GrantTypeInterface::class)
        ->tag('psx.oauth2_grant');

    $services->alias(ContainerInterface::class, 'service_container')
        ->public();

    $services->set(Directory::class)
        ->arg('$appDir', param('psx_path_app'))
        ->arg('$cacheDir', param('psx_path_cache'))
        ->arg('$srcDir', param('psx_path_src'))
        ->arg('$logDir', param('psx_path_log'));
    $services->alias(DirectoryInterface::class, Directory::class)
        ->public();

    $services->set(FilesystemAdapter::class)
        ->args(['psx', 0, param('psx_path_cache')]);
    $services->alias(CacheItemPoolInterface::class, FilesystemAdapter::class)
        ->public();

    $services->set(LoggerFactory::class)
        ->arg('$logDir', param('psx_path_log'))
        ->arg('$logLevel', param('psx_log_level'));
    $services->set(Logger::class)
        ->factory([service(LoggerFactory::class), 'factory']);
    $services->alias(LoggerInterface::class, Logger::class)
        ->public();

    $services->set(ConnectionFactory::class)
        ->arg('$params', param('psx_connection'));
    $services->set(Connection::class)
        ->factory([service(ConnectionFactory::class), 'factory'])
        ->public();

    $services->set(HttpClient::class);
    $services->alias(HttpClientInterface::class, HttpClient::class)
        ->public();

    $services->set(ProcessorFactory::class);
    $services->set(Processor::class)
        ->factory([service(ProcessorFactory::class), 'factory'])
        ->public();

    $services->set(ImportResolver::class)
        ->factory([ImportResolver::class, 'createDefault']);

    $services->set(SchemaManager::class)
        ->arg('$debug', param('psx_debug'));
    $services->alias(SchemaManagerInterface::class, SchemaManager::class)
        ->public();

    $services->set(TableManager::class);
    $services->alias(TableManagerInterface::class, TableManager::class)
        ->public();

    $services->set(Validate::class);

    $services->set(EventDispatcherFactory::class)
        ->args([tagged_iterator('psx.event_subscriber')]);
    $services->set(EventDispatcher::class)
        ->factory([service(EventDispatcherFactory::class), 'factory']);
    $services->alias(EventDispatcherInterface::class, EventDispatcher::class)
        ->public();

    $services->set(Converter::class)
        ->arg('$debug', param('psx_debug'));
    $services->alias(ConverterInterface::class, Converter::class)
        ->public();

    $services->set(RoutingParser::class);
    $services->alias(LocationFinderInterface::class, RoutingParser::class)
        ->public();

    $services->set(AttributeParser::class)
        ->args([tagged_iterator('psx.controller')]);
    $services->alias(RoutingParserInterface::class, AttributeParser::class)
        ->public();

    $services->set(Loader::class);
    $services->alias(LoaderInterface::class, Loader::class)
        ->public();

    $services->set(ContextFactory::class);
    $services->alias(ContextFactoryInterface::class, ContextFactory::class)
        ->public();

    $services->set(Dispatch::class);
    $services->alias(DispatchInterface::class, Dispatch::class)
        ->public();

    $services->set(ReverseRouter::class)
        ->arg('$url', param('psx_url'))
        ->arg('$dispatch', param('psx_dispatch'));

    $services->set(ControllerAttribute::class);
    $services->alias(ScannerInterface::class, ControllerAttribute::class)
        ->public();

    $services->set(FilterFactory::class);
    $services->alias(FilterFactoryInterface::class, FilterFactory::class)
        ->public();

    $services->set(GeneratorFactory::class)
        ->arg('$url', param('psx_url'))
        ->arg('$dispatch', param('psx_dispatch'));
    $services->alias(GeneratorFactoryInterface::class, GeneratorFactory::class)
        ->public();

    $services->set(ApiManager::class)
        ->arg('$debug', param('psx_debug'));
    $services->alias(ApiManagerInterface::class, ApiManager::class)
        ->public();

    $services->set(RequestReader::class);
    $services->set(ResponseWriter::class)
        ->arg('$supportedWriter', param('psx_supported_writer'));

    $services->set(ApplicationFactory::class)
        ->args([tagged_iterator('psx.command')]);
    $services->set(Application::class)
        ->factory([service(ApplicationFactory::class), 'factory'])
        ->public();
    $services->set(SingleConnectionProvider::class);

    $services->set(ControllerExecutorFactory::class);

    $services->set(GrantTypeFactory::class)
        ->args([tagged_iterator('psx.oauth2_grant')]);

    $services->set(VoidAuthorizer::class);
    $services->alias(AuthorizerInterface::class, VoidAuthorizer::class)
        ->public();

    $services->set(VoidCallback::class);
    $services->alias(CallbackInterface::class, VoidCallback::class)
        ->public();

    // test environment
    $services->set(Environment::class)
        ->arg('$debug', param('psx_debug'))
        ->public();

    // global filter chain
    $services->set(PreFilterCollection::class)
        ->args([tagged_iterator('psx.pre_filter')]);
    $services->set(PostFilterCollection::class)
        ->args([tagged_iterator('psx.post_filter')]);

    // middlewares
    $services->set(Filter\CORS::class)
        ->args([
            param('psx_cors_origin'),
            ['OPTIONS', 'HEAD', 'GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            param('psx_cors_headers'),
            false
        ])
        ->public();

    // commands
    $services->set(RouteCommand::class);
    $services->set(ServeCommand::class);
    $services->set(GenerateCommand::class);
    $services->set(ParseCommand::class);
    $services->set(PushCommand::class);
    $services->set(DebugContainerCommand::class);
    $services->set(DebugAutowiringCommand::class);
    $services->set(DebugEventDispatcherCommand::class);
    $services->set(HelpCommand::class);
    $services->set(ListCommand::class);
    $services->set(ReservedWordsCommand::class);
    $services->set(RunSqlCommand::class);
    $services->set(SchemaParseCommand::class);

    // event listener
    $services->set(LoggingListener::class);
};
