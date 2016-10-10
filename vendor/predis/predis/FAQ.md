# Some frequently asked questions about Predis #
________________________________________________

### What is the point of Predis? ###

The main point of Predis is about offering a highly customizable and extensible client for Redis,
that can be easily extended by developers while still being reasonabily fast. With Predis you can
swap almost any class with your own custom implementation: you can have custom connection classes,
new distribution strategies for client-side sharding, or handlers to replace or add Redis commands.
All of this can be achieved without messing with the source code of the library and directly in your
own application. Given the fast pace at which Redis is developed and adds new features, this can be
a great asset since it allows developers to add new and still missing features or commands or change
the standard behaviour of the library without the need to break dependencies in production code (at
least to some degree).

### Does Predis support UNIX domain sockets and persistent connections? ###

Yes. Obviously persistent connections actually work only when using PHP configured as a persistent
process reused by the web server (see [PHP-FPM](http://php-fpm.org)).

### Does Predis support transparent (de)serialization of values? ###

No and it will not ever do that by default. The reason behind this decision is that serialization is
usually something that developers prefer to customize depending on their needs and can not be easily
generalized when using Redis because of the many possible access patterns for your data. This does
not mean that it is impossible to have such a feature since you can leverage the extensibility of
this library to define your own serialization-aware commands. You can find more details about how to
do that [on this issue](http://github.com/nrk/predis/issues/29#issuecomment-1202624).

### How can I force Predis to connect to Redis before sending any command? ###

Explicitly connecting to Redis is usually not needed since the client initializes connections lazily
only when they are needed. Admittedly, this behavior can be inconvenient in certain scenarios when
you absolutely need to perform an upfront check to determine if the server is up and running and
eventually catch exceptions on failures. Forcing the client to open the underlying connection can be
done by invoking `Predis\Client::connect()`:

```php
$client = new Predis\Client();

try {
    $client->connect();
} catch (Predis\Connection\ConnectionException $exception) {
    // We could not connect to Redis! Your handling code goes here.
}

$client->info();
```

### How Predis abstracts Redis commands? ###

The approach used to implement Redis commands is quite simple: by default each command follows the
same signature as defined on the [Redis documentation](http://redis.io/commands) which makes things
pretty easy if you already know how Redis works or you need to look up how to use certain commands.
Alternatively, variadic commands can accept an array for keys or values (depending on the command)
instead of a list of arguments. Commands such as [`RPUSH`](http://redis.io/commands/rpush) and
[`HMSET`](http://redis.io/commands/hmset) are great examples:

```php
$client->rpush('my:list', 'value1', 'value2', 'value3');             // plain method arguments
$client->rpush('my:list', ['value1', 'value2', 'value3']);           // single argument array

$client->hmset('my:hash', 'field1', 'value1', 'field2', 'value2');   // plain method arguments
$client->hmset('my:hash', ['field1'=>'value1', 'field2'=>'value2']); // single named array
```

An exception to this rule is [`SORT`](http://redis.io/commands/sort) for which modifiers are passed
[using a named array](tests/Predis/Command/KeySortTest.php#L54-L75).


# Speaking about performances... #
_________________________________________________


### Predis is a pure-PHP implementation: it can not be fast enough! ###

It really depends, but most of the times the answer is: _yes, it is fast enough_. I will give you a
couple of easy numbers with a simple test that uses a single client and is executed by PHP 5.5.6
against a local instance of Redis 2.8 that runs under Ubuntu 13.10 on a Intel Q6600:

```
21000 SET/sec using 12 bytes for both key and value.
21000 GET/sec while retrieving the very same values.
0.130 seconds to fetch 30000 keys using _KEYS *_.
```

How does it compare with [__phpredis__](http://github.com/nicolasff/phpredis), a nice C extension
providing an efficient client for Redis?

```
30100 SET/sec using 12 bytes for both key and value
29400 GET/sec while retrieving the very same values
0.035 seconds to fetch 30000 keys using "KEYS *"".
```

Wow __phpredis__ seems much faster! Well, we are comparing a C extension with a pure-PHP library so
lower numbers are quite expected but there is a fundamental flaw in them: is this really how you are
going to use Redis in your application? Are you really going to send thousands of commands using a
for-loop on each page request using a single client instance? If so... well I guess you are probably
doing something wrong. Also, if you need to `SET` or `GET` multiple keys you should definitely use
commands such as `MSET` and `MGET`. You can also use pipelining to get more performances when this
technique can be used.

There is one more thing: we have tested the overhead of Predis by connecting on a localhost instance
of Redis but how these numbers change when we hit the physical network by connecting to remote Redis
instances?

```
Using Predis:
3200 SET/sec using 12 bytes for both key and value
3200 GET/sec while retrieving the very same values
0.132 seconds to fetch 30000 keys using "KEYS *".

Using phpredis:
3500 SET/sec using 12 bytes for both key and value
3500 GET/sec while retrieving the very same values
0.045 seconds to fetch 30000 keys using "KEYS *".
```

There you go, you get almost the same average numbers and the reason is simple: network latency is a
real performance killer and you cannot do (almost) anything about that. As a disclaimer, remember
that we are measuring the overhead of client libraries implementations and the effects of network
round-trip times, so we are not really measuring how fast Redis is. Redis shines best with thousands
of concurrent clients doing requests! Also, actual performances should be measured according to how
your application will use Redis.

### I am convinced, but performances for multi-bulk responses are still worse ###

Fair enough, but there is an option available if you need even more speed and consists on installing
__[phpiredis](http://github.com/nrk/phpiredis)__ (note the additional _i_ in the name) and let the
client use it. __phpiredis__ is another C extension that wraps __hiredis__ (the official C client
library for Redis) with a thin layer exposing its features to PHP. You can then choose between two
different connection classes:

  - `Predis\Connection\PhpiredisStreamConnection` (using native PHP streams).
  - `Predis\Connection\PhpiredisConnection` (requires `ext-socket`).

You will now get the benefits of a faster protocol serializer and parser just by adding a couple of
lines of code:

```php
$client = new Predis\Client('tcp://127.0.0.1', array(
    'connections' => array(
        'tcp'  => 'Predis\Connection\PhpiredisStreamConnection',
        'unix' => 'Predis\Connection\PhpiredisConnection',
    ),
));
```

Dead simple. Nothing changes in the way you use the library in your application. So how fast is it
our basic benchmark script now? There are not much improvements for inline or short bulk responses
like the ones returned by `SET` and `GET`, but the speed for parsing multi-bulk responses is now on
par with phpredis:

```
Fatching 30000 keys with _KEYS *_ using Predis paired with phpiredis::

0.035 seconds from a local Redis instance
0.047 seconds from a remote Redis instance
```

### If I need an extension to get better performances, why not using phpredis? ###

Good question. Generically speaking if you need absolute uber-speed using Redis on the localhost and
you do not care about abstractions built around some Redis features such as MULTI / EXEC, or if you
do not need any kind of extensibility or guaranteed backwards compatibility with different versions
of Redis (Predis currently supports from 1.2 up to 2.8 and the current development version), then
using __phpredis__ makes absolutely sense. Otherwise, Predis is perfect for the job and by adding
__phpiredis__ you can get a nice speed bump almost for free.
