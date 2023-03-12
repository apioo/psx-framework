<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider;
use Psr\Cache\CacheItemPoolInterface;
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
use PSX\Framework\Api\ScannerFactory;
use PSX\Framework\Config\Config;
use PSX\Framework\Connection\ConnectionFactory;
use PSX\Framework\Console\ApplicationFactory;
use PSX\Framework\Console\RouteCommand;
use PSX\Framework\Console\ServeCommand;
use PSX\Framework\Controller\ControllerInterface;
use PSX\Framework\Data\ProcessorFactory;
use PSX\Framework\Dispatch\ControllerFactory;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Event\EventDispatcherFactory;
use PSX\Framework\Exception\Converter;
use PSX\Framework\Exception\ConverterInterface;
use PSX\Framework\Filter\ControllerExecutorFactory;
use PSX\Framework\Filter\PostFilterChain;
use PSX\Framework\Filter\PreFilterChain;
use PSX\Framework\Http\RequestReader;
use PSX\Framework\Http\ResponseWriter;
use PSX\Framework\Loader\ContextFactory;
use PSX\Framework\Loader\ContextFactoryInterface;
use PSX\Framework\Loader\Loader;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Framework\Loader\LocationFinder\RoutingParser;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Loader\RoutingParser\AttributeParser;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Framework\Log\LoggerFactory;
use PSX\Framework\Log\LogListener;
use PSX\Http\Client\Client as HttpClient;
use PSX\Http\Client\ClientInterface as HttpClientInterface;
use PSX\Http\Filter;
use PSX\Http\FilterInterface;
use PSX\Schema\Parser\TypeSchema\ImportResolver;
use PSX\Schema\SchemaManager;
use PSX\Sql\TableManager;
use PSX\Sql\TableManagerInterface;
use PSX\Validate\Validate;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
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

    $services->set(FilesystemAdapter::class)
        ->args(['psx', 0, param('psx_path_cache')]);
    $services->alias(CacheItemPoolInterface::class, FilesystemAdapter::class);

    $services->set(Config::class)
        ->factory([Config::class, 'fromParameterBag']);

    $services->set(LoggerInterface::class)
        ->factory([LoggerFactory::class, 'factory'])
        ->arg('$logDir', param('psx_path_log'))
        ->arg('$logLevel', param('psx_log_level'));

    $services->set(Connection::class)
        ->factory([ConnectionFactory::class, 'factory'])
        ->arg('$params', param('psx_connection'));

    $services->set(HttpClient::class);
    $services->alias(HttpClientInterface::class, HttpClient::class);

    $services->set(Processor::class)
        ->factory([ProcessorFactory::class, 'factory']);

    $services->set(ImportResolver::class)
        ->factory([ImportResolver::class, 'createDefault']);

    $services->set(SchemaManager::class)
        ->arg('$debug', param('psx_debug'));

    $services->set(TableManager::class);
    $services->alias(TableManagerInterface::class, TableManager::class);

    $services->set(Validate::class);

    $services->set(EventDispatcherInterface::class)
        ->factory([EventDispatcherFactory::class, 'factory'])
        ->args([tagged_iterator('psx.event_subscriber')]);

    $services->set(Converter::class)
        ->arg('$debug', param('psx_debug'));
    $services->alias(ConverterInterface::class, Converter::class);

    $services->set(RoutingParser::class);
    $services->alias(LocationFinderInterface::class, RoutingParser::class);

    $services->set(AttributeParser::class)
        ->args([tagged_iterator('psx.controller')]);
    $services->alias(RoutingParserInterface::class, AttributeParser::class);

    $services->set(Loader::class);
    $services->alias(LoaderInterface::class, Loader::class);

    $services->set(ContextFactory::class);
    $services->alias(ContextFactoryInterface::class, ContextFactory::class);

    $services->set(Dispatch::class);
    $services->alias(DispatchInterface::class, Dispatch::class)
        ->public();

    $services->set(ReverseRouter::class)
        ->arg('$url', param('psx_url'))
        ->arg('$dispatch', param('psx_dispatch'));

    $services->set(ControllerAttribute::class);
    $services->alias(ScannerInterface::class, ControllerAttribute::class);

    $services->set(ScannerInterface::class)
        ->factory([ScannerFactory::class, 'factory'])
        ->arg('$debug', param('psx_debug'));

    $services->set(FilterFactory::class);
    $services->alias(FilterFactoryInterface::class, FilterFactory::class);

    $services->set(GeneratorFactory::class)
        ->arg('$url', param('psx_url'))
        ->arg('$dispatch', param('psx_dispatch'));
    $services->alias(GeneratorFactoryInterface::class, GeneratorFactory::class);

    $services->set(ApiManager::class)
        ->arg('$debug', param('psx_debug'));
    $services->alias(ApiManagerInterface::class, ApiManager::class);

    $services->set(RequestReader::class);
    $services->set(ResponseWriter::class)
        ->arg('$supportedWriter', param('psx_supported_writer'));

    $services->set(ApplicationFactory::class)
        ->args([tagged_iterator('psx.command')]);
    $services->set(SingleConnectionProvider::class);

    // global filter chain
    $services->set(PreFilterChain::class)
        ->args([tagged_iterator('psx.pre_filter')]);
    $services->set(PostFilterChain::class)
        ->args([tagged_iterator('psx.post_filter')]);

    // middlewares
    $services->set(Filter\CORS::class)
        ->args([
            param('psx_cors_origin'),
            ['OPTIONS', 'HEAD', 'GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            param('psx_cors_headers'),
            false
        ])
        ->tag('psx.pre_filter');

    // commands
    $services->set(RouteCommand::class);
    $services->set(ServeCommand::class);
    $services->set(GenerateCommand::class);
    $services->set(ParseCommand::class);
    $services->set(PushCommand::class);
    $services->set(HelpCommand::class);
    $services->set(ListCommand::class);
    $services->set(ReservedWordsCommand::class);
    $services->set(RunSqlCommand::class);

    // event listener
    $services->set(LogListener::class);

    // controllers
};
