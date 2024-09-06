<?php

use Auryn\Injector;
use Illuminate\Container\Container;
use thgs\Bootstrap\Bootstrap;
use thgs\Bootstrap\Config\Loader\PhpFileLoader;
use thgs\Bootstrap\DependencyInjection\AurynInjector;
use thgs\Bootstrap\DependencyInjection\IlluminateInjector;

require \dirname(__DIR__) . '/vendor/autoload.php';

$injector = new AurynInjector(new Injector());
#$injector = new IlluminateInjector(new Container());

try {
    new Bootstrap(
        (new PhpFileLoader(
            getcwd() . '/' . ($argv[1] ?? 'config.php')
        ))->load(),
        $injector
    );
} catch (\Throwable $e) {
    print 'Amphp Bootstrap: ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
