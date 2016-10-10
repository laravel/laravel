v0.8.7 (2014-08-01)
================================================================================

- Added `3.0` in the server profiles aliases list for Redis 3.0. `2.8` is still
  the default server profile and `dev` still targets Redis 3.0.

- Added `COMMAND` to the server profile for Redis 2.8.

- Switched internally to the `CLUSTER SLOTS` command instead of `CLUSTER NODES`
  to fetch the updated slots map from redis-cluster. This change requires users
  to upgrade Redis nodes to >= 3.0.0b7.

- The updated slots map is now fetched automatically from redis-cluster upon the
  first `-MOVED` response by default. This change makes it possible to feed the
  client constructor with only a few nodes of the actual cluster composition,
  without needing a more complex configuration.

- Implemented support for `PING` in PUB/SUB loop for Redis >= 3.0.0b8.

- The default client-side sharding strategy and the one for redis-cluster now
  share the same implementations as they follow the same rules. One difference,
  aside from the different hashing function used to calculate distribution, is
  in how empty hash tags like {} are treated by redis-cluster.

- __FIX__: the patch applied to fix #180 introduced a regression affecting read/
  write timeouts in `Predis\Connection\PhpiredisStreamConnection`. Unfortunately
  the only possible solution requires PHP 5.4+. On PHP 5.3, read/write timeouts
  will be ignored from now on.


v0.8.6 (2014-07-15)
================================================================================

- Redis 2.8 is now the default server profile as there are no changes that would
  break compatibility with previous releases.

- Added `PFADD`, `PFCOUNT`, `PFMERGE` to the server profile for Redis 2.8 for
  handling the HyperLogLog data structure introduced in Redis 2.8.9.

- Added `ZLEXCOUNT`, `ZRANGEBYLEX`, `ZREMRANGEBYLEX` to the server profile for
  Redis 2.8 for handling lexicographic operations on members of sorted sets.

- Added support for key hash tags when using redis-cluster (Redis 3.0.0b1).

- __FIX__: minor tweaks to make Predis compatible with HHVM >= 2.4.0.

