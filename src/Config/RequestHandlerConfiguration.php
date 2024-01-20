<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config;

class RequestHandlerConfiguration
{
    public function __construct(
        public string $routeFile,
        public ?string $publicDir = null,
        // todo: add a way to include a file and get a router back
    ) {
    }
}
