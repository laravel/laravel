## Cache Usage

- [Storing Items](#put)
- [Retrieving Items](#get)
- [Removing Items](#forget)

<a name="put"></a>
### Storing Items

Storing items in the cache is simple. Simply call the **put** method on the Cache class:

	Cache::put('name', 'Taylor', 10);

The first parameter is the **key** to the cache item. You will use this key to retrieve the item from the cache. The second parameter is the **value** of the item. The third parameter is the number of **minutes** you want the item to be cached.

> **Note:** It is not necessary to serialize objects when storing them in the cache.

<a name="get"></a>
### Retrieving Items

Retrieving items from the cache is even more simple than storing them. It is done using the **get** method. Just mention the key of the item you wish to retrieve:

	$name = Cache::get('name');

By default, NULL will be returned if the cached item has expired or does not exist. However, you may pass a different default value as a second parameter to the method:

	$name = Cache::get('name', 'Fred');

Now, "Fred" will be returned if the "name" cache item has expired or does not exist.

What if you need a value from your database if a cache item doesn't exist? The solution is simple. You can pass a closure into the **get** method as a default value. The closure will only be executed if the cached item doesn't exist:

	$users = Cache::get('count', function() {return DB::table('users')->count();});

Let's take this example a step further. Imagine you want to retrieve the number of registered users for your application; however, if the value is not cached, you want to store the default value in the cache. It's a breeze using the **remember** method:

	$users = Cache::remember('count', function() {return DB::table('users')->count();}, 5);

Let's talk through that example. If the **count** item exists in the cache, it will be returned. If it doesn't exist, the result of the closure will be stored in the cache for five minutes **and** be returned by the method. Slick, huh?

Laravel even gives you a simple way to determine if a cached item exists using the **has** method:

	if (Cache::has('name'))
	{
	     $name = Cache::get('name');
	}

<a name="forget"></a>
### Removing Items

Need to get rid of a cached item? No problem. Just mention the name of the item to the **forget** method:

	Cache::forget('name');