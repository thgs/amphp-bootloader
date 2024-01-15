<?php declare(strict_types=1);

use thgs\Bootloader\Config\Route;

return [
    'routes' => [
        'hello' => new Route\Route(
            uri: '/',
            method: 'GET',
            handler: HelloHandler::class,
        ),
    ]
];
