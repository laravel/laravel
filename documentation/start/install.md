## Requirements & Installation

### Requirements

- Apache, nginx, or another compatible web server.
- PHP 5.3+.

### Installation

1. [Download Laravel](https://github.com/taylorotwell/laravel/zipball/master)
2. Extract the Laravel archive and upload the contents to your web server.
4. Set the URL of your application in the **application/config/application.php** file.
5. Navigate to your application in a web browser.

If all is well, you should see a pretty Laravel splash page. Get ready, there is lots more to learn!

### Extras

Installing the following goodies will help you take full advantage of Laravel, but they are not required:

- SQLite, MySQL, or PostgreSQL PDO drivers.
- [Memcached](http://memcached.org) or APC.

### Problems?

- Make sure the **public** directory is the document root of your web server.
- If you are using mod_rewrite, set the **index** option in **application/config/application.php** to an empty string.