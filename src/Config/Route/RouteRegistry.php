<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\Route;

use thgs\Bootstrap\Config\RouterBuilder;

/**
 * @psalm-import-type RouteConstructor from RouterBuilder
 *
 * @implements \IteratorAggregate<string, RouteConstructor>
 */
final readonly class RouteRegistry implements \IteratorAggregate
{
    public function __construct(
        /** @var RouteConstructor[] */
        private array $routes,
        /** @var Fallback|null */
        private ?Fallback $fallback = null
    ) {
    }

    public function getIterator(): \Traversable
    {
        yield from $this->routes;
    }

    public function getFallback(): ?Fallback
    {
        return $this->fallback;
    }
}
