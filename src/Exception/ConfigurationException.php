<?php declare(strict_types=1);

namespace thgs\Bootstrap\Exception;

class ConfigurationException extends \Exception
{
    public const UNREADABLE_CONFIG_FILE = 'Configuration file "%s" is not readable';

    public const UNABLE_TO_RETRIEVE_CONFIG = 'File "%s" did not return a %s object.';

    public static function unreadableConfigFile(string $file): self
    {
        return new self(\sprintf(self::UNREADABLE_CONFIG_FILE, $file));
    }

    public static function unableToRetrieveConfiguration(string $file, string $configType): self
    {
        return new self(\sprintf(self::UNABLE_TO_RETRIEVE_CONFIG, $file, $configType));
    }
}
