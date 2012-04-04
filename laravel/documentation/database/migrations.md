# Migrations

## Contents

- [The Basics](#the-basics)
- [Prepping Your Database](#prepping-your-database)
- [Creating Migrations](#creating-migrations)
- [Running Migrations](#running-migrations)
- [Rolling Back](#rolling-back)

<a name="the-basics"></a>
## The Basics

Think of migrations as a type of version control for your database. Let's say your working on a team, and you all have local databases for development. Good ole' Eric makes a change to the database and checks in his code that uses the new column. You pull in the code, and your application breaks because you don't have the new column. What do you do? Migrations are the answer. Let's dig in deeper to find out how to use them!

<a name="prepping-your-database"></a>
## Prepping Your Database

Before you can run migrations, we need to do some work on your database. Laravel uses a special table to keep track of which migrations have already run. To create this table, just use the Artisan command-line:

**Creating the Laravel migrations table:**

	php artisan migrate:install

<a name="creating-migrations"></a>
## Creating Migrations

You can easily create migrations through Laravel's "Artisan" CLI. It looks like this:

**Creating a migration**

	php artisan migrate:make create_users_table

Now, check your **application/migrations** folder. You should see your brand new migration! Notice that it also contains a timestamp. This allows Laravel to run your migrations in the correct order.

You may also create migrations for a bundle. 

**Creating a migration for a bundle:**

	php artisan migrate:make bundle::create_users_table

*Further Reading:*

- [Schema Builder](/docs/database/schema)

<a name="running-migrations"></a>
## Running Migrations

**Running all outstanding migrations in application and bundles:**

	php artisan migrate

**Running all outstanding migrations in the application:**

	php artisan migrate application

**Running all outstanding migrations in a bundle:**

	php artisan migrate bundle

<a name="rolling-back"></a>
## Rolling Back

When you roll back a migration, Laravel rolls back the entire migration "operation". So, if the last migration command ran 122 migrations, all 122 migrations would be rolled back.

**Rolling back the last migration operation:**

	php artisan migrate:rollback

**Roll back all migrations that have ever run:**

	php artisan migrate:reset