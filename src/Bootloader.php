<?php declare(strict_types=1);

namespace thgs\Bootstrap;

use Amp\Http\Cookie\CookieAttributes;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Middleware\ForwardedHeaderType;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Session\SessionMiddleware;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Socket\BindContext;
use Amp\Socket\Certificate;
use Amp\Socket\ServerTlsContext;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use thgs\Bootstrap\Config\LoggingConfiguration;
use thgs\Bootstrap\Config\PathResolver\DefaultPathResolver;
use thgs\Bootstrap\Config\RequestHandlerConfiguration;
use thgs\Bootstrap\Config\RouterBuilder;
use thgs\Bootstrap\Config\RoutesLoader\BlockingArrayLoader;
use thgs\Bootstrap\Config\ServerConfiguration;
use thgs\Bootstrap\Config\SessionConfiguration;
use thgs\Bootstrap\DependencyInjection\Injector;
use thgs\Bootstrap\RequestHandlerFactory\DefaultRequestHandlerFactory;
use thgs\Bootstrap\RequestHandlerFactory\Reflection\NativeReflector;
use function Amp\ByteStream\getStdout;

class Bootloader
{
    public LoggerInterface $logger;

    public function __construct(LoggingConfiguration $loggingConfiguration)
    {
        $this->logger = $this->loadLoggingConfig($loggingConfiguration);
    }

    public function loadLoggingConfig(LoggingConfiguration $config): LoggerInterface
    {
        if ($config->logInStdout) {
            $this->logger = $this->getStdoutLogger($config->name);
            $this->logger->info('Logger is ready', ['boot']);
            return $this->logger;
        }

        throw new \Exception('Not implemented yet. Use stdout logger only');
    }

    public function loadServerConfig(ServerConfiguration $config): HttpServer
    {
        $context = null;
        if ($config->certificatePath) {
            $certificate = new Certificate($config->certificatePath);
            $context = (new BindContext())
                ->withTlsContext((new ServerTlsContext())
                ->withDefaultCertificate($certificate));
        }

        // after instantiation
        if ($config->directAccess) {
            $httpServer = SocketHttpServer::createForDirectAccess(
                logger: $this->logger,
                enableCompression: $config->compression,
                connectionLimit: $config->connectionLimit,
                connectionLimitPerIp: $config->connectionLimitPerIp,
                concurrencyLimit: $config->concurrencyLimit,
                allowedMethods: $config->allowedMethods
                // todo: add config for HttpDriver?
            );
            $this->log('Server for direct access loaded');
        } else {
            $headerType = $config->forwardedHeaderType === 'forwarded'
                ? ForwardedHeaderType::Forwarded
                : ForwardedHeaderType::XForwardedFor;

            /** @var array<non-empty-string> $trustedProxies  Just to suppress psalm here */
            $trustedProxies = $config->trustedProxies;
            if (\count($trustedProxies) < 1 || empty($trustedProxies[0])) {
                throw new \Exception('Trusted proxies empty. Must include at least one.');
            }

            $httpServer = SocketHttpServer::createForBehindProxy(
                logger: $this->logger,
                headerType: $headerType,
                trustedProxies: $trustedProxies,
                enableCompression: $config->compression,
                concurrencyLimit: $config->concurrencyLimit,
                allowedMethods: $config->allowedMethods,
                // todo: add config for HttpDriver?
            );
            $this->log('Server for behind proxy loaded');
        }

        // expose servers
        foreach ($config->servers as $server) {
            $httpServer->expose($server);
            $this->log('Exposing ' . $server);
        }

        foreach ($config->tlsServers as $server) {
            $httpServer->expose($server, $context);
            $this->log('Exposing ' . $server);
        }

        return $httpServer;
    }

    public function loadSession(
        SessionConfiguration $config
    ): SessionMiddleware {
        $attributes = CookieAttributes::default();

        if ($config->expiry) {
            // todo: does expiry work? (or how does expiry work?)
            $attributes->withExpiry(new \DateTimeImmutable($config->expiry));
        }

        // todo: support storage and parameters and requestAttribute and the rest

        return new SessionMiddleware(
            cookieAttributes: $attributes,
            cookieName: $config->cookieName,
        );
    }

    public function loadHandler(
        RequestHandlerConfiguration $config,
        HttpServer $httpServer,
        LoggerInterface $logger,
        ErrorHandler $errorHandler,
        Injector $injector,
        ?int $cacheSize = null
    ): RequestHandler {
        $loader = new BlockingArrayLoader($config->routeFile);

        // todo: allow injecting a different factory and a different path resolver
        $builder = new RouterBuilder(
            new DefaultRequestHandlerFactory($injector, new NativeReflector(), new DefaultPathResolver($config->publicDir)),
            $httpServer,
            $logger
        );

        $routeRegistry = $loader->load();
        $builder->addRegistry($routeRegistry);
        $builder->addFallback($routeRegistry->getFallback());

        return $builder->build($errorHandler, $cacheSize);
    }

    private function getStdoutLogger(string $name = 'amphp-httpd'): LoggerInterface
    {
        $handler = new StreamHandler(getStdout());
        $handler->pushProcessor(new PsrLogMessageProcessor());
        $handler->setFormatter(new ConsoleFormatter());

        $logger = new Logger($name);
        $logger->pushHandler($handler);
        return $logger;
    }

    private function log(string $message): void
    {
        $this->logger->info($message, ['boot']);
    }
}
