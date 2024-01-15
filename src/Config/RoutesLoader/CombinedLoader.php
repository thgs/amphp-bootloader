<?php declare(strict_types=1);

namespace thgs\Bootloader\Config\RoutesLoader;

use thgs\Bootloader\Config\Route\RouteRegistry;
use thgs\Bootloader\Config\RoutesLoader;

final class CombinedLoader implements RoutesLoader
{
    public function __construct(
        private RoutesLoader $a,
        private RoutesLoader $b,
        private ?string      $fallback
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
