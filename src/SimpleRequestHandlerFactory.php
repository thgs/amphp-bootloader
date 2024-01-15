<?php declare(strict_types=1);

namespace thgs\Bootloader;

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
use thgs\Bootloader\DependencyInjection\InjectorInterface;
use thgs\Bootloader\Reflection\NativeReflector;
use function Amp\Http\Server\Middleware\stackMiddleware;

class SimpleRequestHandlerFactory implements RequestHandlerFactory
{
    public function __construct(
        private InjectorInterface $injector,
        private NativeReflector $reflector
    ) {
    }

    /**
     * @param class-string $class
     */
    public function createRequestHandler(string $class): RequestHandler
    {
        $created = $this->injector->create($class);
        if (!$created instanceof RequestHandler) {
            throw new \Exception("Class $class is not a RequestHandler");
        }
        return $created;
    }

    /**
     * @param class-string $class
     */
    public function createDelegateRequestHandler(string $class, string $delegateMethod): RequestHandler
    {
        $delegate = $this->injector->create($class);

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
        string $clientHandlerClass
    ): RequestHandler {
        $acceptorInstance = $this->injector->create($acceptorClass);
        $clientHandlerInstance = $this->injector->create($clientHandlerClass);

        // todo: two more arguments optionally.
        return new Websocket($httpServer, $logger, $acceptorInstance, $clientHandlerInstance);
    }

    public function createFallbackRequestHandler(
        HttpServer $httpServer,
        ErrorHandler $errorHandler,
        string $fallback
    ): RequestHandler {
        return match(\true) {
            \class_exists($fallback) => $this->createRequestHandler($fallback),
            \is_dir($fallback) => new DocumentRoot($httpServer, $errorHandler, $fallback),
            \is_file($fallback) => new StaticResource($httpServer, $errorHandler, $fallback),
            default => throw new \Exception('Cannot create fallback handler')
        };
    }

    /**
     * @param class-string ...$middlewares
     */
    public function createMiddlewareStack(RequestHandler $mainHandler, string ...$middlewares): RequestHandler
    {
        // todo: support array on $middleware elements so that they can add configuration
        /** @var Middleware[] $instances */
        $instances = [];
        foreach ($middlewares as $middleware) {
            $created = $this->injector->create($middleware);

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
        // todo: some reference here may be beneficial

        $mapped = [];
        foreach ($parameterTypes as $name => $type) {
            $mapped[$name] = $this->mapForType($type);
        }
        return $mapped;
    }

    /**
     * @param string|class-string $type
     */
    private function mapForType(string $type): callable
    {
        return match ($type) {
            "int" => function (Request $request, string $x): int { return (int) $x; },
            "string" => function (RequestHandler $request, string $x): string { return $x; },
            "float" => function (Request $request, string $x): float { return (float) $x; },
            "bool" => function (Request $request, string $x): bool { return (bool) $x; },
            "null" => function (Request $request, string $x) { return $x; },
            default => $this->resolveThroughMinorDI($type)
        };
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
