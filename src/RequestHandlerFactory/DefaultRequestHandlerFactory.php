<?php declare(strict_types=1);

namespace thgs\Bootstrap\RequestHandlerFactory;

use Amp\File\Filesystem;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Http\Server\StaticContent\StaticResource;
use Amp\Websocket\Server\Websocket;
use Psr\Log\LoggerInterface;
use thgs\Bootstrap\Config\PathResolver;
use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Path;
use thgs\Bootstrap\Config\Route\Route;
use thgs\Bootstrap\Config\Route\Websocket as WebsocketRoute;
use thgs\Bootstrap\DependencyInjection\Injector;
use thgs\Bootstrap\RequestHandlerFactory;
use function Amp\Http\Server\Middleware\stackMiddleware;

class DefaultRequestHandlerFactory implements RequestHandlerFactory
{
    public function __construct(
        private Injector $injector,
        private PathResolver $pathResolver
    ) {
    }

    public function createRequestHandler(
        string $class,
        Route|WebsocketRoute|Fallback|null $forRoute = null
    ): RequestHandler {
        $created = $this->injector->create($class, $forRoute);
        if (!$created instanceof RequestHandler) {
            throw new \Exception("Class $class is not a RequestHandler");
        }
        return $created;
    }

    public function createWebsocketRequestHandler(
        HttpServer $httpServer,
        LoggerInterface $logger,
        string $acceptorClass,
        string $clientHandlerClass,
        ?WebsocketRoute $forRoute = null
    ): RequestHandler {
        $acceptorInstance = $this->injector->create($acceptorClass, $forRoute);
        $clientHandlerInstance = $this->injector->create($clientHandlerClass, $forRoute);

        // todo: two more arguments optionally.
        return new Websocket($httpServer, $logger, $acceptorInstance, $clientHandlerInstance);
    }

    public function createPathRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        Path $forRoute
    ): RequestHandler {
        $filesystem = null;
        $filesystemDriver = $forRoute->filesystemDriver;
        if ($filesystemDriver !== null) {
            $filesystem = new Filesystem($this->injector->create($filesystemDriver));
        }

        $resolved = $this->pathResolver->resolve($forRoute);

        return $resolved instanceof PathResolver\ResolvedDir
            ? new DocumentRoot($httpServer, $errorHandler, $resolved->path, $filesystem)
            : new StaticResource($httpServer, $errorHandler, $resolved->path);
    }

    public function createFallbackRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        Fallback $fallback
    ): RequestHandler {
        if ($fallback->handler !== null) {
            return $this->createRequestHandler($fallback->handler, $fallback);
        }

        if ($fallback->path === null) {
            throw new \Exception('Missing fallback path');
        }

        // todo: support filesystem
        $filesystem = null;
        $filesystemDriver = $fallback->filesystemDriver;
        if ($filesystemDriver !== null) {
            $filesystem = new Filesystem($this->injector->create($filesystemDriver));
        }

        $resolved = $this->pathResolver->resolve($fallback);
        return $resolved instanceof PathResolver\ResolvedDir
            ? new DocumentRoot($httpServer, $errorHandler, $resolved->path, $filesystem)
            : new StaticResource($httpServer, $errorHandler, $resolved->path);
    }

    /**
     * @param class-string ...$middlewares
     */
    public function createMiddlewareStack(
        RequestHandler $mainHandler,
        Route|WebsocketRoute|Path|Fallback|null $forRoute,
        string ...$middlewares
    ): RequestHandler {
        // todo: support array on $middleware elements so that they can add configuration
        /** @var Middleware[] $instances */
        $instances = [];
        foreach ($middlewares as $middleware) {
            $created = $this->injector->create($middleware, $forRoute);

            if (!$created instanceof Middleware) {
                throw new \Exception("$middleware does not implement Middleware interface");
            }
            $instances[] = $created;
        }

        return stackMiddleware($mainHandler, ...$instances);
    }
}
