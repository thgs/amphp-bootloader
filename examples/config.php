<?php declare(strict_types=1);

use thgs\Bootstrap\Config\Configuration;
use thgs\Bootstrap\Config\LoggingConfiguration;
use thgs\Bootstrap\Config\RequestHandlerConfiguration;
use thgs\Bootstrap\Config\ServerConfiguration;

require dirname(__DIR__) . '/vendor/autoload.php';
require "HelloHandler.php";
require "AuthMiddleware.php";
require "DelegatedHandler.php";
require "HomeHandler.php";

return new Configuration(
    server: new ServerConfiguration(
        servers: ['127.0.0.1:1337'],
        documentRoot: 'public'
    ),
    requestHandler: new RequestHandlerConfiguration(
        routeFile: __DIR__ . '/routes.php'
    ),
    logging: new LoggingConfiguration(
        name: 'hellow-example'
    )
);
