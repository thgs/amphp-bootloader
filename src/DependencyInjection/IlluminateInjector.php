<?php

namespace thgs\Bootstrap\DependencyInjection;

use Illuminate\Container\Container as IlluminateContainer;
use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Path;
use thgs\Bootstrap\Config\Route\Route;
use thgs\Bootstrap\Config\Route\Websocket;

class IlluminateInjector implements Injector
{
    public function __construct(
        private IlluminateContainer $container
    ) {
    }

    /** @inheritDoc */
    public function create(string $class, Route|Websocket|Fallback|Path|null $forRoute = null): object
    {
        return $this->container->make($class);
    }

    /** @inheritDoc */
    public function register($instance, ?string $definitionIdentifier = null): void
    {
        $this->container->instance($definitionIdentifier ?? get_class($instance), $instance);
    }
}
