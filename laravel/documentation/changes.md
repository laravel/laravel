# Laravel Change Log

## Contents

- [Laravel 3.2.7](#3.2.7)
- [Upgrading From 3.2.6](#upgrade-3.2.7)
- [Laravel 3.2.6](#3.2.6)
- [Upgrading From 3.2.5](#upgrade-3.2.6)
- [Laravel 3.2.5](#3.2.5)
- [Upgrading From 3.2.4](#upgrade-3.2.5)
- [Laravel 3.2.4](#3.2.4)
- [Upgrading From 3.2.3](#upgrade-3.2.4)
- [Laravel 3.2.3](#3.2.3)
- [Upgrading From 3.2.2](#upgrade-3.2.3)
- [Laravel 3.2.2](#3.2.2)
- [Upgrading From 3.2.1](#upgrade-3.2.2)
- [Laravel 3.2.1](#3.2.1)
- [Upgrading From 3.2](#upgrade-3.2.1)
- [Laravel 3.2](#3.2)
- [Upgrading From 3.1](#upgrade-3.2)
- [Laravel 3.1.9](#3.1.9)
- [Upgrading From 3.1.8](#upgrade-3.1.9)
- [Laravel 3.1.8](#3.1.8)
- [Upgrading From 3.1.7](#upgrade-3.1.8)
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

<a name="3.2.7"></a>
## Laravel 3.2.7

- Fix bug in Eloquent `to_array` method.
- Fix bug in displaying of generic error page.

<a name="upgrade-3.2.7"></a>
### Upgrading From 3.2.6

- Replace the **laravel** folder.

<a name="3.2.6"></a>
## Laravel 3.2.6

- Revert Blade code back to 3.2.3 tag.

<a name="upgrade-3.2.6"></a>
### Upgrading From 3.2.5

- Replace the **laravel** folder.

<a name="3.2.5"></a>
## Laravel 3.2.5

- Revert nested where code back to 3.2.3 tag.

<a name="upgrade-3.2.5"></a>
### Upgrading From 3.2.4

- Replace the **laravel** folder.

<a name="3.2.4"></a>
## Laravel 3.2.4

- Speed up many to many eager loading mapping.
- Tweak the Eloquent::changed() method.
- Various bug fixes and improvements.

<a name="upgrade-3.2.3"></a>
### Upgrading From 3.2.3

- Replace the **laravel** folder.

<a name="3.2.3"></a>
## Laravel 3.2.3

- Fixed eager loading bug in Eloquent.
- Added `laravel.resolving` event for all IoC resolutions.

<a name="upgrade-3.2.3"></a>
### Upgrading From 3.2.2

- Replace the **laravel** folder.

<a name="3.2.2"></a>
## Laravel 3.2.2

- Overall improvement of Postgres support.
- Fix issue in SQL Server Schema grammar.
- Fix issue with eager loading and `first` or `find`.
- Fix bug causing parameters to not be passed to `IoC::resolve`.
- Allow the specification of hostnames in environment setup.
- Added `DB::last_query` method.
- Added `password` option to Auth configuration.

<a name="upgrade-3.2.2"></a>
### Upgrading From 3.2.1

- Replace the **laravel** folder.

<a name="3.2.1"></a>
## Laravel 3.2.1

- Fixed bug in cookie retrieval when cookie is set on same request.
- Fixed bug in SQL Server grammar for primary keys.
- Fixed bug in Validator on PHP 5.4.
- If HTTP / HTTPS is not specified for generated links, current protocol is used.
- Fix bug in Eloquent auth driver.
- Added `format` method to message container.

<a name="upgrade-3.2.1"></a>
### Upgrading From 3.2

- Replace the **laravel** folder.

<a name="3.2"></a>
## Laravel 3.2

- [Added `to_array` method to the base Eloquent model](/docs/database/eloquent#to-array).
- [Added `$hidden` static variable to the base Eloquent model](/docs/database/eloquent#to-array).
- [Added `sync` method to has\_many\_and\_belongs\_to Eloquent relationship](/docs/database/eloquent#sync-method).
- [Added `save` method to has\_many Eloquent relationship](/docs/database/eloquent#has-many-save).
- [Added `unless` structure to Blade template engine](/docs/views/templating#blade-unless).
- [Added Blade comments](/docs/views/templating#blade-comments).
- [Added simpler environment management](/docs/install#environments).
- Added `Blade::extend()` method to define custom blade compilers.
- Added `View::exists` method.
- Use [Memcached](http://php.net/manual/en/book.memcached.php) API instead of older [Memcache](http://php.net/manual/en/book.memcache.php) API.
- Added support for bundles outside of the bundle directory.
- Added support for DateTime database query bindings.
- Migrated to the Symfony HttpFoundation component for core request / response handling.
- Fixed the passing of strings into the `Input::except` method.
- Fixed replacement of optional parameters in `URL::transpose` method.
- Improved `update` handling on `Has_Many` and `Has_One` relationships.
- Improved View performance by only loading contents from file once.
- Fix handling of URLs beginning with hashes in `URL::to`.
- Fix the resolution of unset Eloquent attributes.
- Allows pivot table timestamps to be disabled.
- Made the `get_timestamp` Eloquent method static.
- `Request::secure` now takes `application.ssl` configuration option into consideration.
- Simplified the `paths.php` file.
- Only write file caches if number of minutes is greater than zero.
- Added `$default` parameter to Bundle::option method.
- Fixed bug present when using Eloquent models with Twig.
- Allow multiple views to be registered for a single composer.
- Added `Request::set_env` method.
- `Schema::drop` now accepts `$connection` as second parameter.
- Added `Input::merge` method.
- Added `Input::replace` method.
- Added saving, saved, updating, creating, deleting, and deleted events to Eloquent.
- Added new `Sectionable` interface to allow cache drivers to simulate namespacing.
- Added support for `HAVING` SQL clauses.
- Added `array_pluck` helper, similar to pluck method in Underscore.js.
- Allow the registration of custom cache and session drivers.
- Allow the specification of a separate asset base URL for using CDNs.
- Allow a `starter` Closure to be defined in `bundles.php` to be run on Bundle::start.
- Allow the registration of custom database drivers.
- New, driver based authentication system.
- Added Input::json() method for working with applications using Backbone.js or similar.
- Added Response::json method for creating JSON responses.
- Added Response::eloquent method for creating Eloquent responses.
- Fixed bug when using many-to-many relationships on non-default database connection.
- Added true reflection based IoC to container.
- Added `Request::route()->controller` and `Request::route()->controller_action`.
- Added `Event::queue`, `Event::flusher`, and `Event::flush` methods to Event class.
- Added `array_except` and `array_only` helpers, similar to `Input::except` and `Input::only` but for arbitrary arrays.

<a name="upgrade-3.2"></a>
### Upgrading From 3.1

- Add new `asset_url` and `profiler` options to application configuration.
- Replace **auth** configuration file.

Add the following entry to the `aliases` array in `config/application.php`..

	'Profiler'   => 'Laravel\\Profiling\\Profiler',

Add the following code above `Blade::sharpen()` in `application/start.php`..

	if (Config::get('application.profiler'))
	{
	    Profiler::attach();
	}

- Upgrade the **paths.php** file.
- Replace the **laravel** folder.

<a name="3.1.9"></a>
## Laravel 3.1.9

- Fixes cookie session driver bug that caused infinite loop on some occasions.

<a name="upgrade-3.1.9"></a>
### Upgrading From 3.1.8

- Replace the **laravel** folder.

<a name="3.1.8"></a>
## Laravel 3.1.8

- Fixes possible WSOD when using Blade's @include expression.

<a name="upgrade-3.1.8"></a>
### Upgrading From 3.1.7

- Replace the **laravel** folder.

<a name="3.1.7"></a>
## Laravel 3.1.7

- Fixes custom validation language line loading from bundles.
- Fixes double-loading of classes when overriding the core.
- Classify migration names.

<a name="upgrade-3.1.7"></a>
### Upgrading From 3.1.6

- Replace the **laravel** folder.

<a name="3.1.6"></a>
## Laravel 3.1.6

- Fixes many-to-many eager loading in Eloquent.

<a name="upgrade-3.1.6"></a>
### Upgrading From 3.1.5

- Replace the **laravel** folder.

<a name="3.1.5"></a>
## Laravel 3.1.5

- Fixes bug that could allow secure cookies to be sent over HTTP.

<a name="upgrade-3.1.5"></a>
### Upgrading From 3.1.4

- Replace the **laravel** folder.

<a name="3.1.4"></a>
## Laravel 3.1.4

- Fixes Response header casing bug.
- Fixes SQL "where in" (...) short-cut bug.

<a name="upgrade-3.1.4"></a>
### Upgrading From 3.1.3

- Replace the **laravel** folder.

<a name="3.1.3"></a>
## Laravel 3.1.3

- Fixes **delete** method in Eloquent models.

<a name="upgrade-3.1.3"></a>
### Upgrade From 3.1.2

- Replace the **laravel** folder.

<a name="3.1.2"></a>
## Laravel 3.1.2

- Fixes Eloquent query method constructor conflict.

<a name="upgrade-3.1.2"></a>
### Upgrade From 3.1.1

- Replace the **laravel** folder.

<a name="3.1.1"></a>
## Laravel 3.1.1

- Fixes Eloquent model hydration bug involving custom setters.

<a name="upgrade-3.1.1"></a>
### Upgrading From 3.1

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
### Upgrading From 3.0

#### Replace your **application/start.php** file.

The default **start.php** file has been expanded in order to give you more flexibility over the loading of your language, configuration, and view files. To upgrade your file, copy your current file and paste it at the bottom of a copy of the new Laravel 3.1 start file. Next, scroll up in the **start** file until you see the default Autoloader registrations (line 61 and line 76). Delete both of these sections since you just pasted your previous auto-loader registrations at the bottom of the file.

#### Remove the **display** option from your **errors** configuration file.

This option is now set at the beginning of your **application/start** file.

#### Call the parent controller's constructor from your controller.

Simply add a **parent::__construct();** to to any of your controllers that have a constructor.

#### Prefix Laravel migration created indexes with their table name.

If you have created indexes on tables using the Laravel migration system and you used to the default index naming scheme provided by Laravel, prefix the index names with their table name on your database. So, if the current index name is "id_unique" on the "users" table, make the index name "users_id_unique".

#### Add alias for Eloquent in your application configuration.

Add the following to the **aliases** array in your **application/config/application.php** file:

	'Eloquent' => 'Laravel\\Database\\Eloquent\\Model',
	'Blade' => 'Laravel\\Blade',

#### Update Eloquent many-to-many tables.

Eloquent now maintains **created_at** and **updated_at** column on many-to-many intermediate tables by default. Simply add these columns to your tables. Also, many-to-many tables are now the singular model names concatenated with an underscore. For example, if the relationship is between User and Role, the intermediate table name should be **role_user**.

#### Remove Eloquent bundle.

If you are using the Eloquent bundle with your installation, you can remove it from your bundles directory and your **application/bundles.php** file. Eloquent version 2 is included in the core in Laravel 3.1. Your models can also now extend simply **Eloquent** instead of **Eloquent\Model**.

#### Update your **config/strings.php** file.

English pluralization and singularization is now automatic. Just completely replace your **application/config/strings.php** file.

#### Add the **fetch** option to your database configuration file.

A new **fetch** option allows you to specify in which format you receive your database results. Just copy and paste the option from the new **application/config/database.php** file.

#### Add **database** option to your Redis configuration.

If you are using Redis, add the "database" option to your Redis connection configurations. The "database" value can be zero by default.

	'redis' => array(
		'default' => array(
			'host' => '127.0.0.1',
			'port' => 6379,
			'database' => 0
		),
	),
