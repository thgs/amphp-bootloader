<?php

use Auryn\Injector;
use thgs\Bootloader\Bootstrap;
use thgs\Bootloader\Config\Loader\PhpFileLoader;
use thgs\Bootloader\DependencyInjection\AurynInjector;

require \dirname(__DIR__) . '/vendor/autoload.php';

try {
    new Bootstrap(
        (new PhpFileLoader(
            getcwd() . '/' . ($argv[1] ?? 'config.php')
        ))->load(),
        new AurynInjector(new Injector())
    );
} catch (\Throwable $e) {
    print 'Amphp Bootloader: ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
