<?php declare(strict_types=1);

namespace thgs\Bootloader;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Websocket\Server\WebsocketAcceptor;
use Amp\Websocket\Server\WebsocketClientHandler;
use Psr\Log\LoggerInterface;
use thgs\Bootloader\Config\Route\Delegate;
use thgs\Bootloader\Config\Route\Route;
use thgs\Bootloader\Config\Route\Websocket;

interface RequestHandlerFactory
{
    /**
     * @param class-string $class
     */
    public function createRequestHandler(
        string $class,
        Route|Delegate|Websocket|null $forRoute = null
    ): RequestHandler;

    /**
     * @param class-string $class
     */
    public function createDelegateRequestHandler(
        string $class,
        string $delegateMethod,
        Route|Delegate|Websocket|null $forRoute = null
    ): RequestHandler;

    /**
     * @param class-string<WebsocketAcceptor> $acceptorClass
     * @param class-string<WebsocketClientHandler> $clientHandlerClass
     */
    public function createWebsocketRequestHandler(
        HttpServer $httpServer,
        LoggerInterface $logger,
        string $acceptorClass,
        string $clientHandlerClass,
        Route|Delegate|Websocket|null $forRoute
    ): RequestHandler;

    /**
     * Implementations SHOULD pass `Fallback` route to DI when creating an instance
     * of the fallback request handler.
     */
    public function createFallbackRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        string $fallback
    ): RequestHandler;

    /**
     * @param class-string ...$middlewares
     */
    public function createMiddlewareStack(
        RequestHandler $mainHandler,
        Route|Delegate|Websocket|null $forRoute,
        string ...$middlewares
    ): RequestHandler;
}
