<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\PathResolver;

use thgs\Bootstrap\Config\PathResolver;
use thgs\Bootstrap\Config\Route\Fallback;
use thgs\Bootstrap\Config\Route\Path;
use thgs\Bootstrap\Exception\MissingPathDefinitionException;

class DefaultPathResolver implements PathResolver
{
    public function __construct(private readonly ?string $publicDir = null)
    {
    }

    /** @throws MissingPathDefinitionException */
    public function resolve(Path|Fallback $route): ResolvedFile|ResolvedDir
    {
        $givenPath = $route->path;
        if ($givenPath === null) {
            throw new MissingPathDefinitionException($route);
        }

        return match ($route->isDir) {
            true => match (\true) {
                \is_dir($givenPath) => new ResolvedDir($givenPath),
                \is_dir($try = \getcwd() . '/' . $givenPath) => new ResolvedDir($try),

                $this->publicDir !== null
                && \is_dir($try = $this->publicDir . '/' . $givenPath) => new ResolvedDir($try),

                // for now, to allow for different filesystem drivers
                default => new ResolvedDir($givenPath)
            },
            false => match (\true) {
                \is_file($givenPath) => new ResolvedFile($givenPath),
                \is_file($try = \getcwd() . '/' . $givenPath) => new ResolvedFile($try),

                $this->publicDir !== null
                && \is_file($try = $this->publicDir . '/' . $givenPath) => new ResolvedFile($try),

                // for now, to allow for different filesystem drivers
                default => new ResolvedFile($givenPath)
            }
        };
    }
}
