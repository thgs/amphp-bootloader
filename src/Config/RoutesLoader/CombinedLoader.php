<?php declare(strict_types=1);

namespace thgs\Bootloader\Config\RoutesLoader;

use thgs\Bootloader\Config\Route\RouteRegistry;
use thgs\Bootloader\Config\RoutesLoaderInterface;

final class CombinedLoader implements RoutesLoaderInterface
{
    public function __construct(
        private RoutesLoaderInterface $a,
        private RoutesLoaderInterface $b,
        private ?string $fallback
    ) {
    }

    public function load(): RouteRegistry
    {
        $combined = [];
        foreach ($this->a->load() as $name => $route) {
            $combined[$name] = $route;
        }
        foreach ($this->b->load() as $name => $route) {
            $combined[$name] = $route;
        }

        return new RouteRegistry($combined, $this->fallback);
    }
}
