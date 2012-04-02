# Cache Configuration

## Contents

- [The Basics](#the-basics)
- [Database](#database)
- [Memcached](#memcached)
- [Redis](#redis)
- [Cache Keys](#keys)
- [In-Memory Cache](#memory)

<a name="the-basics"></a>
## The Basics

Imagine your application displays the ten most popular songs as voted on by your users. Do you really need to look up these ten songs every time someone visits your site? What if you could store them for 10 minutes, or even an hour, allowing you to dramatically speed up your application? Laravel's caching makes it simple.

Laravel provides five cache drivers out of the box:

- File System
- Database
- Memcached
- APC
- Redis
- Memory (Arrays)

By default, Laravel is configured to use the **file** system cache driver. It's ready to go out of the box with no configuration. The file system driver stores cached items as files in the **cache** directory. If you're satisfied with this driver, no other configuration is required. You're ready to start using it.

> **Note:** Before using the file system cache driver, make sure your **storage/cache** directory is writeable.

<a name="database"></a>
## Database

The database cache driver uses a given database table as a simple key-value store. To get started, first set the name of the database table in **application/config/cache.php**:

	'database' => array('table' => 'laravel_cache'),

Next, create the table on your database. The table should have three columns:

- key (varchar)
- value (text)
- expiration (integer)

That's it. Once your configuration and table is setup, you're ready to start caching!

<a name="memcached"></a>
## Memcached

[Memcached](http://memcached.org) is an ultra-fast, open-source distributed memory object caching system used by sites such as Wikipedia and Facebook. Before using Laravel's Memcached driver, you will need to install and configure Memcached and the PHP Memcache extension on your server.

Once Memcached is installed on your server you must set the **driver** in the **application/config/cache.php** file:

	'driver' => 'memcached'

Then, add your Memcached servers to the **servers** array:

	'servers' => array(
	     array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
	)

<a name="redis"></a>
## Redis

[Redis](http://redis.io) is an open source, advanced key-value store. It is often referred to as a data structure server since keys can contain [strings](http://redis.io/topics/data-types#strings), [hashes](http://redis.io/topics/data-types#hashes), [lists](http://redis.io/topics/data-types#lists), [sets](http://redis.io/topics/data-types#sets), and [sorted sets](http://redis.io/topics/data-types#sorted-sets).

Before using the Redis cache driver, you must [configure your Redis servers](/docs/database/redis#config). Now you can just set the **driver** in the **application/config/cache.php** file:

	'driver' => 'redis'

<a name="keys"></a>
### Cache Keys

To avoid naming collisions with other applications using APC, Redis, or a Memcached server, Laravel prepends a **key** to each item stored in the cache using these drivers. Feel free to change this value:

	'key' => 'laravel'

<a name="memory"></a>
### In-Memory Cache

The "memory" cache driver does not actually cache anything to disk. It simply maintains an internal array of the cache data for the current request. This makes it perfect for unit testing your application in isolation from any storage mechanism. It should never be used as a "real" cache driver.