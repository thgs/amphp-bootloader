<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\PathResolver;

final readonly class ResolvedFile
{
    public function __construct(public string $path)
    {
    }
}
