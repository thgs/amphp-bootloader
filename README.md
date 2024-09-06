## amphp-bootstrap

Note that this package is at an **experimental/exploratory** stage.

This is a convenience package that boots an [Amphp HTTP server](https://amphp.org/http-server).
 It exports configuration objects that are used as input and loaders for
the config for a few basic formats. It requires a container
implementation to be adapted to an interface, however some basic
adapters are already provided for some container packages.

### Features

- With the container integration you can setup your routes and use DI.

### Usage

You can boot it like this, example below is with Auryn Injector.

```php
use Auryn\Injector;
use thgs\Bootstrap\Bootstrap;
use thgs\Bootstrap\Config\Loader\PhpFileLoader;
use thgs\Bootstrap\DependencyInjection\AurynInjector;

require \dirname(__DIR__) . '/vendor/autoload.php';

// Loader for config.php which contains the main configuration
$loader = new PhpFileLoader(getcwd() . '/' . ($argv[1] ?? 'config.php'));

// Pick an injector
$injector = new AurynInjector(new Injector());

// Finally, boot everything
new Bootstrap($loader->load(), $injector);
```


See
[`examples/`](https://github.com/thgs/amphp-bootstrap/tree/master/examples)
directory for a sample configuration. All configuration objects
contain the various options you could set up using Amphp directly as
arguments of the constructor.

Out of the box this package supports the below injectors/containers:

* [Auryn](https://github.com/rdlowrey/Auryn)
* [Illuminate Container](https://github.com/illuminate/container)
