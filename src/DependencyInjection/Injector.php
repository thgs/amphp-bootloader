<?php declare(strict_types=1);

namespace thgs\Bootloader\DependencyInjection;

interface Injector
{
    /**
     * @template T
     * @param class-string<T> $class
     * @param string|null $forRoute Optional contextual information that may be useful for the injector
     * @return T
     */
    public function create(string $class, ?string $forRoute = null): object;

    /**
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    public function newInstance(string $class): object;
}
