<?php declare(strict_types=1);

namespace thgs\Bootstrap\DependencyInjection;

use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Path;
use thgs\Bootstrap\Config\Route\Route;
use thgs\Bootstrap\Config\Route\Websocket;

interface Injector
{
    /**
     * @template T
     * @param class-string<T> $class
     * @psalm-type Context = Route|Websocket|Fallback|Path|null
     * @param Context $forRoute Optional contextual information that may be useful
     * @return T
     */
    public function create(string $class, Route|Websocket|Fallback|Path|null $forRoute = null): object;

    /**
     * @param string|class-string|null $definitionIdentifier
     */
    public function register(object $instance, ?string $definitionIdentifier = null): void;
}
