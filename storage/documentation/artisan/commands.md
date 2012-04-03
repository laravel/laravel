# Artisan Commands

## Contents

- [Application Configuration](#application-configuration)
- [Sessions](#sessions)
- [Migrations](#migrations)
- [Bundles](#bundles)
- [Tasks](#tasks)
- [Unit Tests](#unit-tests)
- [Routing](#routing)
- [Application Keys](#keys)
- [CLI Options](#cli-options)

<a name="application-configuration"></a>
## Application Configuration <small>[(More Information)](/docs/install#basic-configuration)</small>

Description  | Command
------------- | -------------
Generate a secure application key. An application key will not be generated unless the field in **config/application.php** is empty. | `php artisan key:generate`

<a name="sessions"></a>
## Database Sessions <small>[(More Information)](/docs/session/config#database)</small>

Description  | Command
------------- | -------------
Create a session table  | `php artisan session:table`

<a name="migrations"></a>
## Migrations <small>[(More Information)](/docs/database/migrations)</small>

Description  | Command
------------- | -------------
Create the Laravel migration table  | `php artisan migrate:install`
Creating a migration  | `php artisan migrate:make create_users_table`
Creating a migration for a bundle  |  `php artisan migrate:make bundle::tablename`
Running outstanding migrations  |  `php artisan migrate`
Running outstanding migrations in the application |  `php artisan migrate application`
Running all outstanding migrations in a bundle  |  `php artisan migrate bundle`
Rolling back the last migration operation | `php artisan migrate:rollback`
Roll back all migrations that have ever run  |  `php artisan migrate:reset`

<a name="bundles"></a>
## Bundles <small>[(More Information)](/docs/bundles)</small>

Description  | Command
------------- | -------------
Install a bundle  |  `php artisan bundle:install eloquent`
Upgrade a bundle  |  `php artisan bundle:upgrade eloquent`
Upgrade all bundles | `php artisan bundle:upgrade`
Publish a bundle assets | `php artisan bundle:publish bundle_name`
Publish all bundles assets | `php artisan bundle:publish`

<br>
> **Note:** After installing you need to [register the bundle](../bundles/#registering-bundles)

<a name="tasks"></a>
## Tasks <small>[(More Information)](/docs/artisan/tasks)</small>

Description  | Command
------------- | -------------
Calling a task  |  `php artisan notify`
Calling a task and passing arguments  |  `php artisan notify taylor`
Calling a specific method on a task  |  `php artisan notify:urgent`
Running a task on a bundle | `php artisan admin::generate`
Running a specific method on a bundle  |  `php artisan admin::generate:list`

<a name="unit-tests"></a>
## Unit Tests <small>[(More Information)](/docs/testing)</small>

Description  | Command
------------- | -------------
Running the application tests  |  `php artisan test`
Running the bundle tests  |  `php artisan test bundle-name`

<a name="routing"></a>
## Routing <small>[(More Information)](/docs/routing)</small>

Description  | Command
------------- | -------------
Calling a route  |  `php artisan route:call get api/user/1`

<br>
> **Note:** You can replace get with post, put, delete, etc.

<a name="keys"></a>
## Application Keys

Description  | Command
------------- | -------------
Generate an application key  |  `php artisan key:generate`

<br>
> **Note:** You can specify an alternate key length by adding an extra argument to the command.

<a name="cli-options"></a>
## CLI Options

Description  | Command
------------- | -------------
Setting the Laravel environment  |  `php artisan foo --env=local`
Setting the default database connection  |  `php artisan foo --database=sqlitename`