- __FIX__: responses to `INFO` are now properly parsed and will not break when
  redis sentinel is being used (ISSUE #154).

- __FIX__: added missing support for `INCRBYFLOAT` in cluster and replication
  configurations (ISSUE #159).

- __FIX__: fix parsing of the output of `CLUSTER NODES` to fetch the slots map
  from a node when redis-cluster has slaves in its configuration (ISSUE #165).

- __FIX__: prevent a stack overflow when iterating over large Redis collections
  using our abstraction for cursor-based iterators (ISSUE #182).

- __FIX__: properly discards transactions when the server immediately returns an
  error response (e.g. -OOM or -ERR on invalid arguments for a command) instead
  of a +QUEUED response (ISSUE #187).

- Upgraded to PHPUnit 4.* for the test suite.


v0.8.5 (2014-01-16)
================================================================================

- Added `2.8` in the server profiles aliases list for Redis 2.8. `2.6` is still
  the default server profile and `dev` now targets Redis 3.0.

- Added `SCAN`, `SSCAN`, `ZSCAN`, `HSCAN` to the server profile for Redis 2.8.

- Implemented PHP iterators for incremental iterations over Redis collections:

    - keyspace (cursor-based iterator using `SCAN`)
    - sets (cursor-based iterator using `SSCAN`)
    - sorted sets (cursor-based iterator using `ZSCAN`)
    - hashes (cursor-based iterator using `HSCAN`)
    - lists (plain iterator using `LRANGE`)

- It is now possible to execute "raw commands" using `Predis\Command\RawCommand`
  and a variable list of command arguments. Input arguments are not filtered and
  responses are not parsed, which means arguments must follow the signature of
  the command as defined by Redis and complex responses are left untouched.

- URI parsing for connection parameters has been improved and has slightly less
  overhead when the number of fields in the querystring grows. New features are:

    - Parsing does not break when value of a field contains one or more "=".
    - Repeated fieldnames using [] produce an array of values.
    - Empty or incomplete "key=value" pairs result in an empty string for "key".

- Various improvements and fixes to the redis-cluster connection backend:

    - __FIX__: the `ASKING` command is sent upon -ASK redirections.
    - An updated slots-map can be fetched from nodes using the `CLUSTER NODES`
      command. By default this is a manual operation but can be enabled to get
      automatically done upon -MOVED redirections.
    - It is possible to specify a common set of connection parameters that are
      applied to connections created on the fly upon redirections to nodes not
      part of the initial pool.

- List of deprecated methods:

    - `Predis\Client::multiExec()`: superseded by `Predis\Client::transaction()`
      and to be removed in the next major release.
    - `Predis\Client::pubSub()`: superseded by `Predis\Client::pubSubLoop()` and
      to be removed in the next major release. This change was needed due to the
      recently introduced `PUBSUB` command in Redis 2.8.


v0.8.4 (2013-07-27)
================================================================================

- Added `DUMP` and `RESTORE` to the server profile for Redis 2.6.

- Connection exceptions now report basic host details in their messages.

- Allow `Predis\Connection\PhpiredisConnection` to use a random IP when a host
  actually has several IPs (ISSUE #116).

- __FIX__: allow `HMSET` when using a cluster of Redis nodes with client-side
  sharding or redis-cluster (ISSUE #106).

- __FIX__: set `WITHSCORES` modifer for `ZRANGE`, `ZREVRANGE`, `ZRANGEBYSCORE`
  and `ZREVRANGEBYSCORE` only when the options array passed to these commands
  has `WITHSCORES` set to `true` (ISSUE #107).

- __FIX__: scripted commands falling back from `EVALSHA` to `EVAL` resulted in
  PHP errors when using a prefixed client (ISSUE #109).

- __FIX__: `Predis\PubSub\DispatcherLoop` now works properly when using key
  prefixing (ISSUE #114).


v0.8.3 (2013-02-18)
================================================================================

- Added `CLIENT SETNAME` and `CLIENT GETNAME` (ISSUE #102).

- Implemented the `Predis\Connection\PhpiredisStreamConnection` class using the
  `phpiredis` extension like `Predis\Connection\PhpiredisStreamConnection`, but
  without requiring the `socket` extension since it relies on PHP's streams.

- Added support for the TCP_NODELAY flag via the `tcp_nodelay` parameter for
  stream-based connections, namely `Predis\Connection\StreamConnection` and
  `Predis\Connection\PhpiredisStreamConnection` (requires PHP >= 5.4.0).

- Updated the aggregated connection class for redis-cluster to work with 16384
  hash slots instead of 4096 to reflect the recent change from redis unstable
  ([see this commit](https://github.com/antirez/redis/commit/ebd666d)).

- The constructor of `Predis\Client` now accepts a callable as first argument
  returning `Predis\Connection\ConnectionInterface`. Users can create their
  own self-contained strategies to create and set up the underlying connection.

- Users should return `0` from `Predis\Command\ScriptedCommand::getKeysCount()`
  instead of `FALSE` to indicate that all of the arguments of a Lua script must
  be used to populate `ARGV[]`. This does not represent a breaking change.

- The `Predis\Helpers` class has been deprecated and it will be removed in
  future releases.


v0.8.2 (2013-02-03)
================================================================================

- Added `Predis\Session\SessionHandler` to make it easy to store PHP sessions
  on Redis using Predis. Please note that this class needs either PHP >= 5.4.0
  or a polyfill for PHP's `SessionHandlerInterface`.

- Added the ability to get the default value of a client option directly from
  `Predis\Option\ClientOption` using the `getDefault()` method by passing the
  option name or its instance.

- __FIX__: the standard pipeline executor was not using the response parser
  methods associated to commands to process raw responses (ISSUE #101).


v0.8.1 (2013-01-19)
================================================================================

- The `connections` client option can now accept a callable object returning
  an instance of `Predis\Connection\ConnectionFactoryInterface`.

- Client options accepting callable objects as factories now pass their actual
  instance to the callable as the second argument.

- `Predis\Command\Processor\KeyPrefixProcessor` can now be directly casted to
  string to obtain the current prefix, useful with string interpolation.

- Added an optional callable argument to `Predis\Cluster\Distribution\HashRing`
  and `Predis\Cluster\Distribution\KetamaPureRing` constructor that can be used
  to customize how the distributor should extract the connection hash when
  initializing the nodes distribution (ISSUE #36).

- Correctly handle `TTL` and `PTTL` returning -2 on non existing keys starting
  with Redis 2.8.

- __FIX__: a missing use directive in `Predis\Transaction\MultiExecContext`
  caused PHP errors when Redis did not return `+QUEUED` replies to commands
  when inside a MULTI / EXEC context.

- __FIX__: the `parseResponse()` method implemented for a scripted command was
  ignored when retrying to execute a Lua script by falling back to `EVAL` after
  a `-NOSCRIPT` error (ISSUE #94).

- __FIX__: when subclassing `Predis\Client` the `getClientFor()` method returns
  a new instance of the subclass instead of a new instance of `Predis\Client`.


v0.8.0 (2012-10-23)
================================================================================

- The default server profile for Redis is now `2.6`.

- Certain connection parameters have been renamed:

  - `connection_async` is now `async_connect`
  - `connection_timeout` is now `timeout`
  - `connection_persistent` is now `persistent`

- The `throw_errors` connection parameter has been removed and replaced by the
  new `exceptions` client option since exceptions on `-ERR` replies returned by
  Redis are not generated by connection classes anymore but instead are thrown
  by the client class and other abstractions such as pipeline contexts.

- Added smart support for redis-cluster (Redis v3.0) in addition to the usual
  cluster implementation that uses client-side sharding.

- Various namespaces and classes have been renamed to follow rules inspired by
  the Symfony2 naming conventions.

- The second argument of the constructor of `Predis\Client` does not accept
  strings or instances of `Predis\Profile\ServerProfileInterface` anymore.
  To specify a server profile you must explicitly set `profile` in the array
  of client options.

- `Predis\Command\ScriptedCommand` internally relies on `EVALSHA` instead of
  `EVAL` thus avoiding to send Lua scripts bodies on each request. The client
  automatically resends the command falling back to `EVAL` when Redis returns a
  `-NOSCRIPT` error. Automatic fallback to `EVAL` does not work with pipelines,
  inside a `MULTI / EXEC` context or with plain `EVALSHA` commands.

- Complex responses are no more parsed by connection classes as they must be
  processed by consumer classes using the handler associated to the issued
  command. This means that executing commands directly on connections only
  returns simple Redis types, but nothing changes when using `Predis\Client`
  or the provided abstractions for pipelines and transactions.

- Iterators for multi-bulk replies now skip the response parsing method of the
  command that generated the response and are passed directly to user code.
  Pipeline and transaction objects still consume automatically iterators.

- Cluster and replication connections now extend a new common interface,
  `Predis\Connection\AggregatedConnectionInterface`.

- `Predis\Connection\MasterSlaveReplication` now uses an external strategy
  class to handle the logic for checking readable / writable commands and Lua
  scripts.

- Command pipelines have been optimized for both speed and code cleanness, but
  at the cost of bringing a breaking change in the signature of the interface
  for pipeline executors.

- Added a new pipeline executor that sends commands wrapped in a MULTI / EXEC
  context to make the execution atomic: if a pipeline fails at a certain point
  then the whole pipeline is discarded.

- The key-hashing mechanism for commands is now handled externally and is no
  more a competence of each command class. This change is neeeded to support
  both client-side sharding and Redis cluster.

- `Predis\Options\Option` is now abstract, see `Predis\Option\AbstractOption`.


v0.7.3 (2012-06-01)
================================================================================

- New commands available in the Redis v2.6 profile (dev): `BITOP`, `BITCOUNT`.

- When the number of keys `Predis\Commands\ScriptedCommand` is negative, Predis
  will count from the end of the arguments list to calculate the actual number
  of keys that will be interpreted as elements for `KEYS` by the underlying
  `EVAL` command.

- __FIX__: `examples\CustomDistributionStrategy.php` had a mistyped constructor
  call and produced a bad distribution due to an error as pointed in ISSUE #63.
  This bug is limited to the above mentioned example and does not affect the
  classes implemented in the `Predis\Distribution` namespace.

- __FIX__: `Predis\Commands\ServerEvalSHA::getScriptHash()` was calculating the
  hash while it just needs to return the first argument of the command.

- __FIX__: `Predis\Autoloader` has been modified to allow cascading autoloaders
  for the `Predis` namespace.


v0.7.2 (2012-04-01)
================================================================================

- Added `2.6` in the server profiles aliases list for the upcoming Redis 2.6.
  `2.4` is still the default server profile. `dev` now targets Redis 2.8.

- Connection instances can be serialized and unserialized using `serialize()`
  and `unserialize()`. This is handy in certain scenarios such as client-side
  clustering or replication to lower the overhead of initializing a connection
  object with many sub-connections since unserializing them can be up to 5x
  times faster.

- Reworked the default autoloader to make it faster. It is also possible to
  prepend it in PHP's autoload stack.

- __FIX__: fixed parsing of the payload returned by `MONITOR` with Redis 2.6.


v0.7.1 (2011-12-27)
================================================================================

- The PEAR channel on PearHub has been deprecated in favour of `pear.nrk.io`.

- Miscellaneous minor fixes.

- Added transparent support for master / slave replication configurations where
  write operations are performed on the master server and read operations are
  routed to one of the slaves. Please refer to ISSUE #21 for a bit of history
  and more details about replication support in Predis.

- The `profile` client option now accepts a callable object used to initialize
  a new instance of `Predis\Profiles\IServerProfile`.

- Exposed a method for MULTI / EXEC contexts that adds the ability to execute
  instances of Redis commands against transaction objects.


v0.7.0 (2011-12-11)
================================================================================

- Predis now adheres to the PSR-0 standard which means that there is no more a
  single file holding all the classes of the library, but multiple files (one
  for each class). You can use any PSR-0 compatible autoloader to load Predis
  or just leverage the default one shipped with the library by requiring the
  `Predis/Autoloader.php` and call `Predis\Autoloader::register()`.

- The default server profile for Redis is now 2.4. The `dev` profile supports
  all the features of Redis 2.6 (currently unstable) such as Lua scripting.

- Support for long aliases (method names) for Redis commands has been dropped.

- Redis 1.0 is no more supported. From now on Predis will use only the unified
  protocol to serialize commands.

- It is possible to prefix keys transparently on a client-level basis with the
  new `prefix` client option.

- An external connection factory is used to initialize new connection instances
  and developers can now register their own connection classes using the new
  `connections` client option.

- It is possible to connect locally to Redis using UNIX domain sockets. Just
  use `unix:///path/to/redis.sock` or a named array just like in the following
  example: `array('scheme' => 'unix', 'path' => '/path/to/redis.sock');`.

- If the `phpiredis` extension is loaded by PHP, it is now possible to use an
  alternative connection class that leverages it to make Predis faster on many
  cases, especially when dealing with big multibulk replies, with the the only
  downside that persistent connections are not supported. Please refer to the
  documentation to see how to activate this class using the new `connections`
  client option.

- Predis is capable to talk with Webdis, albeit with some limitations such as
  the lack of pipelining and transactions, just by using the `http` scheme in
  in the connection parameters. All is needed is PHP with the `curl` and the
  `phpiredis` extensions loaded.

- Way too many changes in the public API to make a list here, we just tried to
  make all the Redis commands compatible with previous releases of v0.6 so that
  you do not have to worry if you are simply using Predis as a client. Probably
  the only breaking changes that should be mentioned here are:

  - `throw_on_error` has been renamed to `throw_errors` and it is a connection
    parameter instead of a client option, along with `iterable_multibulk`.

  - `key_distribution` has been removed from the client options. To customize
    the distribution strategy you must provide a callable object to the new
    `cluster` client option to configure and then return a new instance of
    `Predis\Network\IConnectionCluster`.

  - `Predis\Client::create()` has been removed. Just use the constructor to set
    up a new instance of `Predis\Client`.

  - `Predis\Client::pipelineSafe()` was deprecated in Predis v0.6.1 and now has
    finally removed. Use `Predis\Client::pipeline(array('safe' => true))`.

  - `Predis\Client::rawCommand()` has been removed due to inconsistencies with
    the underlying connection abstractions. You can still get the raw resource
    out of a connection with `Predis\Network\IConnectionSingle::getResource()`
    so that you can talk directly with Redis.

- The `Predis\MultiBulkCommand` class has been merged into `Predis\Command` and
  thus removed. Serialization of commands is now a competence of connections.

- The `Predis\IConnection` interface has been splitted into two new interfaces:
  `Predis\Network\IConnectionSingle` and `Predis\Network\IConnectionCluster`.

- The constructor of `Predis\Client` now accepts more type of arguments such as
  instances of `Predis\IConnectionParameters` and `Predis\Network\IConnection`.


v0.6.6 (2011-04-01)
================================================================================

- Switched to Redis 2.2 as the default server profile (there are no changes
  that would break compatibility with previous releases). Long command names
  are no more supported by default but if you need them you can still require
  `Predis_Compatibility.php` to avoid breaking compatibility.

- Added a `VERSION` constant to `Predis\Client`.

- Some performance improvements for multibulk replies (parsing them is about
  16% faster than the previous version). A few core classes have been heavily
  optimized to reduce overhead when creating new instances.

- Predis now uses by default a new protocol reader, more lightweight and
  faster than the default handler-based one. Users can revert to the old
  protocol reader with the `reader` client option set to `composable`.
  This client option can also accept custom reader classes implementing the
  new `Predis\IResponseReader` interface.

- Added support for connecting to Redis using UNIX domain sockets (ISSUE #25).

- The `read_write_timeout` connection parameter can now be set to 0 or false
  to disable read and write timeouts on connections. The old behaviour of -1
  is still intact.

- `ZUNIONSTORE` and `ZINTERSTORE` can accept an array to specify a list of the
  source keys to be used to populate the destination key.

- `MGET`, `SINTER`, `SUNION` and `SDIFF` can accept an array to specify a list
  of keys. `SINTERSTORE`, `SUNIONSTORE` and `SDIFFSTORE` can also accept an
  array to specify the list of source keys.

- `SUBSCRIBE` and `PSUBSCRIBE` can accept a list of channels for subscription.

- __FIX__: some client-side clean-ups for `MULTI/EXEC` were handled incorrectly
  in a couple of corner cases (ISSUE #27).


v0.6.5 (2011-02-12)
================================================================================

- __FIX__: due to an untested internal change introduced in v0.6.4, a wrong
  handling of bulk reads of zero-length values was producing protocol
  desynchronization errors (ISSUE #20).


v0.6.4 (2011-02-12)
================================================================================

- Various performance improvements (15% ~ 25%) especially when dealing with
  long multibulk replies or when using clustered connections.

- Added the `on_retry` option to `Predis\MultiExecBlock` that can be used to
  specify an external callback (or any callable object) that gets invoked
  whenever a transaction is aborted by the server.

- Added inline (p)subscribtion via options when initializing an instance of
  `Predis\PubSubContext`.


v0.6.3 (2011-01-01)
================================================================================

- New commands available in the Redis v2.2 profile (dev):
  - Strings: `SETRANGE`, `GETRANGE`, `SETBIT`, `GETBIT`
  - Lists  : `BRPOPLPUSH`

- The abstraction for `MULTI/EXEC` transactions has been dramatically improved
  by providing support for check-and-set (CAS) operations when using Redis >=
  2.2. Aborted transactions can also be optionally replayed in automatic up
  to a user-defined number of times, after which a `Predis\AbortedMultiExec`
  exception is thrown.


v0.6.2 (2010-11-28)
================================================================================

- Minor internal improvements and clean ups.

- New commands available in the Redis v2.2 profile (dev):
  - Strings: `STRLEN`
  - Lists  : `LINSERT`, `RPUSHX`, `LPUSHX`
  - ZSets  : `ZREVRANGEBYSCORE`
  - Misc.  : `PERSIST`

- WATCH also accepts a single array parameter with the keys that should be
  monitored during a transaction.

- Improved the behaviour of `Predis\MultiExecBlock` in certain corner cases.

- Improved parameters checking for the SORT command.

- __FIX__: the `STORE` parameter for the `SORT` command didn't work correctly
  when using `0` as the target key (ISSUE #13).

- __FIX__: the methods for `UNWATCH` and `DISCARD` do not break anymore method
  chaining with `Predis\MultiExecBlock`.


v0.6.1 (2010-07-11)
================================================================================

- Minor internal improvements and clean ups.

- New commands available in the Redis v2.2 profile (dev):
  - Misc.  : `WATCH`, `UNWATCH`

- Optional modifiers for `ZRANGE`, `ZREVRANGE` and `ZRANGEBYSCORE` queries are
  supported using an associative array passed as the last argument of their
  respective methods.

- The `LIMIT` modifier for `ZRANGEBYSCORE` can be specified using either:
  - an indexed array: `array($offset, $count)`
  - an associative array: `array('offset' => $offset, 'count' => $count)`

- The method `Predis\Client::__construct()` now accepts also instances of
  `Predis\ConnectionParameters`.

- `Predis\MultiExecBlock` and `Predis\PubSubContext` now throw an exception
  when trying to create their instances using a profile that does not
  support the required Redis commands or when the client is connected to
  a cluster of connections.

- Various improvements to `Predis\MultiExecBlock`:
  - fixes and more consistent behaviour across various usage cases.
  - support for `WATCH` and `UNWATCH` when using the current development
    profile (Redis v2.2) and aborted transactions.

- New signature for `Predis\Client::multiExec()` which is now able to accept
  an array of options for the underlying instance of `Predis\MultiExecBlock`.
  Backwards compatibility with previous releases of Predis is ensured.

- New signature for `Predis\Client::pipeline()` which is now able to accept
  an array of options for the underlying instance of Predis\CommandPipeline.
  Backwards compatibility with previous releases of Predis is ensured.
  The method `Predis\Client::pipelineSafe()` is to be considered deprecated.

- __FIX__: The `WEIGHT` modifier for `ZUNIONSTORE` and `ZINTERSTORE` was
  handled incorrectly with more than two weights specified.


v0.6.0 (2010-05-24)
================================================================================

- Switched to the new multi-bulk request protocol for all of the commands
  in the Redis 1.2 and Redis 2.0 profiles. Inline and bulk requests are now
  deprecated as they will be removed in future releases of Redis.

- The default server profile is `2.0` (targeting Redis 2.0.x). If you are
  using older versions of Redis, it is highly recommended that you specify
  which server profile the client should use (e.g. `1.2` when connecting
  to instances of Redis 1.2.x).

- Support for Redis 1.0 is now optional and it is provided by requiring
  'Predis_Compatibility.php' before creating an instance of `Predis\Client`.

- New commands added to the Redis 2.0 profile since Predis 0.5.1:
  - Strings: `SETEX`, `APPEND`, `SUBSTR`
  - ZSets  : `ZCOUNT`, `ZRANK`, `ZUNIONSTORE`, `ZINTERSTORE`, `ZREMBYRANK`,
             `ZREVRANK`
  - Hashes : `HSET`, `HSETNX`, `HMSET`, `HINCRBY`, `HGET`, `HMGET`, `HDEL`,
             `HEXISTS`, `HLEN`, `HKEYS`, `HVALS`, `HGETALL`
  - PubSub : `PUBLISH`, `SUBSCRIBE`, `UNSUBSCRIBE`
  - Misc.  : `DISCARD`, `CONFIG`

- Introduced client-level options with the new `Predis\ClientOptions` class.
  Options can be passed to the constructor of `Predis\Client` in its second
  argument as an array or an instance of `Predis\ClientOptions`. For brevity's
  sake and compatibility with older versions, the constructor still accepts
  an instance of `Predis\RedisServerProfile` in its second argument. The
  currently supported client options are:

  - `profile` [default: `2.0` as of Predis 0.6.0]: specifies which server
    profile to use when connecting to Redis. This option accepts an instance
    of `Predis\RedisServerProfile` or a string that indicates the version.

  - `key_distribution` [default: `Predis\Distribution\HashRing`]: specifies
    which key distribution strategy to use to distribute keys among the
    servers that compose a cluster. This option accepts an instance of
    `Predis\Distribution\IDistributionStrategy` so that users can implement
    their own key distribution strategy. `Predis\Distribution\KetamaPureRing`
    is an alternative distribution strategy providing a pure-PHP implementation
    of the same algorithm used by libketama.

  - `throw_on_error` [default: `TRUE`]: server errors can optionally be handled
    "silently": instead of throwing an exception, the client returns an error
    response type.

  - `iterable_multibulk` [EXPERIMENTAL - default: `FALSE`]: in addition to the
    classic way of fetching a whole multibulk reply into an array, the client
    can now optionally stream a multibulk reply down to the user code by using
    PHP iterators. It is just a little bit slower, but it can save a lot of
    memory in certain scenarios.

- New parameters for connections:

  - `alias` [default: not set]: every connection can now be identified by an
    alias that is useful to get a specific connections when connected to a
    cluster of Redis servers.
  - `weight` [default: not set]: allows to balance keys asymmetrically across
    multiple servers. This is useful when you have servers with different
    amounts of memory to distribute the load of your keys accordingly.
  - `connection_async` [default: `FALSE`]: estabilish connections to servers
    in a non-blocking way, so that the client is not blocked while the socket
    resource performs the actual connection.
  - `connection_persistent` [default: `FALSE`]: the underlying socket resource
    is left open when a script ends its lifecycle. Persistent connections can
    lead to unpredictable or strange behaviours, so they should be used with
    extreme care.

- Introduced the `Predis\Pipeline\IPipelineExecutor` interface. Classes that
  implements this interface are used internally by the `Predis\CommandPipeline`
  class to change the behaviour of the pipeline when writing/reading commands
  from one or multiple servers. Here is the list of the default executors:

  - `Predis\Pipeline\StandardExecutor`: exceptions generated by server errors
    might be thrown depending on the options passed to the client (see the
    `throw_on_error` client option). Instead, protocol or network errors always
    throw exceptions. This is the default executor for single and clustered
    connections and shares the same behaviour of Predis 0.5.x.
  - `Predis\Pipeline\SafeExecutor`: exceptions generated by server, protocol
    or network errors are not thrown but returned in the response array as
    instances of `Predis\ResponseError` or `Predis\CommunicationException`.
  - `Predis\Pipeline\SafeClusterExecutor`: this executor shares the same
    behaviour of `Predis\Pipeline\SafeExecutor` but it is geared towards
    clustered connections.

- Support for PUB/SUB is handled by the new `Predis\PubSubContext` class, which
  could also be used to build a callback dispatcher for PUB/SUB scenarios.

- When connected to a cluster of connections, it is now possible to get a
  new `Predis\Client` instance for a single connection of the cluster by
  passing its alias/index to the new `Predis\Client::getClientFor()` method.

- `Predis\CommandPipeline` and `Predis\MultiExecBlock` return their instances
  when invokink commands, thus allowing method chaining in pipelines and
  multi-exec blocks.

- `Predis\MultiExecBlock` can handle the new `DISCARD` command.

- Connections now support float values for the `connection_timeout` parameter
  to express timeouts with a microsecond resolution.

- __FIX__: TCP connections now respect the read/write timeout parameter when
  reading the payload of server responses. Previously, `stream_get_contents()`
  was being used internally to read data from a connection but it looks like
  PHP does not honour the specified timeout for socket streams when inside
  this function.

- __FIX__: The `GET` parameter for the `SORT` command now accepts also multiple
  key patterns by passing an array of strings. (ISSUE #1).

* __FIX__: Replies to the `DEL` command return the number of elements deleted
  by the server and not 0 or 1 interpreted as a boolean response. (ISSUE #4).


v0.5.1 (2010-01-23)
================================================================================

* `RPOPLPUSH` has been changed from bulk command to inline command in Redis
  1.2.1, so `ListPopLastPushHead` now extends `InlineCommand`. The old behavior
  is still available via the `ListPopLastPushHeadBulk` class so that you can
  override the server profile if you need the old (and uncorrect) behaviour
  when connecting to a Redis 1.2.0 instance.

* Added missing support for `BGREWRITEAOF` for Redis >= 1.2.0.

* Implemented a factory method for the `RedisServerProfile` class to ease the
  creation of new server profile instances based on a version string.


v0.5.0 (2010-01-09)
================================================================================
* First versioned release of Predis
