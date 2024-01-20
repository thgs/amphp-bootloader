<?php declare(strict_types=1);

namespace thgs\Bootstrap;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Websocket\Server\WebsocketAcceptor;
use Amp\Websocket\Server\WebsocketClientHandler;
use Psr\Log\LoggerInterface;
use thgs\Bootstrap\Config\Route\Delegate;
use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Path;
use thgs\Bootstrap\Config\Route\Route;
use thgs\Bootstrap\Config\Route\Websocket;

interface RequestHandlerFactory
{
    /**
     * @param class-string $class
     */
    public function createRequestHandler(
        string $class,
        ?Route $forRoute = null
    ): RequestHandler;

    /**
     * @param class-string $class
     */
    public function createDelegateRequestHandler(
        string $class,
        string $delegateMethod,
        ?Delegate $forRoute = null
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
        ?Websocket $forRoute
    ): RequestHandler;

    public function createPathRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        Path $forRoute
    ): RequestHandler;

    public function createFallbackRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        Fallback $fallback
    ): RequestHandler;

    /**
     * @param class-string ...$middlewares
     */
    public function createMiddlewareStack(
        RequestHandler $mainHandler,
        Route|Delegate|Websocket|Path|Fallback|null $forRoute,
        string ...$middlewares
    ): RequestHandler;
}
