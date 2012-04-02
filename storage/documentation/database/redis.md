# Redis

## Contents

- [The Basics](#the-basics)
- [Configuration](#config)
- [Usage](#usage)

<a name="the-basics"></a>
## The Basics

[Redis](http://redis.io) is an open source, advanced key-value store. It is often referred to as a data structure server since keys can contain [strings](http://redis.io/topics/data-types#strings), [hashes](http://redis.io/topics/data-types#hashes), [lists](http://redis.io/topics/data-types#lists), [sets](http://redis.io/topics/data-types#sets), and [sorted sets](http://redis.io/topics/data-types#sorted-sets).

<a name="config"></a>
## Configuration

The Redis configuration for your application lives in the **application/config/database.php** file. Within this file, you will see a **redis** array containing the Redis servers used by your application:

	'redis' => array(

		'default' => array('host' => '127.0.0.1', 'port' => 6379),

	),

The default server configuration should suffice for development. However, you are free to modify this array based on your environment. Simply give each Redis server a name, and specify the host and port used by the server.

<a name="usage"></a>
## Usage

You may get a Redis instance by calling the **db** method on the **Redis** class:

	$redis = Redis::db();

This will give you an instance of the **default** Redis server. You may pass the server name to the **db** method to get a specific server as defined in your Redis configuration:

	$redis = Redis::db('redis_2');

Great! Now that we have an instance of the Redis client, we may issue any of the [Redis commands](http://redis.io/commands) to the instance. Laravel uses magic methods to pass the commands to the Redis server:

	$redis->set('name', 'Taylor');

	$name = $redis->get('name');

	$values = $redis->lrange('names', 5, 10);

Notice the arguments to the comment are simply passed into the magic method. Of course, you are not required to use the magic methods, you may also pass commands to the server using the **run** method:

	$values = $redis->run('lrange', array(5, 10));

Just want to execute commands on the default Redis server? You can just use static magic methods on the Redis class:

	Redis::set('name', 'Taylor');

	$name = Redis::get('name');

	$values = Redis::lrange('names', 5, 10);

> **Note:** Redis [cache](/docs/cache/config#redis) and [session](/docs/session/config#redis) drivers are included with Laravel.