## Database Configuration

- [Quick Start Using SQLite](#quick)
- [Configuring MySQL or PostgreSQL](#server)
- [Setting The Default Connection Name](#default)

Database configuration in Laravel is easy. The hardest part is deciding which database to use. Three popular open-source databases are supported out of the box:

- MySQL
- PostgreSQL
- SQLite

All of the database configuration options live in the **application/config/db.php** file. Let's get started.

<a name="quick"></a>
### Quick Start Using SQLite

[SQLite](http://sqlite.org) is an awesome, zero-configuration database system. By default, Laravel is configured to use a SQLite database. Really, you don't have to change anything. Just drop a SQLite database named **application.sqlite** into the **application/storage/db directory**. You're done.

Of course, if you want to name your database something besides "application", you can modify the database option in the SQLite section of the **application/config/db.php** file:

	'sqlite' => array(
	     'driver'   => 'sqlite',
	     'database' => 'your_database_name',
	)

If your application receives less than 100,000 hits per day, SQLite should be suitable for production use in your application. Otherwise, consider using MySQL or PostgreSQL.

> **Note:** Need a good SQLite manager? Check out this [Firefox extension](https://addons.mozilla.org/en-US/firefox/addon/sqlite-manager/).

<a name="server"></a>
### Configuring MySQL or PostgreSQL

If you are using MySQL or PostgreSQL, you will need to edit the configuration options in **application/config/db.php**. Don't worry. In the configuration file, sample configurations exist for both systems. All you need to do is change the options as necessary for your server and set the default connection name.

	'mysql' => array(
	     'driver'   => 'mysql',
	     'host'     => 'localhost',
	     'database' => 'database',
	     'username' => 'root',
	     'password' => 'password',
	     'charset'  => 'utf8',
	),

<a name="default"></a>
### Setting The Default Connection Name

As you have probably noticed, each database connection defined in the **application/config/db.php** file has a name. By default, there are three connections defined: **sqlite**, **mysql**, and **pgsql**. You are free to change these connection names. The default connection can be specified via the **default** option:

	'default' => 'sqlite';

The default connection will always be used by the [fluent query builder](/docs/database/query) and [Eloquent ORM](/docs/database/eloquent). If you need to change the default connection during a request, use the **Config::set** method.