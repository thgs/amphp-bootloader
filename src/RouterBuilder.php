<?php declare(strict_types=1);

namespace thgs\Bootstrap;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Router;
use Psr\Log\LoggerInterface;
use thgs\Bootstrap\Config\Route\Delegate;
use thgs\Bootstrap\Config\Route\Group;
use thgs\Bootstrap\Config\Route\Route;
use thgs\Bootstrap\Config\Route\RouteRegistry;
use thgs\Bootstrap\Config\Route\Websocket;

/**
 * @psalm-type RouteConstructor = Route|Delegate|Group|Websocket
 */
class RouterBuilder
{
    /**
     * @var array<string, Route|Delegate|Group|Websocket>
     */
    private array $routes = [];

    private ?string $fallback = null;

    public function __construct(
        private RequestHandlerFactory $handlerFactory,
        private HttpServer $httpServer,
        private LoggerInterface $logger
    ) {
    }

    public function add(string $name, Route|Delegate|Group|Websocket $route): void
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
        // todo: when adding a fallback like DocumentRoot the default FilesystemDriver needs ext-posix
        $this->fallback = $fallback;
    }

    public function build(
        ErrorHandler $errorHandler,
        ?int $cacheSize = null
    ): Router {
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

    private function addRoute(Router $router, Route|Delegate|Websocket|Group $route): void
    {
        if ($route instanceof Group) {
            foreach ($route as $memberRoute) {
                $this->addRoute($router, $memberRoute);
            }
            return;
        }

        $method = $route->method;
        $uri = $route->uri;

        if ($route instanceof Route) {
            $handler = $this->handlerFactory->createRequestHandler($route->handler, $route);
        }

        if ($route instanceof Delegate) {
            $handler = $this->handlerFactory->createDelegateRequestHandler($route->delegate, $route->action, $route);
        }

        if ($route instanceof Websocket) {
            $handler = $this->handlerFactory->createWebsocketRequestHandler(
                $this->httpServer,
                $this->logger,
                $route->acceptor,
                $route->clientHandler,
                $route
            );
        }

        if (!isset($handler)) {
            throw new \Exception("Unable to create handler for route $method . $uri");
        }

        if (!$handler instanceof RequestHandler) {
            throw new \Exception("Handler for $uri is not a RequestHandler");
        }

        if (!empty($route->middleware)) {
            $handler = $this->handlerFactory->createMiddlewareStack($handler, $route, ...$route->middleware);
        }

        $router->addRoute($method, $uri, $handler);
    }
}
