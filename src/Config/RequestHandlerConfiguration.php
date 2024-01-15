<?php

namespace thgs\Bootloader\Config;

class RequestHandlerConfiguration
{
    public function __construct(
        public string $routeFile,
        // todo: add a way to include a file and get a router back
    ) {
    }
}