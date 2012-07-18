# Eloquent ORM

## Contents

- [The Basics](#the-basics)
- [Conventions](#conventions)
- [Retrieving Models](#get)
- [Aggregates](#aggregates)
- [Inserting & Updating Models](#save)
- [Relationships](#relationships)
- [Inserting Related Models](#inserting-related-models)
- [Working With Intermediate Tables](#intermediate-tables)
- [Eager Loading](#eager)
- [Constraining Eager Loads](#constraining-eager-loads)
- [Setter & Getter Methods](#getter-and-setter-methods)
- [Mass-Assignment](#mass-assignment)
- [Converting Models To Arrays](#to-array)

<a name="the-basics"></a>
## The Basics

An ORM is an [object-relational mapper](http://en.wikipedia.org/wiki/Object-relational_mapping), and Laravel has one that you will absolutely love to use. It is named "Eloquent" because it allows you to work with your database objects and relationships using an eloquent and expressive syntax. In general, you will define one Eloquent model for each table in your database. To get started, let's define a simple model:

	class User extends Eloquent {}

Nice! Notice that our model extends the **Eloquent** class. This class will provide all of the functionality you need to start working eloquently with your database.

> **Note:** Typically, Eloquent models live in the **application/models** directory.

<a name="conventions"></a>
## Conventions

Eloquent makes a few basic assumptions about your database structure:

- Each table should have a primary key named **id**.
- Each table name should be the plural form of its corresponding model name.

Sometimes you may wish to use a table name other than the plural form of your model. No problem. Just add a static **table** property your model:

	class User extends Eloquent {

	     public static $table = 'my_users';

	}

<a name="get"></a>
## Retrieving Models

Retrieving models using Eloquent is refreshingly simple. The most basic way to retrieve an Eloquent model is the static **find** method. This method will return a single model by primary key with properties corresponding to each column on the table:

	$user = User::find(1);

	echo $user->email;

The find method will execute a query that looks something like this:

	SELECT * FROM "users" WHERE "id" = 1

Need to retrieve an entire table? Just use the static **all** method:

	$users = User::all();

	foreach ($users as $user)
	{
	     echo $user->email;
	}

Of course, retrieving an entire table isn't very helpful. Thankfully, **every method that is available through the fluent query builder is available in Eloquent**. Just begin querying your model with a static call to one of the [query builder](/docs/database/fluent) methods, and execute the query using the **get** or **first** method. The get method will return an array of models, while the first method will return a single model:

	$user = User::where('email', '=', $email)->first();

	$user = User::where_email($email)->first();

	$users = User::where_in('id', array(1, 2, 3))->or_where('email', '=', $email)->get();

	$users = User::order_by('votes', 'desc')->take(10)->get();

> **Note:** If no results are found, the **first** method will return NULL. The **all** and **get** methods return an empty array.

<a name="aggregates"></a>
## Aggregates

Need to get a **MIN**, **MAX**, **AVG**, **SUM**, or **COUNT** value? Just pass the column to the appropriate method:

	$min = User::min('id');

	$max = User::max('id');

	$avg = User::avg('id');

	$sum = User::sum('id');

	$count = User::count();

Of course, you may wish to limit the query using a WHERE clause first:

	$count = User::where('id', '>', 10)->count();

<a name="save"></a>
## Inserting & Updating Models

Inserting Eloquent models into your tables couldn't be easier. First, instantiate a new model. Second, set its properties. Third, call the **save** method:

	$user = new User;

	$user->email = 'example@gmail.com';
	$user->password = 'secret';

	$user->save();

Alternatively, you may use the **create** method, which will insert a new record into the database and return the model instance for the newly inserted record, or **false** if the insert failed.

	$user = User::create(array('email' => 'example@gmail.com'));

Updating models is just as simple. Instead of instantiating a new model, retrieve one from your database. Then, set its properties and save:

	$user = User::find(1);

	$user->email = 'new_email@gmail.com';
	$user->password = 'new_secret';

	$user->save();

Need to maintain creation and update timestamps on your database records? With Eloquent, you don't have to worry about it. Just add a static **timestamps** property to your model:

	class User extends Eloquent {

	     public static $timestamps = true;

	}

Next, add **created_at** and **updated_at** date columns to your table. Now, whenever you save the model, the creation and update timestamps will be set automatically. You're welcome.

> **Note:** You can change the default timezone of your application in the **application/config/application.php** file.

<a name="relationships"></a>
## Relationships

Unless you're doing it wrong, your database tables are probably related to one another. For instance, an order may belong to a user. Or, a post may have many comments. Eloquent makes defining relationships and retrieving related models simple and intuitive. Laravel supports three types of relationships:

- [One-To-One](#one-to-one)
- [One-To-Many](#one-to-many)
- [Many-To-Many](#many-to-many)

To define a relationship on an Eloquent model, you simply create a method that returns the result of either the **has\_one**, **has\_many**, **belongs\_to**, or **has\_many\_and\_belongs\_to** method. Let's examine each one in detail.

<a name="one-to-one"></a>
### One-To-One

A one-to-one relationship is the most basic form of relationship. For example, let's pretend a user has one phone. Simply describe this relationship to Eloquent:

	class User extends Eloquent {

	     public function phone()
	     {
	          return $this->has_one('Phone');
	     }

	}

Notice that the name of the related model is passed to the **has_one** method. You can now retrieve the phone of a user through the **phone** method:

	$phone = User::find(1)->phone()->first();

Let's examine the SQL performed by this statement. Two queries will be performed: one to retrieve the user and one to retrieve the user's phone:

	SELECT * FROM "users" WHERE "id" = 1

	SELECT * FROM "phones" WHERE "user_id" = 1

Note that Eloquent assumes the foreign key of the relationship will be **user\_id**. Most foreign keys will follow this **model\_id** convention; however, if you want to use a different column name as the foreign key, just pass it in the second parameter to the method:

	return $this->has_one('Phone', 'my_foreign_key');

Want to just retrieve the user's phone without calling the first method? No problem. Just use the **dynamic phone property**. Eloquent will automatically load the relationship for you, and is even smart enough to know whether to call the get (for one-to-many relationships) or first (for one-to-one relationships) method:

	$phone = User::find(1)->phone;

What if you need to retrieve a phone's user? Since the foreign key (**user\_id**) is on the phones table, we should describe this relationship using the **belongs\_to** method. It makes sense, right? Phones belong to users. When using the **belongs\_to** method, the name of the relationship method should correspond to the foreign key (sans the **\_id**). Since the foreign key is **user\_id**, your relationship method should be named **user**:

	class Phone extends Eloquent {

	     public function user()
	     {
	          return $this->belongs_to('User');
	     }

	}

Great! You can now access a User model through a Phone model using either your relationship method or dynamic property:

	echo Phone::find(1)->user()->first()->email;

	echo Phone::find(1)->user->email;

<a name="one-to-many"></a>
### One-To-Many

Assume a blog post has many comments. It's easy to define this relationship using the **has_many** method:

	class Post extends Eloquent {

	     public function comments()
	     {
	          return $this->has_many('Comment');
	     }

	}

Now, simply access the post comments through the relationship method or dynamic property:

	$comments = Post::find(1)->comments()->get();

	$comments = Post::find(1)->comments;

Both of these statements will execute the following SQL:

	SELECT * FROM "posts" WHERE "id" = 1

	SELECT * FROM "comments" WHERE "post_id" = 1

Want to join on a different foreign key? No problem. Just pass it in the second parameter to the method:

	return $this->has_many('Comment', 'my_foreign_key');

You may be wondering: _If the dynamic properties return the relationship and require less keystokes, why would I ever use the relationship methods?_ Actually, relationship methods are very powerful. They allow you to continue to chain query methods before retrieving the relationship. Check this out:

	echo Post::find(1)->comments()->order_by('votes', 'desc')->take(10)->get();

<a name="many-to-many"></a>
### Many-To-Many

Many-to-many relationships are the most complicated of the three relationships. But don't worry, you can do this. For example, assume a User has many Roles, but a Role can also belong to many Users. Three database tables must be created to accomplish this relationship: a **users** table, a **roles** table, and a **role_user** table. The structure for each table looks like this:

**Users:**

	id    - INTEGER
	email - VARCHAR

**Roles:**

	id   - INTEGER
	name - VARCHAR

**Roles_Users:**

    id      - INTEGER
	user_id - INTEGER
	role_id - INTEGER

Now you're ready to define the relationship on your models using the **has\_many\_and\_belongs\_to** method:

	class User extends Eloquent {

	     public function roles()
	     {
	          return $this->has_many_and_belongs_to('Role');
	     }

	}

Great! Now it's time to retrieve a user's roles:

	$roles = User::find(1)->roles()->get();

Or, as usual, you may retrieve the relationship through the dynamic roles property:

	$roles = User::find(1)->roles;

As you may have noticed, the default name of the intermediate table is the singular names of the two related models arranged alphabetically and concatenated by an underscore. However, you are free to specify your own table name. Simply pass the table name in the second parameter to the **has\_and\_belongs\_to\_many** method:

	class User extends Eloquent {

	     public function roles()
	     {
	          return $this->has_many_and_belongs_to('Role', 'user_roles');
	     }

	}

By default only certain fields from the pivot table will be returned (the two **id** fields, and the timestamps). If your pivot table contains additional columns, you can fetch them too by using the **with()** method :

	class User extends Eloquent {

	     public function roles()
	     {
	          return $this->has_many_and_belongs_to('Role', 'user_roles')->with('column');
	     }

	}

<a name="inserting-related-models"></a>
## Inserting Related Models

Let's assume you have a **Post** model that has many comments. Often you may want to insert a new comment for a given post. Instead of manually setting the **post_id** foreign key on your model, you may insert the new comment from it's owning Post model. Here's what it looks like:

	$comment = new Comment(array('message' => 'A new comment.'));

	$post = Post::find(1);

	$post->comments()->insert($comment);

When inserting related models through their parent model, the foreign key will automatically be set. So, in this case, the "post_id" was automatically set to "1" on the newly inserted comment.

<a name="has-many-save"></a>
When working with `has_many` relationships, you may use the `save` method to insert / update related models:

	$comments = array(
		array('message' => 'A new comment.'),
		array('message' => 'A second comment.'),
	);

	$post = Post::find(1);

	$post->comments()->save($comments);

### Inserting Related Models (Many-To-Many)

This is even more helpful when working with many-to-many relationships. For example, consider a **User** model that has many roles. Likewise, the **Role** model may have many users. So, the intermediate table for this relationship has "user_id" and "role_id" columns. Now, let's insert a new Role for a User:

	$role = new Role(array('title' => 'Admin'));

	$user = User::find(1);

	$user->roles()->insert($role);

Now, when the Role is inserted, not only is the Role inserted into the "roles" table, but a record in the intermediate table is also inserted for you. It couldn't be easier!

However, you may often only want to insert a new record into the intermediate table. For example, perhaps the role you wish to attach to the user already exists. Just use the attach method:

	$user->roles()->attach($role_id);

It's also possible to attach data for fields in the intermediate table (pivot table), to do this add a second array variable to the attach command containing the data you want to attach:
	
	$user->roles()->attach($role_id, array('expires' => $expires));

<a name="sync-method"></a>
Alternatively, you can use the `sync` method, which accepts an array of IDs to "sync" with the intermediate table. After this operation is complete, only the IDs in the array will be on the intermediate table.

	$user->roles()->sync(array(1, 2, 3));

<a name="intermediate-tables"></a>
## Working With Intermediate Tables

As your probably know, many-to-many relationships require the presence of an intermediate table. Eloquent makes it a breeze to maintain this table. For example, let's assume we have a **User** model that has many roles. And, likewise, a **Role** model that has many users. So the intermediate table has "user_id" and "role_id" columns. We can access the pivot table for the relationship like so:

	$user = User::find(1);

	$pivot = $user->roles()->pivot();

Once we have an instance of the pivot table, we can use it just like any other Eloquent model:

	foreach ($user->roles()->pivot()->get() as $row)
	{
		//
	}

You may also access the specific intermediate table row associated with a given record. For example:

	$user = User::find(1);

	foreach ($user->roles as $role)
	{
		echo $role->pivot->created_at;
	}

Notice that each related **Role** model we retrieved is automatically assigned a **pivot** attribute. This attribute contains a model representing the intermediate table record associated with that related model.

Sometimes you may wish to remove all of the record from the intermediate table for a given model relationship. For instance, perhaps you want to remove all of the assigned roles from a user. Here's how to do it:

	$user = User::find(1);

	$user->roles()->delete();

Note that this does not delete the roles from the "roles" table, but only removes the records from the intermediate table which associated the roles with the given user.

<a name="eager"></a>
## Eager Loading

Eager loading exists to alleviate the N + 1 query problem. Exactly what is this problem? Well, pretend each Book belongs to an Author. We would describe this relationship like so:

	class Book extends Eloquent {

	     public function author()
	     {
	          return $this->belongs_to('Author');
	     }

	}

Now, examine the following code:

	foreach (Book::all() as $book)
	{
	     echo $book->author->name;
	}

How many queries will be executed? Well, one query will be executed to retrieve all of the books from the table. However, another query will be required for each book to retrieve the author. To display the author name for 25 books would require **26 queries**. See how the queries can add up fast?

Thankfully, you can eager load the author models using the **with** method. Simply mention the **function name** of the relationship you wish to eager load:

	foreach (Book::with('author')->get() as $book)
	{
	     echo $book->author->name;
	}

In this example, **only two queries will be executed**!

	SELECT * FROM "books"

	SELECT * FROM "authors" WHERE "id" IN (1, 2, 3, 4, 5, ...)

Obviously, wise use of eager loading can dramatically increase the performance of your application. In the example above, eager loading cut the execution time in half.

Need to eager load more than one relationship? It's easy:

	$books = Book::with(array('author', 'publisher'))->get();

> **Note:** When eager loading, the call to the static **with** method must always be at the beginning of the query.

You may even eager load nested relationships. For example, let's assume our **Author** model has a "contacts" relationship. We can eager load both of the relationships from our Book model like so:

	$books = Book::with(array('author', 'author.contacts'))->get();

If you find yourself eager loading the same models often, you may want to use **$includes** in the model.

	class Book extends Eloquent {

	     public $includes = array('author');
	     
	     public function author()
	     {
	          return $this->belongs_to('Author');
	     }

	}
	
**$includes** takes the same arguments that **with** takes. The following is now eagerly loaded.

	foreach (Book::all() as $book)
	{
	     echo $book->author->name;
	}

> **Note:** Using **with** will override a models **$includes**.

<a name="constraining-eager-loads"></a>
## Constraining Eager Loads

Sometimes you may wish to eager load a relationship, but also specify a condition for the eager load. It's simple. Here's what it looks like:

	$users = User::with(array('posts' => function($query)
	{
		$query->where('title', 'like', '%first%');

	}))->get();

In this example, we're eager loading the posts for the users, but only if the post's "title" column contains the word "first".

<a name="getter-and-setter-methods"></a>
## Getter & Setter Methods

Setters allow you to handle attribute assignment with custom methods.  Define a setter by appending "set_" to the intended attribute's name.

	public function set_password($password)
	{
		$this->set_attribute('hashed_password', Hash::make($password));
	}

Call a setter method as a variable (without parenthesis) using the name of the method without the "set_" prefix.

	$this->password = "my new password";

Getters are very similar. They can be used to modify attributes before they're returned. Define a getter by appending "get_" to the intended attribute's name.

	public function get_published_date()
	{
		return date('M j, Y', $this->get_attribute('published_at'));
	}

Call the getter method as a variable (without parenthesis) using the name of the method without the "get_" prefix.

	echo $this->published_date;

<a name="mass-assignment"></a>
## Mass-Assignment

Mass-assignment is the practice of passing an associative array to a model method which then fills the model's attributes with the values from the array. Mass-assignment can be done by passing an array to the model's constructor:

	$user = new User(array(
		'username' => 'first last',
		'password' => 'disgaea'
	));

	$user->save();

Or, mass-assignment may be accomplished using the **fill** method.

	$user = new User;

	$user->fill(array(
		'username' => 'first last',
		'password' => 'disgaea'
	));

	$user->save();

By default, all attribute key/value pairs will be store during mass-assignment. However, it is possible to create a white-list of attributes that will be set. If the accessible attribute white-list is set then no attributes other than those specified will be set during mass-assignment.

You can specify accessible attributes by assigning the **$accessible** static array. Each element contains the name of a white-listed attribute.

	public static $accessible = array('email', 'password', 'name');

Alternatively, you may use the **accessible** method from your model:

	User::accessible(array('email', 'password', 'name'));

> **Note:** Utmost caution should be taken when mass-assigning using user-input. Technical oversights could cause serious security vulnerabilities.

<a name="to-array"></a>
## Converting Models To Arrays

When building JSON APIs, you will often need to convert your models to array so they can be easily serialized. It's really simple.

#### Convert a model to an array:

	return json_encode($user->to_array());

The `to_array` method will automatically grab all of the attributes on your model, as well as any loaded relationships.

Sometimes you may wish to limit the attributes that are included in your model's array, such as passwords. To do this, add a `hidden` attribute definition to your model:

#### Excluding attributes from the array:

	class User extends Eloquent {

		public static $hidden = array('password');

	}