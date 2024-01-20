<?php

use Auryn\Injector;
use thgs\Bootstrap\Bootstrap;
use thgs\Bootstrap\Config\Loader\PhpFileLoader;
use thgs\Bootstrap\DependencyInjection\AurynInjector;

require \dirname(__DIR__) . '/vendor/autoload.php';

try {
    new Bootstrap(
        (new PhpFileLoader(
            getcwd() . '/' . ($argv[1] ?? 'config.php')
        ))->load(),
        new AurynInjector(new Injector())
    );
} catch (\Throwable $e) {
    print 'Amphp Bootstrap: ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
