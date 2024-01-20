<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config;

use Amp\Http\Server\Session\LocalSessionStorage;

final readonly class SessionConfiguration
{
    public const DEFAULT_COOKIE_NAME = 'stc';

    public function __construct(
        /**
         * @var class-string
         */
        public string $sessionStorage = LocalSessionStorage::class,

        /**
         * This is only applicable if `sessionStorage === LocalSessionStorage`.
         * Otherwise, if `sessionStorage` is anything else it should be constructed
         * from the container configured.
         *
         * @var int
         */
        public int $sessionLifetime = LocalSessionStorage::DEFAULT_SESSION_LIFETIME,
        public string $cookieName = self::DEFAULT_COOKIE_NAME,

        /**
         * Use `DateTime::modify()` syntax or `null` for no expiry.
         *
         * @var string|null
         */
        public ?string $expiry = '+30 minute',
        public bool $secureCookie = false,
    ) {
    }
}
