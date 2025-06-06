<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command as MigrationCommand;
use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use PSX\Api\ApiManager;
use PSX\Api\ApiManagerInterface;
use PSX\Api\Console\PushCommand;
use PSX\Api\GeneratorFactory;
use PSX\Api\Parser\Attribute\Builder;
use PSX\Api\Parser\Attribute\BuilderInterface;
use PSX\Api\Repository;
use PSX\Api\Scanner\FilterFactory;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Data\Processor;
use PSX\Engine\DispatchInterface;
use PSX\Framework\Api\Repository\SDKgen\Config as SDKgenConfig;
use PSX\Framework\Api\Scanner\RoutingParser as ScannerRoutingParser;
use PSX\Framework\Config\BaseUrl;
use PSX\Framework\Config\BaseUrlInterface;
use PSX\Framework\Config\ConfigInterface;
use PSX\Framework\Config\ContainerConfig;
use PSX\Framework\Config\Directory;
use PSX\Framework\Config\DirectoryInterface;
use PSX\Framework\Connection\ConnectionFactory;
use PSX\Framework\Console\ApplicationFactory;
use PSX\Framework\Data\ProcessorFactory;
use PSX\Framework\Dependency\Configurator;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Environment\IPResolver;
use PSX\Framework\Event\EventDispatcherFactory;
use PSX\Framework\Exception\Converter;
use PSX\Framework\Exception\ConverterInterface;
use PSX\Framework\Filter\ControllerExecutorFactory;
use PSX\Framework\Filter\ControllerExecutorFactoryInterface;
use PSX\Framework\Filter\PostFilterCollection;
use PSX\Framework\Filter\PreFilterCollection;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Listener\LoggingListener;
use PSX\Framework\Loader\ContextFactory;
use PSX\Framework\Loader\ContextFactoryInterface;
use PSX\Framework\Loader\ControllerResolver;
use PSX\Framework\Loader\ControllerResolverInterface;
use PSX\Framework\Loader\Loader;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Framework\Loader\LocationFinder\RoutingParser;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Loader\RoutingParser\AttributeParser;
use PSX\Framework\Loader\RoutingParser\CachedParser;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Framework\Logger\LoggerFactory;
use PSX\Framework\Mailer\MailerFactory;
use PSX\Framework\Messenger\DefaultTransport;
use PSX\Framework\Messenger\HandlersLocator;
use PSX\Framework\Messenger\SendersLocator;
use PSX\Framework\Messenger\Transport\DoctrineTransportFactory;
use PSX\Framework\Migration\DependencyFactoryFactory;
use PSX\Framework\OAuth2\AuthorizerInterface;
use PSX\Framework\OAuth2\CallbackInterface;
use PSX\Framework\OAuth2\GrantTypeFactory;
use PSX\Framework\OAuth2\VoidAuthorizer;
use PSX\Framework\OAuth2\VoidCallback;
use PSX\Framework\Test\Environment;
use PSX\Http\Client\Client as HttpClient;
use PSX\Http\Client\ClientInterface as HttpClientInterface;
use PSX\Http\Filter;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Sql\TableManager;
use PSX\Sql\TableManagerInterface;
use PSX\Validate\Validate;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Command\StopWorkersCommand;
use Symfony\Component\Messenger\EventListener as MessengerEventListener;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Retry\MultiplierRetryStrategy;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use Symfony\Component\Messenger\Transport as MessengerTransport;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container) {
    $services = Configurator::services($container->services());

    $services->alias(ContainerInterface::class, 'service_container')
        ->public();

    $services->set(Directory::class)
        ->arg('$appDir', param('psx_path_app'))
        ->arg('$cacheDir', param('psx_path_cache'))
        ->arg('$srcDir', param('psx_path_src'))
        ->arg('$logDir', param('psx_path_log'));
    $services->alias(DirectoryInterface::class, Directory::class)
        ->public();

    $services->set(BaseUrl::class)
        ->arg('$url', param('psx_url'))
        ->arg('$dispatch', param('psx_dispatch'));
    $services->alias(BaseUrlInterface::class, BaseUrl::class)
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

    $services->set(MailerFactory::class)
        ->arg('$dsn', param('psx_mailer'));
    $services->set(MailerInterface::class)
        ->factory([service(MailerFactory::class), 'factory'])
        ->public();

    $services->set(HttpClient::class);
    $services->alias(HttpClientInterface::class, HttpClient::class)
        ->public();

    $services->set(ProcessorFactory::class);
    $services->set(Processor::class)
        ->factory([service(ProcessorFactory::class), 'factory'])
        ->public();

    $services->set(SchemaManager::class)
        ->arg('$debug', param('psx_debug'));
    $services->alias(SchemaManagerInterface::class, SchemaManager::class)
        ->public();

    $services->set(TableManager::class);
    $services->alias(TableManagerInterface::class, TableManager::class)
        ->public();

    $services->set(Validate::class);

    $services->set(EventDispatcherFactory::class)
        ->args([
            tagged_iterator('psx.event_subscriber'),
        ]);
    $services->set(EventDispatcher::class)
        ->lazy()
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
    $services->set(CachedParser::class)
        ->args([
            service(AttributeParser::class),
            service(CacheItemPoolInterface::class),
            param('psx_debug'),
        ]);
    $services->alias(RoutingParserInterface::class, CachedParser::class)
        ->public();

    $services->set(Builder::class)
        ->arg('$debug', param('psx_debug'));
    $services->alias(BuilderInterface::class, Builder::class)
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

    $services->set(ReverseRouter::class);

    $services->set(ScannerRoutingParser::class);
    $services->alias(ScannerInterface::class, ScannerRoutingParser::class)
        ->public();

    $services->set(FilterFactory::class);
    $services->alias(FilterFactoryInterface::class, FilterFactory::class)
        ->public();

    $services->set(Repository\LocalRepository::class);
    $services->set(Repository\SchemaRepository::class);
    $services->set(Repository\SDKgenRepository::class);
    $services->set(SDKgenConfig::class);
    $services->alias(Repository\SDKgen\ConfigInterface::class, SDKgenConfig::class);
    $services->set(GeneratorFactory::class)
        ->args([
            tagged_iterator('psx.api_repository'),
            tagged_iterator('psx.api_configurator'),
            expr('service(\'' . addslashes(BaseUrlInterface::class) . '\').getDispatchUrl()'),
        ]);

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
    $services->alias(ConnectionProvider::class, SingleConnectionProvider::class);

    $services->set(DependencyFactoryFactory::class)
        ->arg('$srcDir', param('psx_path_src'))
        ->arg('$namespace', param('psx_migration_namespace'));
    $services->set(DependencyFactory::class)
        ->factory([service(DependencyFactoryFactory::class), 'factory'])
        ->public();

    $services->set(ControllerResolver::class);
    $services->alias(ControllerResolverInterface::class, ControllerResolver::class);

    $services->set(ControllerExecutorFactory::class);
    $services->alias(ControllerExecutorFactoryInterface::class, ControllerExecutorFactory::class);

    $services->set(GrantTypeFactory::class)
        ->args([tagged_iterator('psx.oauth2_grant')]);

    $services->set(VoidAuthorizer::class);
    $services->alias(AuthorizerInterface::class, VoidAuthorizer::class)
        ->public();

    $services->set(VoidCallback::class);
    $services->alias(CallbackInterface::class, VoidCallback::class)
        ->public();

    $services->set(ContainerConfig::class);
    $services->alias(ConfigInterface::class, ContainerConfig::class)
        ->public();

    // messenger
    $services->set(MessageBus::class)
        ->args([tagged_iterator('psx.messenger_middleware')]);
    $services->alias(MessageBusInterface::class, MessageBus::class)
        ->public();

    $services->set(SendMessageMiddleware::class);
    $services->set(HandleMessageMiddleware::class);

    $services->set(HandlersLocator::class)
        ->args([abstract_arg('handlers')]);
    $services->alias(HandlersLocatorInterface::class, HandlersLocator::class);

    $services->set(SendersLocator::class);
    $services->alias(MessengerTransport\Sender\SendersLocatorInterface::class, SendersLocator::class);

    $services->set(MessengerTransport\TransportFactory::class)
        ->args([tagged_iterator('psx.messenger_transport_factory')]);
    $services->alias(MessengerTransport\TransportFactoryInterface::class, MessengerTransport\TransportFactory::class);

    $services->set(MessengerTransport\TransportInterface::class)
        ->factory([service(MessengerTransport\TransportFactoryInterface::class), 'createTransport'])
        ->args([
            param('psx_messenger'),
            [],
            service(MessengerTransport\Serialization\SerializerInterface::class)
        ])
        ->public();

    $services->set(MessengerTransport\InMemory\InMemoryTransportFactory::class);
    $services->set(DoctrineTransportFactory::class);

    $services->set(MessengerTransport\Sync\SyncTransport::class);

    $services->set(MessengerTransport\Serialization\PhpSerializer::class);
    $services->alias(MessengerTransport\Serialization\SerializerInterface::class, MessengerTransport\Serialization\PhpSerializer::class);

    $services->set(MultiplierRetryStrategy::class);
    $services->alias(RetryStrategyInterface::class, MultiplierRetryStrategy::class);

    $services->set(MessengerEventListener\SendFailedMessageForRetryListener::class)
        ->args([
            service_locator([DefaultTransport::NAME => service(MessengerTransport\TransportInterface::class)]),
            service_locator([DefaultTransport::NAME => service(RetryStrategyInterface::class)]),
            service(LoggerInterface::class),
            service(EventDispatcherInterface::class),
        ]);
    $services->set(MessengerEventListener\AddErrorDetailsStampListener::class);
    $services->set(MessengerEventListener\DispatchPcntlSignalListener::class);
    $services->set(MessengerEventListener\StopWorkerOnRestartSignalListener::class);
    $services->set(MessengerEventListener\StopWorkerOnCustomStopExceptionListener::class);

    // test environment
    $services->set(Environment::class)
        ->public();

    $services->set(IPResolver::class);

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
    $services->load('PSX\\Framework\\Command\\', __DIR__ . '/../src/Command');

    // controller
    $services->load('PSX\\Framework\\Controller\\OAuth2\\', __DIR__ . '/../src/Controller/OAuth2')
        ->public();

    $services->load('PSX\\Framework\\Controller\\Tool\\', __DIR__ . '/../src/Controller/Tool')
        ->public();

    // migrations
    $services->set(MigrationCommand\DiffCommand::class);
    $services->set(MigrationCommand\DumpSchemaCommand::class);
    $services->set(MigrationCommand\ExecuteCommand::class);
    $services->set(MigrationCommand\GenerateCommand::class);
    $services->set(MigrationCommand\LatestCommand::class);
    $services->set(MigrationCommand\ListCommand::class);
    $services->set(MigrationCommand\MigrateCommand::class);
    $services->set(MigrationCommand\RollupCommand::class);
    $services->set(MigrationCommand\StatusCommand::class);
    $services->set(MigrationCommand\SyncMetadataCommand::class);
    $services->set(MigrationCommand\UpToDateCommand::class);
    $services->set(MigrationCommand\VersionCommand::class);

    // messenger
    $services->set(StopWorkersCommand::class);

    $services->set(PushCommand::class);
    $services->set(HelpCommand::class);
    $services->set(ListCommand::class);
    $services->set(RunSqlCommand::class);

    // event listener
    $services->set(LoggingListener::class);
};
