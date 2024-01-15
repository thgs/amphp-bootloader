<?php

namespace thgs\Bootloader\Config\Route;

use Traversable;

/**
 * @implements \IteratorAggregate<string, Route|Delegate|Group>
 */
final class RouteRegistry implements \IteratorAggregate
{
    public function __construct(private array $routes, private ?string $fallback)
    {
    }

    public function getIterator(): Traversable
    {
        yield from $this->routes;
    }

    public function getFallback(): ?string
    {
        return $this->fallback;
    }
}