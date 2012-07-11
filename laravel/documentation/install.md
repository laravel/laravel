# Installation & Setup

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Server Configuration](#server-configuration)
- [Basic Configuration](#basic-configuration)
- [Environments](#environments)
- [Cleaner URLs](#cleaner-urls)

<a name="requirements"></a>
## Requirements

- Apache, nginx, or another compatible web server.
- Laravel takes advantage of the powerful features that have become available in PHP 5.3. Consequently, PHP 5.3 is a requirement.
- Laravel uses the [FileInfo library](http://php.net/manual/en/book.fileinfo.php) to detect files' mime-types. This is included by default with PHP 5.3. However, Windows users may need to add a line to their php.ini file before the Fileinfo module is enabled. For more information check out the [installation / configuration details on PHP.net](http://php.net/manual/en/fileinfo.installation.php).
- Laravel uses the [Mcrypt library](http://php.net/manual/en/book.mcrypt.php) for encryption and hash generation. Mcrypt typically comes pre-installed. If you can't find Mcrypt in the output of phpinfo() then check the vendor site of your LAMP installation or check out the [installation / configuration details on PHP.net](http://php.net/manual/en/book.mcrypt.php).

<a name="installation"></a>
## Installation

1. [Download Laravel](http://laravel.com/download)
2. Extract the Laravel archive and upload the contents to your web server.
3. Set the value of the **key** option in the **config/application.php** file to a random, 32 character string.
4. Verify that the `storage/views` directory is writable.
5. Navigate to your application in a web browser.

If all is well, you should see a pretty Laravel splash page. Get ready, there is lots more to learn!

### Extra Goodies

Installing the following goodies will help you take full advantage of Laravel, but they are not required:

- SQLite, MySQL, PostgreSQL, or SQL Server PDO drivers.
- Memcached or APC.

### Problems?

If you are having problems installing, try the following:

- Make sure the **public** directory is the document root of your web server. (see: Server Configuration below)
- If you are using mod_rewrite, set the **index** option in **application/config/application.php** to an empty string.
- Verify that your storage folder and the folders within are writable by your web server.

<a name="server-configuration"></a>
## Server Configuration

Like most web-development frameworks, Laravel is designed to protect your application code, bundles, and local storage by placing only files that are necessarily public in the web server's DocumentRoot. This prevents some types of server misconfiguration from making your code (including database passwords and other configuration data) accessible through the web server. It's best to be safe. 

In this example let's imagine that we installed Laravel to the directory **/Users/JonSnow/Sites/MySite**.

A very basic example of an Apache VirtualHost configuration for MySite might look like this.

	<VirtualHost *:80>
		DocumentRoot /Users/JonSnow/Sites/MySite/public
		ServerName mysite.dev
	</VirtualHost>

Notice that while we installed to **/Users/JonSnow/Sites/MySite** our DocumentRoot points to /Users/JonSnow/Sites/MySite/public**.

While pointing the DocumentRoot to the public folder is a commonly used best-practice, it's possible that you may need to use Laravel on a host that does not allow you to update your DocumentRoot. A collection of algorithms to circumvent this need can be found [http://forums.laravel.com/viewtopic.php?id=1258](on the Laravel forums.)
>>>>>>> feature/docs-install

<a name="basic-configuration"></a>
## Basic Configuration

All of the configuration provided are located in your applications config/ directory. We recommend that you read through these files just to get a basic understanding of the options available to you. Pay special attention to the **application/config/application.php** file as it contains the basic configuration options for your application.

It's **extremely** important that you change the **application key** option before working on your site. This key is used throughout the framework for encryption, hashing, etc. It lives in the **config/application.php** file and should be set to a random, 32 character string. A standards-compliant application key can be automatically generated using the Artisan command-line utility.  More information can be found in the [Artisan command index](/docs/artisan/commands).

> **Note:** If you are using mod_rewrite, you should set the index option to an empty string.

<a name="environments"></a>
## Environments

Most likely, the configuration options you need for local development are not the same as the options you need on your production server. Laravel's default environment handling mechanism is URL based, which will make setting up environments a breeze. Pop open the `paths.php` file in the root of your Laravel installation. You should see an array like this:

	$environments = array(

		'local' => array('http://localhost*', '*.dev'),

	);

This tells Laravel that any URLs beginning with "localhost" or ending with ".dev" should be considered part of the "local" environment.

Next, create an **application/config/local** directory. Any files and options you place in this directory will override the options in the base **application/config** directory. For example, you may wish to create an **application.php** file within your new **local** configuration directory:

	return array(

		'url' => 'http://localhost/laravel/public',

	);

In this example, the local **URL** option will override the **URL** option in **application/config/application.php**. Notice that you only need to specify the options you wish to override.

Isn't it easy? Of course, you are free to create as many environments as you wish!

<a name="cleaner-urls"></a>
## Cleaner URLs

Most likely, you do not want your application URLs to contain "index.php". You can remove it using HTTP rewrite rules. If you are using Apache to serve your application, make sure to enable mod_rewrite and create a **.htaccess** file like this one in your **public** directory:

	<IfModule mod_rewrite.c>
	     RewriteEngine on

	     RewriteCond %{REQUEST_FILENAME} !-f
	     RewriteCond %{REQUEST_FILENAME} !-d

	     RewriteRule ^(.*)$ index.php/$1 [L]
	</IfModule>

Is the .htaccess file above not working for you? Try this one:

	Options +FollowSymLinks
	RewriteEngine on

	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d

	RewriteRule . index.php [L]

After setting up HTTP rewriting, you should set the **index** configuration option in **application/config/application.php** to an empty string.

> **Note:** Each web server has a different method of doing HTTP rewrites, and may require a slightly different .htaccess file.