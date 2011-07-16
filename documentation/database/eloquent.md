## Eloquent ORM

- [Conventions](#conventions)
- [Retrieving Models](#get)
- [Aggregates](#aggregates)
- [Inserting & Updating Models](#save)
- [Relationships](#relationships)
- [Eager Loading](#eager)

An ORM is an [object-relational mapper](http://en.wikipedia.org/wiki/Object-relational_mapping), and Laravel has one that you will absolutely love to use. It is named "Eloquent" because it allows you to work with your database objects and relationships using an eloquent and expressive syntax. In general, you will define one Eloquent model for each table in your database. To get started, let's define a simple model:

	class User extends Eloquent {}

Nice! Notice that our model extends the **Eloquent** class. This class will provide all of the functionality you need to start working eloquently with your database.

> **Note:** Typically, Eloquent models live in the **application/models** directory.

<a name="conventions"></a>
### Conventions

Eloquent makes a few basic assumptions about your database structure:

- Each table should have a primary key named **id**.
- Each table name should be the plural form of its corresponding model name.

Sometimes you may wish to use a table name other than the plural form of your model. No problem. Just add a static **table** property your model:

	class User extends Eloquent {

	     public static $table = 'my_users';

	}

<a name="get"></a>
### Retrieving Models

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

Of course, retrieving an entire table isn't very helpful. Thankfully, **every method that is available through the fluent query builder is available in Eloquent**. Just begin querying your model with a static call to one of the [query builder](/docs/database/query) methods, and execute the query using the **get** or **first** method. The get method will return an array of models, while the first method will return a single model:

	$user = User::where('email', '=', $email)->first();

	$user = User::where_email($email)->first();

	$users = User::where_in('id', array(1, 2, 3))->or_where('email', '=', $email)->get();

	$users = User::order_by('votes', 'desc')->take(10)->get();

> **Note:** If no results are found, the **first** method will return NULL. The **all** and **get** methods return an empty array.

<a name="aggregates"></a>
### Aggregates

Need to get a **MIN**, **MAX**, **AVG**, **SUM**, or **COUNT** value? Just pass the column to the appropriate method:

	$min = User::min('id');

	$max = User::max('id');

	$avg = User::avg('id');

	$sum = User::sum('id');

	$count = User::count();

Of course, you may wish to limit the query using a WHERE clause first:

	$count = User::where('id', '>', 10)->count();

<a name="save"></a>
### Inserting & Updating Models

Inserting Eloquent models into your tables couldn't be easier. First, instantiate a new model. Second, set its properties. Third, call the **save** method:

	$user = new User;

	$user->email = 'example@gmail.com';
	$user->password = 'secret';

	$user->save();

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
### Relationships

Unless you're doing it wrong, your database tables are probably related to one another. For instance, an order may belong to a user. Or, a post may have many comments. Eloquent makes defining relationships and retrieving related models simple and intuitive. Laravel supports three types of relationships:

- [One-To-One](#one-to-one)
- [One-To-Many](#one-to-many)
- [Many-To-Many](#many-to-many)

To define a relationship on an Eloquent model, you simply create a method that returns the result of either the **has\_one**, **has\_many**, **belongs\_to**, or **has\_and\_belongs\_to\_many** method. Let's examine each one in detail.

<a name="one-to-one"></a>
#### One-To-One

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
#### One-To-Many

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
#### Many-To-Many

Many-to-many relationships are the most complicated of the three relationships. But don't worry, you can do this. For example, assume a User has many Roles, but a Role can also belong to many Users. Three database tables must be created to accomplish this relationship: a **users** table, a **roles** table, and a **roles_users** table. The structure for each table looks like this:

**Users:**

	id    - INTEGER
	email - VARCHAR

**Roles:**

	id   - INTEGER
	name - VARCHAR

**Roles_Users:**

	user_id - INTEGER
	role_id - INTEGER

Now you're ready to define the relationship on your models using the **has\_and\_belongs\_to\_many** method:

	class User extends Eloquent {

	     public function roles()
	     {
	          return $this->has_and_belongs_to_many('Role');
	     }

	}

Great! Now it's time to retrieve a user's roles:

	$roles = User::find(1)->roles()->get();

Or, as usual, you may retrieve the relationship through the dynamic roles property:

	$roles = User::find(1)->roles;

As you may have noticed, the default name of the intermediate table is the plural names of the two related models arranged alphabetically and concatenated by an underscore. However, you are free to specify your own table name. Simply pass the table name in the second parameter to the **has\_and\_belongs\_to\_many** method:

	class User extends Eloquent {

	     public function roles()
	     {
	          return $this->has_and_belongs_to_many('Role', 'user_roles');
	     }

	}

<a name="eager"></a>
### Eager Loading

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

	$books = Book::with('author', 'publisher')->get();

> **Note:** When eager loading, the call to the static **with** method must always be at the beginning of the query.