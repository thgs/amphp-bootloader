<?php

namespace thgs\Bootloader\Reflection;

class NativeReflector implements ReflectorInterface
{
    /**
     * @return array<string, string|class-string>
     */
    public function reflectParameterTypes(object $object, string $method): array
    {
        $method = (new \ReflectionObject($object))->getMethod($method);

        $types = [];
        foreach ($method->getParameters() as $parameter) {
            $reflectionType = $parameter->getType();
            if ($reflectionType instanceof \ReflectionNamedType) {
                $type = $reflectionType->getName();
                $types[$parameter->getName()] = $type;
                continue;
            }

            if ($reflectionType instanceof \ReflectionUnionType) {
                throw new \Exception('Unable to handle this type yet!');
            }

            if ($reflectionType instanceof \ReflectionIntersectionType) {
                throw new \Exception('Unable to handle this type yet!');
            }

            $types[$parameter->getName()] = 'null';
        }
        return $types;
    }
}