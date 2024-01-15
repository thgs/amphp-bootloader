<?php

namespace thgs\Bootloader\Config\Route;

use Amp\Http\Server\Middleware;
use Amp\Http\Server\RequestHandler;

readonly class Route
{
    public function __construct(
        public string $uri,
        public string $method,
        /**
         * @var class-string<RequestHandler>
         */
        public string $handler,
        /**
         * @var class-string<Middleware>[]
         */
        public array $middleware = []
    ) {
    }
}