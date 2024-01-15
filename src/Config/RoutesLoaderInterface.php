<?php declare(strict_types=1);

namespace thgs\Bootloader\Config;

use thgs\Bootloader\Config\Route\RouteRegistry;

interface RoutesLoaderInterface
{
    public function load(): RouteRegistry;
}
