# Database Configuration

## Contents

- [Quick Start Using SQLite](#quick)
- [Configuring Other Databases](#server)
- [Setting The Default Connection Name](#default)
- [Overwriting The Default PDO Options](#options)

Laravel supports the following databases out of the box:

- MySQL
- PostgreSQL
- SQLite
- SQL Server

All of the database configuration options live in the **application/config/database.php** file.

<a name="quick"></a>
## Quick Start Using SQLite

[SQLite](http://sqlite.org) is an awesome, zero-configuration database system. By default, Laravel is configured to use a SQLite database. Really, you don't have to change anything. Just drop a SQLite database named **application.sqlite** into the **application/storage/database** directory. You're done.

Of course, if you want to name your database something besides "application", you can modify the database option in the SQLite section of the **application/config/database.php** file:

	'sqlite' => array(
	     'driver'   => 'sqlite',
	     'database' => 'your_database_name',
	)

If your application receives less than 100,000 hits per day, SQLite should be suitable for production use in your application. Otherwise, consider using MySQL or PostgreSQL.

> **Note:** Need a good SQLite manager? Check out this [Firefox extension](https://addons.mozilla.org/en-US/firefox/addon/sqlite-manager/).

<a name="server"></a>
## Configuring Other Databases

If you are using MySQL, SQL Server, or PostgreSQL, you will need to edit the configuration options in **application/config/database.php**. In the configuration file you can find sample configurations for each of these systems. Just change the options as necessary for your server and set the default connection name.

<a name="default"></a>
## Setting The Default Connection Name

As you have probably noticed, each database connection defined in the **application/config/database.php** file has a name. By default, there are three connections defined: **sqlite**, **mysql**, **sqlsrv**, and **pgsql**. You are free to change these connection names. The default connection can be specified via the **default** option:

	'default' => 'sqlite';

The default connection will always be used by the [fluent query builder](/docs/database/fluent). If you need to change the default connection during a request, use the **Config::set** method.

<a href="options"></a>
##Overwriting The Default PDO Options

The PDO connecter class (**laravel/database/connectors/connector.php**) has a set of default PDO attributes defined which can be overwritten in the options array for each system. For example, one of the default attributes is to force column names to lowercase (**PDO::CASE_LOWER**) even if they are defined in UPPERCASE or CamelCase in the table. Therefor, under the default attributes, query result object variables would only be accessible in lowercase.
An example of the MySQL system settings with added default PDO attributes:

	'mysql' => array(
		'driver'   => 'mysql',
		'host'     => 'localhost',
		'database' => 'database',
		'username' => 'root',
		'password' => '',
		'charset'  => 'utf8',
		'prefix'   => '',
		PDO::ATTR_CASE              => PDO::CASE_LOWER,
		PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES  => false,
	),

More about the PDO connection attributes can be found [here](http://php.net/manual/en/pdo.setattribute.php).