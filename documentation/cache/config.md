## Cache Configuration

- [Memcached](#memcached)
- [Cache Keys](#keys)

Imagine your application displays the ten most popular songs as voted on by your users. Do you really need to look up these ten songs every time someone visits your site? What if you could store them for 10 minutes, or even an hour, allowing you to dramatically speed up your application? Caching makes it simple.

Laravel provides three wonderful cache drivers out of the box:

- File System
- Memcached
- APC

By default, Laravel is configured to use the **file** system cache driver. It's ready to go. The file system driver stores cached items as files in the **application/storage/cache** directory. If you're satisfied with this driver, no other configuration is required. You're ready to start using it.

> **Note:** Before using the file system cache driver, make sure your **application/storage/cache** directory is writeable.

<a name="memcached"></a>
### Memcached

[Memcached](http://memcached.org) is an ultra-fast, open-source distributed memory object caching system used by sites such as Wikipedia and Facebook. Before using Laravel's Memcached driver, you will need to install and configure Memcached and the PHP Memcache extension on your server.

Once Memcached is installed on your server, configuring the Laravel driver is a breeze. First, set the **driver** in the **application/config/cache.php** file:

	'driver' => 'memcached'

Next, add your Memcached servers to the **servers** array:

	'servers' => array(
	     array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
	)

<a name="keys"></a>
### Cache Keys

To avoid naming collisions with other applications using APC or a Memcached server, Laravel prepends a **key** to each item stored in the cache using these drivers. Feel free to change this value:

	'key' => 'laravel'