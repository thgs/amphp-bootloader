<?php declare(strict_types=1);

namespace thgs\Bootloader\DependencyInjection;

use Auryn\Injector;
use thgs\Bootloader\Config\Route\Delegate;
use thgs\Bootloader\Config\Route\Fallback;
use thgs\Bootloader\Config\Route\Route;
use thgs\Bootloader\Config\Route\Websocket;
use thgs\Bootloader\DependencyInjection\Injector as InjectorInterface;

class AurynInjector implements InjectorInterface
{
    public function __construct(private Injector $auryn)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(string $class, Route|Delegate|Websocket|Fallback|null $forRoute = null): object
    {
        $object = $this->auryn->make($class);
        // just a quick way to get rid of psalm complaints
        \assert($object instanceof $class);

        return $object;
    }
}
