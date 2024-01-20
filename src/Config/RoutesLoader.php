<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config;

use thgs\Bootstrap\Config\Route\RouteRegistry;

interface RoutesLoader
{
    public function load(): RouteRegistry;
}
