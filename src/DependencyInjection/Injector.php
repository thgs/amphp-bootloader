<?php declare(strict_types=1);

namespace thgs\Bootloader\DependencyInjection;

use thgs\Bootloader\Config\Route\Delegate;
use thgs\Bootloader\Config\Route\Fallback;
use thgs\Bootloader\Config\Route\Route;
use thgs\Bootloader\Config\Route\Websocket;

interface Injector
{
    /**
     * @template T
     * @param class-string<T> $class
     * @param Route|Delegate|Websocket|Fallback|null $forRoute Optional contextual information that may be useful for the injector
     * @return T
     */
    public function create(string $class, Route|Delegate|Websocket|Fallback|null $forRoute = null): object;
}
