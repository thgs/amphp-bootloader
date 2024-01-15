<?php declare(strict_types=1);

use thgs\Bootloader\Config\Configuration;
use thgs\Bootloader\Config\LoggingConfiguration;
use thgs\Bootloader\Config\RequestHandlerConfiguration;
use thgs\Bootloader\Config\ServerConfiguration;

require dirname(__DIR__) . '/vendor/autoload.php';
require "HelloHandler.php";

return new Configuration(
    server: new ServerConfiguration(
        servers: ['127.0.0.1:1337'],
        documentRoot: 'public'
    ),
    requestHandler: new RequestHandlerConfiguration(
        routeFile: __DIR__ . '/routes.php'
    ),
    logging: new LoggingConfiguration(
        name: 'hellow'
    )
);
