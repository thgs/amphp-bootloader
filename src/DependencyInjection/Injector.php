<?php declare(strict_types=1);

namespace thgs\Bootloader\DependencyInjection;

interface Injector
{
    /**
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    public function create(string $class): object;

    /**
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    public function newInstance(string $class): object;

    //    public function invokeMethod(object $object, string $method, array $arguments);
}
