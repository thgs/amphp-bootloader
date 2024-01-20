<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\RoutesLoader;

use thgs\Bootstrap\Config\Route\RouteRegistry;
use thgs\Bootstrap\Config\RoutesLoader;
use thgs\Bootstrap\Exception\ConfigurationException;

/**
 * A probably blocking AND eager loader. Calls to load() will
 * result to the same output.
 */
class BlockingArrayLoader implements RoutesLoader
{
    private RouteRegistry $registry;

    public function __construct(string $routeFile)
    {
        if (!\is_readable($routeFile)) {
            throw ConfigurationException::unreadableConfigFile($routeFile);
        }

        // todo: would an iffy here guard global vars? do we want to guard?
        $result = require $routeFile;

        if (!\is_array($result)) {
            throw ConfigurationException::unableToRetrieveConfiguration($routeFile, 'array of RouteConstructor');
        }

        // todo: add type checks
        $this->registry = new RouteRegistry($result['routes'] ?? [], $result['fallback'] ?? null);
    }

    public function load(): RouteRegistry
    {
        return $this->registry;
    }
}
