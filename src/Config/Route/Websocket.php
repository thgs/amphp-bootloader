<?php declare(strict_types=1);

namespace thgs\Bootstrap\Config\Route;

use Amp\Http\Server\Middleware;
use Amp\Websocket\Server\WebsocketAcceptor;
use Amp\Websocket\Server\WebsocketClientHandler;

readonly class Websocket
{
    public function __construct(
        public string $uri,
        public string $method,

        /** @var class-string<WebsocketAcceptor> */
        public string $acceptor,

        /** @var class-string<WebsocketClientHandler> */
        public string $clientHandler,

        /** @var class-string<Middleware>[] */
        public array $middleware = []
    ) {
    }
}
