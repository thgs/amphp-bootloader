<?php declare(strict_types=1);

namespace thgs\Bootstrap\RequestHandlerFactory;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Router;
use Amp\Http\Server\StaticContent\DocumentRoot;
use Amp\Http\Server\StaticContent\StaticResource;
use Amp\Websocket\Server\Websocket;
use Psr\Log\LoggerInterface;
use thgs\Bootstrap\Config\Route\Delegate;
use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Route;
use thgs\Bootstrap\Config\Route\Websocket as WebsocketRoute;
use thgs\Bootstrap\DependencyInjection\Injector;
use thgs\Bootstrap\RequestHandlerFactory;
use thgs\Bootstrap\RequestHandlerFactory\Reflection\NativeReflector;
use function Amp\Http\Server\Middleware\stackMiddleware;

class DefaultRequestHandlerFactory implements RequestHandlerFactory
{
    public function __construct(
        private Injector $injector,
        private NativeReflector $reflector
    ) {
    }

    public function createRequestHandler(string $class, Route|Delegate|WebsocketRoute|Fallback|null $forRoute = null): RequestHandler
    {
        $created = $this->injector->create($class, $forRoute);
        if (!$created instanceof RequestHandler) {
            throw new \Exception("Class $class is not a RequestHandler");
        }
        return $created;
    }

    /**
     * @param class-string $class
     */
    public function createDelegateRequestHandler(
        string $class,
        string $delegateMethod,
        Route|Delegate|WebsocketRoute|null $forRoute = null
    ): RequestHandler {
        $delegate = $this->injector->create($class, $forRoute);

        if (!\method_exists($delegate, $delegateMethod)) {
            throw new \Exception("$delegateMethod does not exist in $class");
        }

        $parameterTypes = $this->reflector->reflectParameterTypes($delegate, $delegateMethod);
        $valueMapping = $this->createValueMapping($parameterTypes);

        // todo: since we typehint return type as Response here, maybe check that $delegate returns Response
        $closure = function (Request $request) use ($valueMapping, $delegate, $delegateMethod): Response {
            $requestValues = $request->getAttribute(Router::class);
            $values = [];
            foreach ($valueMapping as $name => $map) {
                if (isset($requestValues[$name])) {
                    $values[$name] = $map($request, $requestValues[$name]);
                    continue;
                }

                $values[$name] = $map($request);
            }

            return $delegate->$delegateMethod(...$values);
        };

        //        if (!isset($closure)) {
        //            throw new \Exception('Cannot create request handler');
        //        }

        return new ClosureRequestHandler($closure);
    }

    public function createWebsocketRequestHandler(
        HttpServer $httpServer,
        LoggerInterface $logger,
        string $acceptorClass,
        string $clientHandlerClass,
        Route|Delegate|WebsocketRoute|null $forRoute = null
    ): RequestHandler {
        $acceptorInstance = $this->injector->create($acceptorClass, $forRoute);
        $clientHandlerInstance = $this->injector->create($clientHandlerClass, $forRoute);

        // todo: two more arguments optionally.
        return new Websocket($httpServer, $logger, $acceptorInstance, $clientHandlerInstance);
    }

    public function createFallbackRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        string $fallback
    ): RequestHandler {
        return match(\true) {
            \class_exists($fallback) => $this->createRequestHandler($fallback, new Fallback()),
            \is_dir($fallback) => new DocumentRoot($httpServer, $errorHandler, $fallback),
            \is_file($fallback) => new StaticResource($httpServer, $errorHandler, $fallback),
            default => throw new \Exception('Cannot create fallback handler')
        };
    }

    /**
     * @param class-string ...$middlewares
     */
    public function createMiddlewareStack(
        RequestHandler $mainHandler,
        Route|Delegate|WebsocketRoute|null $forRoute,
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

    /**
     * @param array<string, string|class-string> $parameterTypes
     * @return array<string, callable>
     */
    private function createValueMapping(array $parameterTypes): array
    {
        /** @var array<string, callable> $valueMapping */
        static $valueMapping = [];

        if (empty($valueMapping)) {
            $valueMapping = [
                "int" => static function (Request $request, string $x): int { return (int) $x; },
                "string" => static function (Request $request, string $x): string { return $x; },
                "float" => static function (Request $request, string $x): float { return (float) $x; },
                "bool" => static function (Request $request, string $x): bool { return (bool) $x; },
                "null" => static function (Request $request, string $x): string { return $x; },
            ];
        }

        $mapped = [];
        foreach ($parameterTypes as $name => $type) {
            $mapped[$name] = $valueMapping[$type] ?? $this->resolveThroughMinorDI($type);
        }
        return $mapped;
    }

    /**
     * @param string|class-string $type
     */
    private function resolveThroughMinorDI(string $type): callable
    {
        // only Request object can be injected from Minor DI (right now)
        // at this point, if you have bigger/actual dependencies, put them in the constructor

        if ($type === Request::class) {
            return function (Request $request) { return $request; };
        }

        // fallback ? odd place
        return function (Request $request, string $x) { return $x; };
    }
}
