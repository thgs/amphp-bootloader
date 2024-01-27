<?php declare(strict_types=1);

use thgs\Bootstrap\Config\Route;

return [
    'routes' => [
        'welcome' => new Route\Route(
            uri: '/',
            method: 'GET',
            handler: HelloHandler::class,
        ),
        'home' => new Route\Route(
            uri: '/home',           // use /home?auth=123 to pass
            method: 'GET',
            handler: HomeHandler::class,
            middleware: [AuthMiddleware::class]
        ),
    ],
];
