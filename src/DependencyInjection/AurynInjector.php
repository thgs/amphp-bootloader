<?php declare(strict_types=1);

namespace thgs\Bootloader\DependencyInjection;

use Auryn\Injector;

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
        return $this->auryn->make($class);
    }

    /**
     * @inheritDoc
     */
    public function newInstance(string $class): object
    {
        return $this->auryn->make($class);
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
