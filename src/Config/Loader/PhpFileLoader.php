<?php

namespace thgs\Bootloader\Config\Loader;

use thgs\Bootloader\Config\ConfigLoaderInterface;
use thgs\Bootloader\Config\Configuration;
use thgs\Bootloader\Exception\ConfigurationException;

final readonly class PhpFileLoader implements ConfigLoaderInterface
{
    private Configuration $configuration;

    public function __construct(string $file)
    {
        // todo: is_readable is blocking - do we care?
        if (!is_readable($file)) {
            throw ConfigurationException::unreadableConfigFile();
        }

        $returned = include_once $file;
        if (!$returned instanceof Configuration) {
            throw ConfigurationException::unableToRetrieveConfiguration();
        }
        $this->configuration = $returned;
    }

    public function load(): Configuration
    {
        return $this->configuration;
    }
}