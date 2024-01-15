<?php

namespace thgs\Bootloader\Config;

final readonly class LoggingConfiguration
{
    public function __construct(
        /**
         * Logger name
         *
         * @var string
         */
        public string $name = 'cane',

        public bool $logInStdout = true,

        /**
         * This is applicable only if `logInStdout === false`
         *
         * @var string|null
         */
        public ?string $logFilePath = null,

        public bool $loopDetection = false,
    ) {
    }
}