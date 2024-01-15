<?php

namespace thgs\Bootloader\Config\Route;

use Amp\Http\Server\Middleware;

readonly class Delegate
{
    public function __construct(
        public string $uri,
        public string $method,
        /**
         * @var class-string
         */
        public string $delegate,

        public string $action = '__invoke',

        /**
         * @var class-string<Middleware>[]
         */
        public array $middleware = []
    ) {
    }
}