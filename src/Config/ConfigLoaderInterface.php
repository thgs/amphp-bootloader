<?php declare(strict_types=1);

namespace thgs\Bootloader\Config;

interface ConfigLoaderInterface
{
    /**
     * Call to retrieve the configuration.
     *
     * Consecutive calls to this method should return the same object
     *
     */
    public function load(): Configuration;
}
