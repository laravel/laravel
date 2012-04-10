## Laravel Change Log

## Contents

- [Laravel 3.1.7](#3.1.7)
- [Upgrading From 3.1.6](#upgrade-3.1.7)
- [Laravel 3.1.6](#3.1.6)
- [Upgrading From 3.1.5](#upgrade-3.1.6)
- [Laravel 3.1.5](#3.1.5)
- [Upgrading From 3.1.4](#upgrade-3.1.5)
- [Laravel 3.1.4](#3.1.4)
- [Upgrading From 3.1.3](#upgrade-3.1.4)
- [Laravel 3.1.3](#3.1.3)
- [Upgrading From 3.1.2](#uprade-3.1.3)
- [Laravel 3.1.2](#3.1.2)
- [Upgrading From 3.1.1](#upgrade-3.1.2)
- [Laravel 3.1.1](#3.1.1)
- [Upgrading From 3.1](#upgrade-3.1.1)
- [Laravel 3.1](#3.1)
- [Upgrading From 3.0](#upgrade-3.1)

<a name="3.1.7"></a>
## Laravel 3.1.7

- Fixes custom validation language line loading from bundles.
- Fixes double-loading of classes when overriding the core.
- Classify migration names.

<a name="upgrade-3.1.7"></a>
## Upgrading From 3.1.6

- Replace the **laravel** folder.

<a name="3.1.6"></a>
## Laravel 3.1.6

- Fixes many-to-many eager loading in Eloquent.

<a name="upgrade-3.1.6"></a>
## Upgrading From 3.1.5

- Replace the **laravel** folder.

<a name="3.1.5"></a>
## Laravel 3.1.5

- Fixes bug that could allow secure cookies to be sent over HTTP.

<a name="upgrade-3.1.5"></a>
## Upgrading From 3.1.4

- Replace the **laravel** folder.

<a name="3.1.4"></a>
## Laravel 3.1.4

- Fixes Response header casing bug.
- Fixes SQL "where in" (...) short-cut bug.

<a name="upgrade-3.1.4"></a>
## Upgrading From 3.1.3

- Replace the **laravel** folder.

<a name="3.1.3"></a>
## Laravel 3.1.3

- Fixes **delete** method in Eloquent models.

<a name="upgrade-3.1.3"></a>
## Upgrade From 3.1.2

- Replace the **laravel** folder.

<a name="3.1.2"></a>
## Laravel 3.1.2

- Fixes Eloquent query method constructor conflict.

<a name="upgrade-3.1.2"></a>
## Upgrade From 3.1.1

- Replace the **laravel** folder.

<a name="3.1.1"></a>
## Laravel 3.1.1

- Fixes Eloquent model hydration bug involving custom setters.

<a name="upgrade-3.1.1"></a>
## Upgrading From 3.1

- Replace the **laravel** folder.

<a name="3.1"></a>
## Laravel 3.1

- Added events to logger for more flexibility.
- Added **database.fetch** configuration option.
- Added controller factories for injecting any IoC.
- Added **link_to_action** HTML helpers.
- Added ability to set default value on Config::get.
- Added the ability to add pattern based filters.
- Improved session ID assignment.
- Added support for "unsigned" integers in schema builder.
- Added config, view, and lang loaders.
- Added more logic to **application/start.php** for more flexibility.
- Added foreign key support to schema builder.
- Postgres "unique" indexes are now added with ADD CONSTRAINT.
- Added "Event::until" method.
- Added "memory" cache and session drivers.
- Added Controller::detect method.
- Added Cache::forever method.
- Controller layouts now resolved in Laravel\Controller __construct.
- Rewrote Eloquent and included in core.
- Added "match" validation rule.
- Fixed table prefix bug.
- Added Form::macro method.
- Added HTML::macro method.
- Added Route::forward method.
- Prepend table name to default index names in schema.
- Added "forelse" to Blade.
- Added View::render_each.
- Able to specify full path to view (path: ).
- Added support for Blade template inheritance.
- Added "before" and "after" validation checks for dates.

<a name="upgrade-3.1"></a>
## Upgrading From 3.0

### Replace your **application/start.php** file.

The default **start.php** file has been expanded in order to give you more flexibility over the loading of your language, configuration, and view files. To upgrade your file, copy your current file and paste it at the bottom of a copy of the new Laravel 3.1 start file. Next, scroll up in the **start** file until you see the default Autoloader registrations (line 61 and line 76). Delete both of these sections since you just pasted your previous auto-loader registrations at the bottom of the file.

### Remove the **display** option from your **errors** configuration file.

This option is now set at the beginning of your **application/start** file.

### Call the parent controller's constructor from your controller.

Simply add a **parent::__construct();** to to any of your controllers that have a constructor.

### Prefix Laravel migration created indexes with their table name.

If you have created indexes on tables using the Laravel migration system and you used to the default index naming scheme provided by Laravel, prefix the index names with their table name on your database. So, if the current index name is "id_unique" on the "users" table, make the index name "users_id_unique".

### Add alias for Eloquent in your application configuration.

Add the following to the **aliases** array in your **application/config/application.php** file:

	'Eloquent' => 'Laravel\\Database\\Eloquent\\Model',
	'Blade' => 'Laravel\\Blade',

### Update Eloquent many-to-many tables.

Eloquent now maintains **created_at** and **updated_at** column on many-to-many intermediate tables by default. Simply add these columns to your tables. Also, many-to-many tables are now the singular model names concatenated with an underscore. For example, if the relationship is between User and Role, the intermediate table name should be **role_user**.

### Remove Eloquent bundle.

If you are using the Eloquent bundle with your installation, you can remove it from your bundles directory and your **application/bundles.php** file. Eloquent version 2 is included in the core in Laravel 3.1. Your models can also now extend simply **Eloquent** instead of **Eloquent\Model**.

### Update your **config/strings.php** file.

English pluralization and singularization is now automatic. Just completely replace your **application/config/strings.php** file.

### Add the **fetch** option to your database configuration file.

A new **fetch** option allows you to specify in which format you receive your database results. Just copy and paste the option from the new **application/config/database.php** file.

### Add **database** option to your Redis configuration.

If you are using Redis, add the "database" option to your Redis connection configurations. The "database" value can be zero by default.

	'redis' => array(
		'default' => array(
			'host' => '127.0.0.1',
			'port' => 6379,
			'database' => 0
		),
	),
