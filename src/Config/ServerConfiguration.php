<?php

namespace thgs\Bootloader\Config;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Middleware\AllowedMethodsMiddleware;

final readonly class ServerConfiguration
{
    public function __construct(
        /**
         * A list in the format of `server:port` to expose.
         * For TLS servers use `tlsServers`.
         *
         * @var string[]
         */
        public array $servers = ['127.0.0.1:80'],

        /**
         * @var string[]
         */
        public array $tlsServers = [],

        /**
         * The path to the certificate.
         * This is only applicable if there are any servers in `tlsServers`.
         *
         * @var string|null
         */
        public ?string $certificatePath = null,

        /**
         * @var class-string
         */
        public string $errorHandler = DefaultErrorHandler::class,

        /**
         * Set to a path to use a fallback for static content.
         *
         * @var string|null
         */
        public ?string $documentRoot = null,

        /**
         * Set full paths to static resources
         *
         * @var string[]|null
         */
        public ?array $staticResources = null,

        /**
         * Enable compression during transport.
         *
         * @var bool
         */
        public bool $compression = true,
        /**
         * @var positive-int
         */
        public int $concurrencyLimit = 1000,

        /**
         * Only applies to direct access
         * @var positive-int
         */
        public int $connectionLimit = 1000,

        /**
         * Only applies to direct access
         * @var positive-int
         */
        public int $connectionLimitPerIp = 10,

        public ?array $allowedMethods = AllowedMethodsMiddleware::DEFAULT_ALLOWED_METHODS,


        /**
         * Set to `true` to use createForBehindProxy.
         * If `true` the following settings take effect:
         *
         *      - trustedProxies
         *      - forwardedHeaderType
         *      -
         *
         * @var bool
         */
        public bool $directAccess = true,

        /**
         * This is applicable only if `directAccess == false`.
         *
         * @var string[]
         */
        public array $trustedProxies = [],

        /**
         * Can only be `x-forwarded-for` or `forwarded`.
         * Anything else will fail.
         *
         * This is applicable only if `directAccess == false`.
         *
         * @todo: why is that?
         *
         * @var string
         */
        public string $forwardedHeaderType = 'x-forwarded-for',
    ) {
    }
}