<?php

namespace thgs\Bootloader\Reflection;

interface ReflectorInterface
{
    /**
     * @return array<string, string|class-string>
     */
    public function reflectParameterTypes(object $object, string $method): array;
}