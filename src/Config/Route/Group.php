<?php declare(strict_types=1);

namespace thgs\Bootloader\Config\Route;

use Traversable;

/**
 * @implements \IteratorAggregate<Route|Delegate|Group>
 */
readonly class Group implements \IteratorAggregate
{
    public function __construct(
        public array $routes,
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

            if ($route instanceof Delegate) {
                $newRoute = new Delegate(
                    $this->prefix . $route->uri,
                    $route->method,
                    $route->delegate,
                    $route->action,
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
