<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\PathResolver;

use thgs\Bootstrap\Config\PathResolver;
use thgs\Bootstrap\Config\Route\Path;

class DefaultPathResolver implements PathResolver
{
    public function __construct(private ?string $publicDir = null)
    {
    }

    public function resolve(Path $route): ResolvedFile|ResolvedDir
    {
        return match ($route->isDir) {
            true => match (\true) {
                \is_dir($route->path) => new ResolvedDir($route->path),
                \is_dir($try = \getcwd() . '/' . $route->path) => new ResolvedDir($try),

                $this->publicDir !== null
                && \is_dir($try = $this->publicDir . '/' . $route->path) => new ResolvedDir($try),

                // for now, to allow for different filesystem drivers
                default => new ResolvedDir($route->path)
            },
            false => match (\true) {
                \is_file($route->path) => new ResolvedFile($route->path),
                \is_file($try = \getcwd() . '/' . $route->path) => new ResolvedFile($try),

                $this->publicDir !== null
                && \is_file($try = $this->publicDir . '/' . $route->path) => new ResolvedFile($try),

                // for now, to allow for different filesystem drivers
                default => new ResolvedFile($route->path)
            }
        };
    }
}
