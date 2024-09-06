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

    /**
     * @inheritDoc
     * @todo Cannot really tell why Psalm complains here
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function create(string $class, Route|Websocket|Fallback|Path|null $forRoute = null): object
    {
        $resolved = $this->container->make($class);
        if (!\is_object($resolved)) {
            throw new \Exception('No support to create() parameters yet.');
        }
        return $resolved;
    }

    /** @inheritDoc */
    public function register(object $instance, ?string $definitionIdentifier = null): void
    {
        $this->container->instance($definitionIdentifier ?? get_class($instance), $instance);
    }
}
