<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\Loader;

use thgs\Bootstrap\Config\ConfigLoader;
use thgs\Bootstrap\Config\Configuration;
use thgs\Bootstrap\Exception\ConfigurationException;

final readonly class PhpFileLoader implements ConfigLoader
{
    private Configuration $configuration;

    public function __construct(string $file)
    {
        // acceptable to block here as this class will be used in initialisation
        if (!\is_readable($file)) {
            throw ConfigurationException::unreadableConfigFile($file);
        }

        $returned = include_once $file;
        if (!$returned instanceof Configuration) {
            throw ConfigurationException::unableToRetrieveConfiguration($file, 'Configuration');
        }
        $this->configuration = $returned;
    }

    public function load(): Configuration
    {
        return $this->configuration;
    }
}
