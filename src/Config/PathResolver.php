<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config;

use thgs\Bootstrap\Config\PathResolver\ResolvedDir;
use thgs\Bootstrap\Config\PathResolver\ResolvedFile;
use thgs\Bootstrap\Config\Route\Path;

interface PathResolver
{
    public function resolve(Path $route): ResolvedFile|ResolvedDir;
}
