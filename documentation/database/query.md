## Fluent Query Builder

- [Retrieving Records](#get)
- [Building Where Clauses](#where)
- [Dynamic Where Clauses](#dynamic)
- [Table Joins](#joins)
- [Ordering Results](#ordering)
- [Skip & Take](#limit)
- [Aggregates](#aggregates)
- [Inserting Records](#insert)
- [Updating Records](#update)
- [Deleting Records](#delete)

Laravel provides an awesome, easy-to-use fluent interface for building SQL queries and working with your database. All queries use prepared statements and are protected against SQL injection. Working with your database doesn't have to be a headache.

You can begin a fluent query using the **table** method on the DB class. Just mention the table you wish to query:

	$query = DB::table('users');

You now have a fluent query builder for the "users" table. Using this query builder, you can retrieve, insert, update, or delete records from the table.

<a name="get"></a>
### Retrieving Records

There are two methods available for retrieving records using a fluent query: **get** and **first**. The **get** method will return an array of records from your database. Each record will be an object with properties corresponding to the columns of the table:

	$users = DB::table('users')->get();

	foreach ($users as $user)
	{
	     echo $user->email;
	}

Instead of returning an array, the **first** method will return a single object:

	$user = DB::table('users')->first();

	echo $user->email;

It's easy to limit the columns returned by your query. Simply pass an array of columns you want into the **get** or **first** method:

	$user = DB::table('users')->get(array('id', 'email as user_email'));

Need to get distinct records from the database? It's easy. Call the **distinct** method before retrieving your records:

	$user = DB::table('users')->distinct()->get();

> **Note:** If no results are found, the **first** method will return NULL. The **get** method will return an empty array.

<a name="where"></a>
### Building Where Clauses

#### where and or\_where

Building WHERE clauses in Laravel is painless. There are a variety of methods to assist you. The most basic of these methods are the **where** and **or_where** methods. Here is how to use them:

	return DB::table('users')
	      				->where('id', '=', 1)
	      				->or_where('email', '=', 'example@gmail.com')
	      				->first();

Of course, you are not limited to simply checking equality. You may also use **greater-than**, **less-than**, **not-equal**, and **like**:

	return DB::table('users')
	      				->where('id', '>', 1)
	      				->or_where('name', 'LIKE', '%Taylor%')
	      				->first();

You may have assumed that the **where** method will add to the query using an AND condition, while the **or_where** method will use an OR condition. You assumed correctly.

#### where\_in, where\_not\_in, or\_where\_in, and or\_where\_not\_in

The suite of **where_in** methods allows you to easily construct queries that search an array of values:

	DB::table('users')->where_in('id', array(1, 2, 3))->get();

	DB::table('users')->where_not_in('id', array(1, 2, 3))->get();

	DB::table('users')
	     ->where('email', '=', 'example@gmail.com')
	     ->or_where_in('id', array(1, 2, 3))
	     ->get();

	DB::table('users')
	     ->where('email', '=', 'example@gmail.com')
	     ->or_where_not_in('id', array(1, 2, 3))
	     ->get();

#### where\_null, where\_not\_null, or\_where\_null, and or\_where\_not\_null

The suite of **where_null** methods makes checking for NULL values a piece of cake:

	return DB::table('users')->where_null('updated_at')->get();

	return DB::table('users')->where_not_null('updated_at')->get();

	return DB::table('users')
	     			->where('email', '=', 'example@gmail.com')
	     			->or_where_null('updated_at')
	     			->get();

	return DB::table('users')
	     			->where('email', '=', 'example@gmail.com')
	     			->or_where_not_null('updated_at')
	     			->get();

<a name="dynamic"></a>
### Dynamic Where Clauses

Ready for some really beautiful syntax? Check out **dynamic where methods**:

	$user = DB::table('users')->where_email('example@gmail.com')->first();

	$user = DB::table('users')->where_email_and_password('example@gmail.com', 'secret');

	$user = DB::table('users')->where_id_or_name(1, 'Fred');

Aren't they a breathe of fresh air?

<a name="joins"></a>
### Table Joins

Need to join to another table? Try the **join** and **left\_join** methods:

	DB::table('users')
				->join('phone', 'users.id', '=', 'phone.user_id')
				->get(array('users.email', 'phone.number'));

The **table** you wish to join is passed as the first parameter. The remaining three parameters are used to construct the **ON** clause of the join.

Once you know how to use the join method, you know how to **left_join**. The method signatures are the same:

	DB::table('users')
				->left_join('phone', 'users.id', '=', 'phone.user_id')
				->get(array('users.email', 'phone.number'));

<a name="ordering"></a>
### Ordering Results

You can easily order the results of your query using the **order_by** method. Simply mention the column and direction (desc or asc) of the sort:

	return DB::table('users')->order_by('email', 'desc')->get();

Of course, you may sort on as many columns as you wish:

	return DB::table('users')
	     				->order_by('email', 'desc')
	     				->order_by('name', 'asc')
	     				->get();

<a name="limit"></a>
### Skip & Take

If you would like to **LIMIT** the number of results returned by your query, you can use the **take** method:

	return DB::table('users')->take(10)->get();

To set the **OFFSET** of your query, use the **skip** method:

	return DB::table('users')->skip(10)->get();

<a name="aggregates"></a>
### Aggregates

Need to get a **MIN**, **MAX**, **AVG**, **SUM**, or **COUNT** value? Just pass the column to the query:

	$min = DB::table('users')->min('age');

	$max = DB::table('users')->max('weight');

	$avg = DB::table('users')->avg('salary');

	$sum = DB::table('users')->sum('votes');

	$count = DB::table('users')->count();

Of course, you may wish to limit the query using a WHERE clause first:

	$count = DB::table('users')->where('id', '>', 10)->count();

<a name="insert"></a>
### Inserting Records

Inserting records is amazingly easy using the **insert** method. The method only expects an array of values to insert. It couldn't be simpler. The insert method will simply return true or false, indicating whether the query was successful:

	DB::table('users')->insert(array('email' => 'example@gmail.com'));

Inserting a record that has an auto-incrementing ID? You can use the **insert\_get\_id** method to insert a record and retrieve the ID:

	$id = DB::table('users')->insert_get_id(array('email' => 'example@gmail.com'));

> **Note:** The **insert\_get\_id** method expects the name of the auto-incrementing column to be "id".

<a name="update"></a>
### Updating Records

Updating records is just as simple as inserting them. Simply pass an array of values to the **update** method:

	$affected = DB::table('users')->update(array('email' => 'new_email@gmail.com'));

Of course, when you only want to update a few records, you should add a WHERE clause before calling the update method:

	$affected = DB::table('users')
						->where('id', '=', 1)
						->update(array('email' => 'new_email@gmail.com'));

<a name="delete"></a>
### Deleting Records

When you want to delete records from your database, simply call the **delete** method:

	$affected = DB::table('users')->where('id', '=', 1)->delete();

Want to quickly delete a record by its ID? No problem. Just pass the ID into the delete method:

	$affected = DB::table('users')->delete(1);