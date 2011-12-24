# Laravel Change Log

## Version 2.0.6

- Fix: Fixed nested sections.

## Version 2.0.5

- Feature: Added array access to session::get.
- Fix: Remove orderings before running pagination queries.
- Fix: Session flush now correctly prepares empty data.
- Fix: DB::raw now works on Eloquent properties.

### Upgrading from 2.0.4

- Replace **laravel** directory.

## Version 2.0.4

- Feature: Added default parameter to File::get method.
- Feature: Allow for message container to be passed to Redirect's "with_errors" method.
- Fix: Lowercase HTTP verbs may be passed to Form::open method.
- Fix: Filter parameters are now merged correctly.

### Upgrading from 2.0.3

- Replace **laravel** directory.

## Version 2.0.3

- Feature: The application URL is now auto-detected.
- Feature: Added new URL::to_action and URL::to_secure_action methods.
- Fix: Fixed a bug in the Autoloader's PSR-0 library detection.
- Fix: View composers should be cached on the first retrieval.

### Upgrading from 2.0.2

- Replace **laravel** directory.

## Version 2.0.2

- Fixed bug in validator class that prevented required file uploads from being validated correctly.
- Added API example to File::upload method.

### Upgrading from 2.0.1

- Replace **laravel** directory.

## Version 2.0.1

- Fixed bug in routing filter class.

### Upgrading from 2.0.0

- Replace **laravel** directory.

## Version 2.0.0

- Added support for controllers.
- Added Redis support, along with cache and session drivers.
- Added cookie session driver.
- Added support for database expressions.
- Added Blade templating engine.
- Added view "sections".
- Added support for filter parameters.
- Added dependency injection and IoC support.
- Made authentication system more flexible.
- Added better PSR-0 library support.
- Added fingerprint hashing to cookies.
- Improved view error handling.
- Made input flashing more developer friendly.
- Added better Redirect shortcut methods.
- Added standalone Memcached class.
- Simplified exception handling.
- Added ability to ignore certain error levels.
- Directories re-structured.
- Improved overall code quality and architecture.

## Version 1.5.9

- Fixed bug in Eloquent relationship loading.

### Upgrading from 1.5.8

- Replace **system** directory.

## Version 1.5.8

- Fixed bug in form class that prevent name attributes from being set properly.

### Upgrading from 1.5.7

- Replace **system** directory.

## Version 1.5.7

- Fixed bug that prevented view composers from being called for module named views.

### Upgrading from 1.5.6

- Replace **system** directory.

## Version 1.5.6

- Fix bug that caused exceptions to not be shown when attempting to render a view that doesn't exist.

### Upgrading from 1.5.5

- Replace **system** directory.

## Version 1.5.5

- Fix bug in session class cookie option extraction.

### Upgrading From 1.5.4

- Replace **system** directory.

## Version 1.5.4

- Fix bug in Eloquent belongs_to relationship eager loading.

### Upgrading From 1.5.3

- Replace **system** directory.

## Version 1.5.3

- Various bug fixes.
- Allow columns to be specified on Eloquent queries.

### Upgrading From 1.5.2

- Replace **system** directory.

## Version 1.5.2

- Moved **system/db/manager.php** to **system/db.php**. Updated alias appropriately.
- Unspecified optional parameters will be removed from URLs generated using route names.
- Fixed bug in Config::set that prevented it from digging deep into arrays.
- Replace Crypt class with Crypter class. Ditched static methods for better architecture.
- Re-wrote exception handling classes for better architecture and design.

### Upgrading From 1.5.1

- Replace the **system** directory.
- Replace the **application/config/aliases.php** file.
- Take note of encryption class changes.
