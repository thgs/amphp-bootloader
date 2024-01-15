<?php declare(strict_types=1);

namespace thgs\Bootloader;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Websocket\Server\WebsocketAcceptor;
use Amp\Websocket\Server\WebsocketClientHandler;
use Psr\Log\LoggerInterface;

interface RequestHandlerFactory
{
    /**
     * @param class-string $class
     */
    public function createRequestHandler(string $class): RequestHandler;

    /**
     * @param class-string $class
     */
    public function createDelegateRequestHandler(string $class, string $delegateMethod): RequestHandler;

    /**
     * @param class-string<WebsocketAcceptor> $acceptorClass
     * @param class-string<WebsocketClientHandler> $clientHandlerClass
     */
    public function createWebsocketRequestHandler(
        HttpServer $httpServer,
        LoggerInterface $logger,
        string $acceptorClass,
        string $clientHandlerClass
    ): RequestHandler;

    public function createFallbackRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        string $fallback
    ): RequestHandler;

    /**
     * @param class-string ...$middlewares
     */
    public function createMiddlewareStack(RequestHandler $mainHandler, string ...$middlewares): RequestHandler;
}
