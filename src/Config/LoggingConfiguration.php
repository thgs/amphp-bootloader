<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config;

/** @api */
final readonly class LoggingConfiguration
{
    public function __construct(
        /**
         * Logger name.
         *
         * @var string
         */
        public string $name = 'amphp-httpd',
        public bool $logInStdout = true,

        /**
         * This is applicable only if `logInStdout === false`.
         *
         * @var string|null
         */
        public ?string $logFilePath = null,
        public bool $loopDetection = false,
    ) {
    }
}
