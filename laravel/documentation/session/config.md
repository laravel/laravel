<a name="config"></a>
# Session Configuration

## Contents

- [The Basics](#the-basics)
- [Cookie Sessions](#cookie)
- [File System Sessions](#file)
- [Database Sessions](#database)
- [Memcached Sessions](#memcached)
- [Redis Sessions](#redis)
- [In-Memory Sessions](#memory)

<a name="the-basics"></a>
## The Basics

The web is a stateless environment. This means that each request to your application is considered unrelated to any previous request. However, **sessions** allow you to store arbitrary data for each visitor to your application. The session data for each visitor is stored on your web server, while a cookie containing a **session ID** is stored on the visitor's machine. This cookie allows your application to "remember" the session for that user and retrieve their session data on subsequent requests to your application.

> **Note:** Before using sessions, make sure an application key has been specified in the **application/config/application.php** file.

Six session drivers are available out of the box:

- Cookie
- File System
- Database
- Memcached
- Redis
- Memory (Arrays)

<a name="cookie"></a>
## Cookie Sessions

Cookie based sessions provide a light-weight and fast mechanism for storing session information. They are also secure. Each cookie is encrypted using strong AES-256 encryption. However, cookies have a four kilobyte storage limit, so you may wish to use another driver if you are storing a lot of data in the session.

To get started using cookie sessions, just set the driver option in the **application/config/session.php** file:

	'driver' => 'cookie'

<a name="file"></a>
## File System Sessions

Most likely, your application will work great using file system sessions. However, if your application receives heavy traffic or runs on a server farm, use database or Memcached sessions.

To get started using file system sessions, just set the driver option in the **application/config/session.php** file:

	'driver' => 'file'

That's it. You're ready to go!

> **Note:** File system sessions are stored in the **storage/sessions** directory, so make sure it's writeable.

<a name="database"></a>
## Database Sessions

To start using database sessions, you will first need to [configure your database connection](/docs/database/config).

Next, you will need to create a session table. Below are some SQL statements to help you get started. However, you may also use Laravel's "Artisan" command-line to generate the table for you!

### Artisan

	php artisan session:table

### SQLite

	CREATE TABLE "sessions" (
	     "id" VARCHAR PRIMARY KEY NOT NULL UNIQUE,
	     "last_activity" INTEGER NOT NULL,
	     "data" TEXT NOT NULL
	);

### MySQL

	CREATE TABLE `sessions` (
	     `id` VARCHAR(40) NOT NULL,
	     `last_activity` INT(10) NOT NULL,
	     `data` TEXT NOT NULL,
	     PRIMARY KEY (`id`)
	);

If you would like to use a different table name, simply change the **table** option in the **application/config/session.php** file:

	'table' => 'sessions'

All you need to do now is set the driver in the **application/config/session.php** file:

	'driver' => 'database'

<a name="memcached"></a>
## Memcached Sessions

Before using Memcached sessions, you must [configure your Memcached servers](/docs/database/config#memcached).

Just set the driver in the **application/config/session.php** file:

	'driver' => 'memcached'

<a name="redis"></a>
## Redis Sessions

Before using Redis sessions, you must [configure your Redis servers](/docs/database/redis#config).

Just set the driver in the **application/config/session.php** file:

	'driver' => 'redis'

<a name="memory"></a>
## In-Memory Sessions

The "memory" session driver just uses a simple array to store your session data for the current request. This driver is perfect for unit testing your application since nothing is written to disk. It shouldn't ever be used as a "real" session driver.