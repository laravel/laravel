# Predis #

[![Latest Stable Version](https://poser.pugx.org/predis/predis/v/stable.png)](https://packagist.org/packages/predis/predis)
[![Total Downloads](https://poser.pugx.org/predis/predis/downloads.png)](https://packagist.org/packages/predis/predis)

Predis is a flexible and feature-complete [Redis](http://redis.io) client library for PHP >= 5.3.

By default Predis does not require any additional C extension, but it can be optionally paired with
[phpiredis](https://github.com/nrk/phpiredis) to lower the overhead of serializing and parsing the
Redis protocol. An asynchronous implementation of the client, albeit experimental, is also available
through [Predis\Async](https://github.com/nrk/predis-async).

Predis can be used with [HHVM](http://www.hhvm.com) >= 2.3.0, but there are no guarantees you will
not run into unexpected issues (especially when the JIT compiler is enabled via `Eval.Jit = true`)
due to HHVM being still under heavy development, thus unstable and not yet 100% compatible with PHP.

More details about the project can be found in our [frequently asked questions](FAQ.md) section or
on the online [wiki](https://github.com/nrk/predis/wiki).


## Main features ##

- Wide range of Redis versions supported (from __1.2__ to __3.0__ and __unstable__) using profiles.
- Cluster of nodes via client-side sharding using consistent hashing or custom distributors.
- Smart support for [redis-cluster](http://redis.io/topics/cluster-tutorial) (Redis >= 3.0).
- Support for master-slave replication configurations (write on master, read from slaves).
- Transparent key prefixing for all known Redis commands.
- Command pipelining (works on both single nodes and aggregate connections).
- Abstraction for Redis transactions (Redis >= 2.0) supporting CAS operations (Redis >= 2.2).
- Abstraction for Lua scripting (Redis >= 2.6) with automatic switching between `EVALSHA` or `EVAL`.
- Abstraction for `SCAN`, `SSCAN`, `ZSCAN` and `HSCAN` (Redis >= 2.8) based on PHP iterators.
- Connections to Redis are established lazily by the client upon the first command.
- Support for both TCP/IP and UNIX domain sockets and persistent connections.
- Support for [Webdis](http://webd.is) (both `ext-curl` and `ext-phpiredis` are needed).
- Support for custom connection classes for providing different network or protocol backends.
- Flexible system for defining and registering custom sets of supported commands or profiles.


## How to use Predis ##

Predis is available on [Packagist](http://packagist.org/packages/predis/predis) which allows a quick
installation using [Composer](http://packagist.org/about-composer). Alternatively, the library can
be found on our [own PEAR channel](http://pear.nrk.io) for a more traditional installation via PEAR.
Ultimately, archives of each release are [available on GitHub](https://github.com/nrk/predis/tags).


### Loading the library ###

Predis relies on the autoloading features of PHP to load its files when needed and complies with the
[PSR-0 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) which makes
it compatible with most PHP frameworks. Autoloading is handled automatically when dependencies are
managed through Composer, but it is also possible to leverage its own autoloader in projects or
scripts not having any autoload facility:

```php
// Prepend a base path if Predis is not available in your "include_path".
require 'Predis/Autoloader.php';

Predis\Autoloader::register();
```

It is possible to easily create a [phar](http://www.php.net/manual/en/intro.phar.php) archive from
the repository just by launching `bin/create-phar`. The generated phar contains a stub defining an
autoloader function for Predis, so you just need to require the phar to start using the library.
Alternatively it is possible to generate one single PHP file holding every class like older versions
of Predis by launching `bin/create-single-file`, but this practice __is not__ encouraged.


### Connecting to Redis ###

When not specifying any connection parameter to create a new client, Predis assumes `127.0.0.1` and
`6379` as the default host and port with a connection timeout of 5 seconds:

```php
$client = new Predis\Client();
$client->set('foo', 'bar');
$value = $client->get('foo');
```

Connection parameters can be supplied either in the form of URI strings or named arrays. While the
latter is the preferred way to supply parameters, URI strings can be useful for quick configurations
or when parameters are read from a non-structured source:

```php
// Named array of connection parameters:
$client = new Predis\Client([
    'scheme' => 'tcp',
    'host'   => '10.0.0.1',
    'port'   => 6379,
]);

// Same set of parameters, but using an URI string:
$client = new Predis\Client('tcp://10.0.0.1:6379');
```

When an array of connection parameters is provided, Predis automatically works in cluster mode using
client-side sharding. Both named arrays and URI strings can be mixed when providing configurations
for each node:

```php
$client = new Predis\Client([
    'tcp://10.0.0.1?alias=first-node',
    ['host' => '10.0.0.2', 'alias' => 'second-node'],
]);
```

The actual list of supported connection parameters can vary depending on each connection backend so
it is recommended to refer to their specific documentation or implementation for details.


### Client configuration ###

Various aspects of the client can be configured simply by passing options to the second argument of
`Predis\Client::__construct()`. Options are managed using a mini DI-alike container and their values
are usually lazily initialized only when needed. Predis by default supports the following options:

  - `profile`: which profile to use in order to match a specific version of Redis.
  - `prefix`: a prefix string that is automatically applied to keys found in commands.
  - `exceptions`: whether the client should throw or return responses upon Redis errors.
  - `connections`: connection backends or a connection factory to be used by the client.
  - `cluster`: which backend to use for clustering (`predis`, `redis` or custom configuration).
  - `replication`: which backend to use for replication (predis or custom configuration).

Users can provide custom option values, they are stored in the options container and can be accessed
later through the library.


### Aggregate connections ###

Predis is able to aggregate multiple connections which is the base for cluster and replication. By
default the client implements a cluster of nodes using either client-side sharding (default) or a
Redis-backed solution using [redis-cluster](http://redis.io/topics/cluster-tutorial).
As for replication, Predis can handle a single-master and multiple-slaves setup by executing read
operations on slaves and switching to the master for write operations. The replication behavior is
fully configurable.

#### Replication ####

The client can be configured to work in a master / slave replication setup by executing read-only
commands on slave nodes and automatically switch to the master node as soon as a command performing
a write operation is executed. This is the basic configuration needed to work with replication:

```php
// Parameters require one master node specifically marked with `alias=master`.
$parameters = ['tcp://10.0.0.1?alias=master', 'tcp://10.0.0.2?alias=slave-01'];
$options    = ['replication' => true];

$client = new Predis\Client($parameters, $options);
```

While Predis is able to distinguish commands performing write and read-only operations, `EVAL` and
`EVALSHA` represent a corner case in which the client switches to the master node because it is not
able to tell when a Lua script is safe to be executed on slaves. While this is the default behavior,
when certain Lua scripts do not perform write operations it is possible to provide an hint to tell
the client to stick with slaves for their execution.

```php
$parameters = ['tcp://10.0.0.1?alias=master', 'tcp://10.0.0.2?alias=slave-01'];
$options    = ['replication' => function () {
    // Set scripts that won't trigger a switch from a slave to the master node.
    $strategy = new Predis\Replication\ReplicationStrategy();
    $strategy->setScriptReadOnly($LUA_SCRIPT);

    return new Predis\Connection\Aggregate\MasterSlaveReplication($strategy);
}];

$client = new Predis\Client($parameters, $options);
$client->eval($LUA_SCRIPT, 0);             // Sticks to slave using `eval`...
$client->evalsha(sha1($LUA_SCRIPT), 0);    // ... and `evalsha`, too.
```

The `examples` directory contains two complete scripts showing how replication can be configured for
[simple](examples/MasterSlaveReplication.php) or [complex](examples/MasterSlaveReplicationComplex.php)
scenarios.

#### Cluster ####

Simply passing an array of connection parameters to the client constructor configures Predis to work
in cluster mode using client-side sharding. If you, on the other hand, want to leverage Redis >= 3.0
nodes coordinated by redis-cluster, then the client must be initialized like this:

```php
$parameters = ['tcp://10.0.0.1', 'tcp://10.0.0.2'];
$options    = ['cluster' => 'redis'];

$client = new Predis\Client($parameters, $options);
```

When using redis-cluster, it is not necessary to pass all of the nodes that compose your cluster but
you can simply specify only a few nodes: Predis will automatically fetch the full and updated slots
map directly from Redis by contacting one of the servers.

__NOTE__: our support for redis-cluster does not currently consider master / slave replication but
this feature will be added in a future release of this library.


### Command pipelines ###

Pipelining can help with performances when many commands need to be sent to a server by reducing the
latency introduced by network round-trip timings. Pipelining also works with aggregate connections.
The client can execute the pipeline inside a callable block or return a pipeline instance with the
ability to chain commands thanks to its fluent interface:

```php
// Executes a pipeline inside a given callable block:
$responses = $client->pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", str_pad($i, 4, '0', 0));
        $pipe->get("key:$i");
    }
});

// Returns a pipeline instance with fluent interface:
$responses = $client->pipeline()->set('foo', 'bar')->get('foo')->execute();
```


### Transactions ###

The client provides an abstraction for Redis transactions based on `MULTI` and `EXEC` with a similar
interface to command pipelines:

```php
// Executes a transaction inside a given callable block:
$responses = $client->transaction(function ($tx) {
    $tx->set('foo', 'bar');
    $tx->get('foo');
});

// Returns a transaction instance with fluent interface:
$responses = $client->transaction()->set('foo', 'bar')->get('foo')->execute();
```

This abstraction can perform check-and-set operations thanks to `WATCH` and `UNWATCH` and provides
automatic retries of transactions aborted by Redis when `WATCH`ed keys are touched. For an example
of a transaction using CAS you can see [the following example](examples/TransactionWithCAS.php).

__NOTE__: the method `transaction()` is available since `v0.8.5`, older versions used `multiExec()`
for the same purpose but it has been deprecated and will be removed in the next major release.


### Adding new Redis commands ###

While we try to update Predis to stay up to date with all the commands available in Redis, you might
prefer to stick with an older version of the library or provide a different way to filter arguments
or parse responses for specific commands. To achieve that, Predis provides the ability to implement
new command classes to define or override commands in the server profiles used by the client:

```php
// Define a new command by extending Predis\Command\AbstractCommand:
class BrandNewRedisCommand extends Predis\Command\AbstractCommand
{
    public function getId()
    {
        return 'NEWCMD';
    }
}

// Inject your command in the current profile:
$client = new Predis\Client();
$client->getProfile()->defineCommand('newcmd', 'BrandNewRedisCommand');

$response = $client->newcmd();
```


### Script commands ###

While it is possible to leverage [Lua scripting](http://redis.io/commands/eval) on Redis 2.6+ using
[`EVAL`](http://redis.io/commands/eval) and [`EVALSHA`](http://redis.io/commands/evalsha), Predis
offers script commands as an higher level abstraction aiming to make things simple. Script commands
can be registered in the server profile used by the client and are accessible as if they were plain
Redis commands, but they define Lua scripts that get transmitted to the server for remote execution.
Internally they use [`EVALSHA`](http://redis.io/commands/evalsha) by default and identify a script
by its SHA1 hash to save bandwidth, but [`EVAL`](http://redis.io/commands/eval) is used as a fall
back when needed:

```php
// Define a new scriptable command by extending Predis\Command\ScriptedCommand:
class ListPushRandomValue extends Predis\Command\ScriptedCommand
{
    public function getKeysCount()
    {
        return 1;
    }

    public function getScript()
    {
        return <<<LUA
math.randomseed(ARGV[1])
local rnd = tostring(math.random())
redis.call('lpush', KEYS[1], rnd)
return rnd
LUA;
    }
}

// Inject your script command in the current profile:
$client = new Predis\Client();
$client->getProfile()->defineCommand('lpushrand', 'ListPushRandomValue');

$response = $client->lpushrand('random_values', $seed = mt_rand());
```


### Customizable connection backends ###

Predis can use different connection backends to connect to Redis. Two of them leverage a third party
extension such as [phpiredis](https://github.com/nrk/phpiredis) resulting in major performance gains
especially when dealing with big multibulk responses. While one is based on PHP streams, the other
is based on socket resources provided by `ext-socket`. Both support TCP/IP or UNIX domain sockets:

```php
$client = new Predis\Client('tcp://127.0.0.1', [
    'connections' => [
        'tcp'  => 'Predis\Connection\PhpiredisStreamConnection', // PHP streams
        'unix' => 'Predis\Connection\PhpiredisConnection',       // ext-socket
    ],
]);
```

Developers can create their own connection classes to support whole new network backends, extend
existing ones or provide completely different implementations. Connection classes must implement
`Predis\Connection\SingleConnectionInterface` or extend `Predis\Connection\AbstractConnection`:

```php
class MyConnectionClass implements Predis\Connection\SingleConnectionInterface
{
    // Implementation goes here...
}

// Use MyConnectionClass to handle connections for the `tcp` scheme:
$client = new Predis\Client('tcp://127.0.0.1', [
    'connections' => ['tcp' => 'MyConnectionClass'],
]);
```

For a more in-depth insight on how to create new connection backends you can refer to the actual
implementation of the standard connection classes available in the `Predis\Connection` namespace.


## Development ##


### Reporting bugs and contributing code ###

Contributions to Predis are highly appreciated either in the form of pull requests for new features,
bug fixes, or just bug reports. We only ask you to adhere to a [basic set of rules](CONTRIBUTING.md)
before submitting your changes or filing bugs on the issue tracker to make it easier for everyone to
stay consistent while working on the project.


### Test suite ###

__ATTENTION__: Do not ever run the test suite shipped with Predis against instances of Redis running
in production environments or containing data you are interested in!

Predis has a comprehensive test suite covering every aspect of the library. This test suite performs
integration tests against a running instance of Redis (>= 2.4.0 is required) to verify the correct
behavior of the implementation of each command and automatically skips commands not defined in the
specified Redis profile. If you do not have Redis up and running, integration tests can be disabled.
By default the test suite is configured to execute integration tests using the profile for Redis 2.8
(which is the current stable version of Redis) but can optionally target a Redis instance built from
the `unstable` branch by modifying `phpunit.xml` and setting `REDIS_SERVER_VERSION` to `dev` so that
the development server profile will be used. You can refer to [the tests README](tests/README.md)
for more detailed information about testing Predis.

Predis uses Travis CI for continuous integration and the history for past and current builds can be
found [on its project page](http://travis-ci.org/nrk/predis).


## Other ##


### Project related links ###

- [Source code](https://github.com/nrk/predis)
- [Wiki](https://wiki.github.com/nrk/predis)
- [Issue tracker](https://github.com/nrk/predis/issues)
- [PEAR channel](http://pear.nrk.io)


### Author ###

- [Daniele Alessandri](mailto:suppakilla@gmail.com) ([twitter](http://twitter.com/JoL1hAHN))


### License ###

The code for Predis is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).
