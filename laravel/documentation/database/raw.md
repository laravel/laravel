# Raw Queries

## Contents

- [The Basics](#the-basics)
- [Other Query Methods](#other-query-methods)
- [PDO Connections](#pdo-connections)

<a name="the-bascis"></a>
## The Basics

The **query** method is used to execute arbitrary, raw SQL against your database connection. 

#### Selecting records from the database:

	$users = DB::query('select * from users');

#### Selecting records from the database using bindings:

	$users = DB::query('select * from users where name = ?', array('test'));

#### Inserting a record into the database

	$success = DB::query('insert into users values (?, ?)', $bindings);

#### Updating table records and getting the number of affected rows:

	$affected = DB::query('update users set name = ?', $bindings);

#### Deleting from a table and getting the number of affected rows:

	$affected = DB::query('delete from users where id = ?', array(1));

<a name="other-query-methods"></a>
## Other Query Methods

Laravel provides a few other methods to make querying your database simple. Here's an overview:

#### Running a SELECT query and returning the first result:

	$user = DB::first('select * from users where id = 1');

#### Running a SELECT query and getting the value of a single column:

	$email = DB::only('select email from users where id = 1');

<a name="pdo-connections"></a>
## PDO Connections

Sometimes you may wish to access the raw PDO connection behind the Laravel Connection object.

#### Get the raw PDO connection for a database:

	$pdo = DB::connection('sqlite')->pdo;

> **Note:** If no connection name is specified, the **default** connection will be returned.