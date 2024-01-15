<?php

namespace thgs\Bootloader\Config;

use thgs\Bootloader\Config\Route\RouteRegistry;

interface RoutesLoaderInterface
{
    public function load(): RouteRegistry;
}