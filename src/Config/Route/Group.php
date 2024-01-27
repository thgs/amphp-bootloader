<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\Route;

use Amp\Http\Server\Middleware;
use thgs\Bootstrap\Config\RouterBuilder;
use Traversable;

/**
 * @psalm-import-type RouteConstructor from RouterBuilder
 * @implements \IteratorAggregate<Route|Group>
 */
readonly class Group implements \IteratorAggregate
{
    public function __construct(
        /** @var RouteConstructor[] */
        public array $routes,
        /** @var array<class-string<Middleware>> */
        public array $middleware,
        public string $prefix = ''
    ) {
    }

    public function getIterator(): Traversable
    {
        foreach ($this->routes as $i => $route) {
            if ($route instanceof Route) {
                $newRoute = new Route(
                    $this->prefix . $route->uri,
                    $route->method,
                    $route->handler,
                    $this->middleware
                );
            }

            if ($route instanceof Group) {
                yield from $route->getIterator();
                continue;
            }

            if (!isset($newRoute)) {
                throw new \Exception('Group cannot handle route type of ' . \get_class($route));
            }

            yield $i => $newRoute;
        }
    }
}
