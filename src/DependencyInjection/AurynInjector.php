<?php declare(strict_types=1);

namespace thgs\Bootstrap\DependencyInjection;

use Auryn\Injector;
use thgs\Bootstrap\Config\Route\Delegate;
use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Path;
use thgs\Bootstrap\Config\Route\Route;
use thgs\Bootstrap\Config\Route\Websocket;
use thgs\Bootstrap\DependencyInjection\Injector as InjectorInterface;

class AurynInjector implements InjectorInterface
{
    public function __construct(private Injector $auryn)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(string $class, Route|Delegate|Websocket|Fallback|Path|null $forRoute = null): object
    {
        $object = $this->auryn->make($class);
        // just a quick way to get rid of psalm complaints
        \assert($object instanceof $class);

        return $object;
    }
}
