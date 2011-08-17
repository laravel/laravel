# Laravel Change Log

## Version 1.6.0

- Moved **system/db/manager.php** to **system/db.php**. Updated alias appropriately.
- Unspecified optional parameters will be removed from URLs generated using route names.
- Fixed bug in Config::set that prevented it from digging deep into arrays.
- Replace Crypt class with Crypter class. Ditched static methods for better architecture.
- Re-wrote exception handling classes for better architecture and design.