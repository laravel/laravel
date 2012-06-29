# Session Usage

## Contents

- [Storing Items](#put)
- [Retrieving Items](#get)
- [Removing Items](#forget)
- [Flashing Items](#flash)
- [Regeneration](#regeneration)

<a name="put"></a>
## Storing Items

To store items in the session call the put method on the Session class:

	Session::put('name', 'Taylor');

The first parameter is the **key** to the session item. You will use this key to retrieve the item from the session. The second parameter is the **value** of the item.

<a name="get"></a>
## Retrieving Items

You can use the **get** method on the Session class to retrieve any item in the session, including flash data. Just pass the key of the item you wish to retrieve:

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
## Removing Items

To remove an item from the session use the **forget** method on the Session class:

	Session::forget('name');

You can even remove all of the items from the session using the **flush** method:

	Session::flush();

<a name="flash"></a>
## Flashing Items

The **flash** method stores an item in the session that will expire after the next request. It's useful for storing temporary data like status or error messages:

	Session::flash('status', 'Welcome Back!');
	
Flash items that are expring in subsequent requests can be retained for another request by using one of the following methods:

Retain all items for another request:

	Session::reflash();
	
Retain an individual item for another request:
	
	Session::keep('status');
	
Retain several items for another request:
	
	Session::keep(array('status', 'other_item'));

<a name="regeneration"></a>
## Regeneration

Sometimes you may want to "regenerate" the session ID. This simply means that a new, random session ID will be assigned to the session. Here's how to do it:

	Session::regenerate();