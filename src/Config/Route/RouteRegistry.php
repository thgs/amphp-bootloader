<?php declare(strict_types=1);

namespace thgs\Bootloader\Config\Route;

use Amp\Http\Server\RequestHandler;
use thgs\Bootloader\RouterBuilder;

/**
 * @psalm-import-type RouteConstructor from RouterBuilder
 *
 * @implements \IteratorAggregate<string, RouteConstructor>
 */
final class RouteRegistry implements \IteratorAggregate
{
    public function __construct(
        /** @var RouteConstructor[] */
        private array $routes,
        /** @var class-string<RequestHandler>|null */
        private ?string $fallback
    ) {
    }

    public function getIterator(): \Traversable
    {
        yield from $this->routes;
    }

    public function getFallback(): ?string
    {
        return $this->fallback;
    }
}
