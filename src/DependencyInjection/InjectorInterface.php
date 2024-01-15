<?php

namespace thgs\Bootloader\DependencyInjection;

interface InjectorInterface
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