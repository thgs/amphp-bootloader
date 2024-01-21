<?php declare(strict_types=1);

namespace thgs\Bootstrap;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Session\SessionMiddleware;
use Psr\Log\LoggerInterface;
use thgs\Bootstrap\Config\Configuration;
use thgs\Bootstrap\DependencyInjection\Injector;
use function Amp\trapSignal;

final class Bootstrap
{
    /** @var string */
    public const LOG_CONTEXT = 'boot';

    private Bootloader $bootloader;
    private HttpServer $httpServer;
    private ?LoggerInterface $logger;

    public function __construct(
        public readonly Configuration $configuration,
        // can we move these two out and they come from config?
        private Injector $injector,
        private ?RequestHandler $requestHandler = null,     // pass your pre-configured Router instance here.
        private readonly ErrorHandler $errorHandler = new DefaultErrorHandler()
    ) {
        $this->boot($this->configuration);
    }

    private function boot(Configuration $configuration): void
    {
        $initTime = \hrtime(true);

        $this->bootloader = new Bootloader($this->configuration->logging);
        $this->logger = $this->bootloader->logger;

        $this->injector->register($this->logger, LoggerInterface::class);

        if ($configuration->session !== null) {
            // todo: this is stacked on start() instead of the constructor in SocketHttpServer
            $sessionMiddleware = $this->bootloader->loadSession($configuration->session);
            $this->injector->register($sessionMiddleware, SessionMiddleware::class);
        }

        $this->httpServer = $this->bootloader->loadServerConfig($configuration->server);

        if ($this->requestHandler instanceof RequestHandler) {
            $this->logger->info('Using override request handler', [self::LOG_CONTEXT]);
        } else {
            $this->requestHandler = $this->bootloader->loadHandler(
                $configuration->requestHandler,
                $this->httpServer,
                $this->logger,
                $this->errorHandler,
                $this->injector,
                $cacheSize = null           // todo: add this
            );
        }

        $someBootTime = (\hrtime(true) - $initTime) / 1_000_000_000;
        $this->logger->info("Booted at some $someBootTime seconds");

        $this->httpServer->start($this->requestHandler, $this->errorHandler);

        // todo: this needs better handling
        $signal = trapSignal([\SIGINT, \SIGTERM]);
        $this->stop(\sprintf("Received signal %s, stopping HTTP server", $signal === 2 ? 'SIGINT' : 'SIGTERM'));
    }

    public function stop(string $reason): void
    {
        $this->logger?->info($reason);
        $this->httpServer->stop();
    }
}
