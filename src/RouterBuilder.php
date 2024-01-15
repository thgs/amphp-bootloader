<?php declare(strict_types=1);

namespace thgs\Bootloader;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Router;
use Psr\Log\LoggerInterface;
use thgs\Bootloader\Config\Route\Delegate;
use thgs\Bootloader\Config\Route\Group;
use thgs\Bootloader\Config\Route\Route;
use thgs\Bootloader\Config\Route\RouteRegistry;
use thgs\Bootloader\Config\Route\Websocket;

class RouterBuilder
{
    private array $routes;

    private ?string $fallback;

    public function __construct(
        private RequestHandlerFactory $handlerFactory,
        private HttpServer $httpServer,
        private LoggerInterface $logger
    ) {
    }

    public function add(string $name, Route | Delegate | Group | Websocket $route): void
    {
        $this->routes[$name] = $route;
    }

    public function addRegistry(RouteRegistry $registry): void
    {
        foreach ($registry as $name => $route) {
            $this->add($name, $route);
        }
    }

    public function addFallback(?string $fallback): void
    {
        $this->fallback = $fallback;
    }

    public function build(
        ErrorHandler $errorHandler,
        ?int $cacheSize = null
    ) {
        $router = $cacheSize
            ? new Router($this->httpServer, $this->logger, $errorHandler, \max(1, $cacheSize))
            : new Router($this->httpServer, $this->logger, $errorHandler);

        foreach ($this->routes as $route) {
            $this->addRoute($router, $route);
        }

        if ($this->fallback) {
            $fallback = $this->handlerFactory->createFallbackRequestHandler(
                $this->httpServer,
                $errorHandler,
                $this->fallback
            );
            $router->setFallback($fallback);
        }

        return $router;
    }

    private function addRoute(Router $router, Route | Delegate | Group | Websocket $route): void
    {
        if ($route instanceof Group) {
            foreach ($route as $groupedRoute) {
                $this->addRoute($router, $groupedRoute);
            }
            return;
        }

        $method = $route->method;
        $uri = $route->uri;

        if ($route instanceof Route) {
            $handler = $this->handlerFactory->createRequestHandler($route->handler);
        }

        if ($route instanceof Delegate) {
            $handler = $this->handlerFactory->createDelegateRequestHandler($route->delegate, $route->action);
        }

        if ($route instanceof Websocket) {
            $handler = $this->handlerFactory->createWebsocketRequestHandler(
                $this->httpServer,
                $this->logger,
                $route->acceptor,
                $route->clientHandler
            );
        }

        if (!isset($handler)) {
            throw new \Exception("Unable to create handler for route $method . $uri");
        }

        if (!empty($route->middleware)) {
            $handler = $this->handlerFactory->createMiddlewareStack($handler, ...$route->middleware);
        }

        $router->addRoute($method, $uri, $handler);
    }
}
