## Session Usage

- [Storing Items](#put)
- [Retrieving Items](#get)
- [Removing Items](#forget)
- [Regeneration](#regeneration)

<a name="put"></a>
### Storing Items

Storing items in the session is a breeze. Simply call the put method on the Session class:

	Session::put('name', 'Taylor');

The first parameter is the **key** to the session item. You will use this key to retrieve the item from the session. The second parameter is the **value** of the item.

Need to store an item in the session that should expire after the next request? Check out the **flash** method. It provides an easy way to store temporary data like status or error messages:

	Session::flash('status', 'Welcome Back!');

<a name="get"></a>
### Retrieving Items

Retrieving items from the session is no problem. You can use the **get** method on the Session class to retrieve any item in the session, including flash data. Just pass the key of the item you wish to retrieve:

	$name = Session::get('name');

By default, NULL will be returned if the session item does not exist. However, you may pass a default value as a second parameter to the get method:

	$name = Session::get('name', 'Fred');

	$name = Session::get('name', function() {return 'Fred';});

Now, "Fred" will be returned if the "name" item does not exist in the session.

Laravel even provides a simple way to determine if a session item exists using the **has** method:

	if (Session::has('name'))
	{
	     $name = Session::get('name');
	}

<a name="forget"></a>
### Removing Items

Need to get rid of a session item? No problem. Just mention the name of the item to the **forget** method on the Session class:

	Session::forget('name');

You can even remove all of the items from the session using the **flush** method:

	Session::flush();

<a name="regeneration"></a>
### Regeneration

Sometimes you may want to "regenerate" the session ID. This simply means that a new, random session ID will be assigned to the session. Here's how to do it:

	Session::regenerate();