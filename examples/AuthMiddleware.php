<?php declare(strict_types=1);

use Amp\Http\Server\Middleware;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use function Amp\Http\Server\redirectTo;

class AuthMiddleware implements Middleware
{
    public function handleRequest(Request $request, RequestHandler $requestHandler): Response
    {
        // some auth logic
        $authToken = $request->getQueryParameter('auth');
        if ($authToken === '123') {
            // passed
            return $requestHandler->handleRequest($request);
        }

        // failed
        return redirectTo('/');
    }
}
