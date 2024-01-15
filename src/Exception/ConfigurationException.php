<?php

namespace thgs\Bootloader\Exception;

class ConfigurationException extends \Exception
{
    public const UNREADABLE_CONFIG_FILE = 'Configuration file is not readable';

    public const UNABLE_TO_RETRIEVE_CONFIG = 'File did not return a Configuration object.';

    public static function unreadableConfigFile(): self
    {
        return new self(self::UNREADABLE_CONFIG_FILE);
    }

    public static function unableToRetrieveConfiguration(): self
    {
        return new self(self::UNABLE_TO_RETRIEVE_CONFIG);
    }
}