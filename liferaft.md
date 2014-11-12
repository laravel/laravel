Eloquent Model Attributes corrupted on HasManyThrough

When using hasManyThrough relationships using `$user->comments()->find($id)`, where `comments()` is a `hasManyThrough` relationship through `Post`, will succeed but all the attributes are for the Post model even though the object is a Comment instance.  Is this the responseiblity of the developer to handle this case?  If so it may want to be more explicitly acknowledged in the docs.

1. Either ensure SQLite PHP extension is installed or change database dirver to MySQL.  This is not specific to one database driver.
2. `php artisan migrate`