# Laravel Change Log

## Version 1.6.6

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