<?php

namespace thgs\Bootloader\Config;

interface ConfigLoaderInterface
{
    /**
     * Call to retrieve the configuration
     *
     * Consecutive calls to this method should return the same object
     *
     * @return Configuration
     */
    public function load(): Configuration;
}