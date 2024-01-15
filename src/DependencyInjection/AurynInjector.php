<?php declare(strict_types=1);

namespace thgs\Bootloader\DependencyInjection;

use Auryn\Injector;
use thgs\Bootloader\DependencyInjection\Injector as InjectorInterface;

class AurynInjector implements InjectorInterface
{
    public function __construct(private Injector $auryn)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(string $class): object
    {
        $object = $this->auryn->make($class);
        // just a quick way to get rid of psalm complaints
        \assert($object instanceof $class);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function newInstance(string $class): object
    {
        $object = $this->auryn->make($class);
        // just a quick way to get rid of psalm complaints
        \assert($object instanceof $class);

        return $object;
    }

    //    public function invokeMethod(object $object, string $method, array $arguments)
    //    {
    //        $args = [];
    //        foreach ($arguments as $key => $argument) {
    //            if (is_string($key)) {
    //                $args[':' . $key] = $argument;
    //                continue;
    //            }
    //            $args[] = $argument;
    //        }
    //        return $this->auryn->execute([$object, $method], $args);
    //    }
}
