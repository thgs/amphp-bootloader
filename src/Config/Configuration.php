<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config;

final readonly class Configuration
{
    public function __construct(
        public ServerConfiguration $server,
        public RequestHandlerConfiguration $requestHandler,
        public LoggingConfiguration $logging = new LoggingConfiguration(),
        public ?SessionConfiguration $session = null,

        /**
         * Placeholder for any custom configuration.
         *
         * @var array
         */
        public array $custom = [],
    ) {
    }
}
