<a name="config"></a>
## Session Configuration

- [File System Sessions](#file)
- [Database Sessions](#database)
- [Memcached Sessions](#memcached)

The web is a stateless environment. This means that each request to your application is considered unrelated to any previous request. However, **sessions** allow you to store arbitrary data for each visitor to your application. The session data for each visitor is stored on your web server, while a cookie containing a **session ID** is stored on the visitor's machine. This cookie allows your application to "remember" the session for that user and retrieve their session data on subsequent requests to your application.

Sound complicated? If so, don't worry about it. Just tell Laravel where to store the sessions and it will take care of the rest.

Three great session drivers are available out of the box:

- File System
- Database
- Memcached

<a name="file"></a>
### File System Sessions

Most likely, your application will work great using file system sessions. However, if your application receives heavy traffic or runs on a server farm, use database or Memcached sessions.

To get started using file system sessions, just set the driver option in the **application/config/session.php** file:

	'driver' => 'file'

That's it. You're ready to go!

> **Note:** File system sessions are stored in the **application/storage/sessions** directory, so make sure it's writeable.

<a name="database"></a>
### Database Sessions

To start using database sessions, you will first need to [configure your database connection](/docs/database/config).

Already setup your database? Nice! Next, you will need to create a session table. Here are some SQL statements to help you get started:

#### SQLite

	CREATE TABLE "sessions" (
	     "id" VARCHAR PRIMARY KEY NOT NULL UNIQUE,
	     "last_activity" INTEGER NOT NULL,
	     "data" TEXT NOT NULL
	);

#### MySQL

	CREATE TABLE `sessions` (
	     `id` VARCHAR(40) NOT NULL,
	     `last_activity` INT(10) NOT NULL,
	     `data` TEXT NOT NULL,
	     PRIMARY KEY (`id`)
	);

If you would like to use a different table name, simply change the **table** option in the **application/config/session.php** file:

	'table' => 'sessions'

Great! All you need to do now is set the driver in the **application/config/session.php** file:

	'driver' => 'db'

<a name="memcached"></a>
### Memcached Sessions

Before using Memcached sessions, you must [configure your Memcached servers](/docs/cache/config#memcached).

All done? Great! Just set the driver in the **application/config/session.php** file:

	'driver' => 'memcached'