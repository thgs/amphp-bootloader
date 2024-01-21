<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\Route;

use Amp\File\FilesystemDriver;
use Amp\Http\Server\Middleware;
use Amp\Http\Server\RequestHandler;

final readonly class Fallback
{
    public function __construct(
        /**
         * If this is set it will override isDir and path.
         *
         * @var class-string<RequestHandler>|null
         */
        public ?string $handler = null,

        /**
         * You may pass a full path or a relative path.
         * Relative paths will be resolved from current working directory
         * or given public dir (with this priority).
         */
        public ?string $path = null,

        /**
         * @var bool
         */
        public bool $isDir = true,

        /**
         * Only usable for directories currently.
         *
         * @var class-string<FilesystemDriver>|null
         */
        public ?string $filesystemDriver = null,

        /**
         * @var class-string<Middleware>[]
         */
        public array $middleware = []

        // todo: add delegate / websocket ?
    ) {
    }
}
