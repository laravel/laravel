# Laravel - A Clean & Classy PHP Framework

## Introduction

Laravel is a clean and classy framework for PHP web development. Freeing you from spaghetti code, Laravel helps you create wonderful applications using simple, expressive syntax. Development should be a creative experience that you enjoy, not something that is painful. Enjoy the fresh air.

<a name="top">
## Table Of Contents

### Getting Started

- <a href="#installation">Requirements & Installation</a>
- <a href="#config">Basic Configuration</a>
- <a href="#routes">Routes</a>
- <a href="#views">Views & Responses</a>
- <a href="#urls">Generating URLs</a>
- <a href="#html">Generating HTML</a>
- <a href="#errors">Errors & Logs</a>

### Input

- <a href="#input">Retrieving Input</a>
- <a href="#cookie">Cookies</a>
- <a href="#validation">Validation</a>
- <a href="#forms">Building Forms</a>

### Database

- <a href="#db-config">Configuration</a>
- <a href="#db-usage">Usage</a>
- <a href="#fluent">Fluent Query Builder</a>
- <a href="#eloquent">Eloquent ORM</a>

### Caching

- <a href="#cache-config">Configuration</a>
- <a href="#cache-usage">Usage</a>

### Sessions

- <a href="#session-config">Configuration</a>
- <a href="#session-usage">Usage</a>

### Authentication

- <a href="#auth-config">Configuration</a>
- <a href="#auth-usage">Usage</a>

### Other Topics

- <a href="#lang">Localization</a>
- <a href="#crypt">Encryption</a>

<a name="installation"></a>
## Requirements & Installation

### Requirements

- Apache, nginx, or another compatible web server.
- PHP 5.3+ (which supports namespaces, closures, etc.)

### Installation

1. Download Laravel
2. Extract the Laravel archive and upload the contents to your web server.
3. Set the URL of your application in the **application/config/application.php** file.
4. Navigate to your application in a web browser.

If all is well, you should see a pretty Laravel splash page. Get ready, there is lots more to learn!

### Extras

Installaing the following goodies will help you take full advantage of Laravel, but they are not required:

- SQLite, MySQL, or PostgreSQL PDO drivers.
- Memcached or APC.

### Problems?

- Make sure the **public** directory is the document root of your web server.
- If you are using mod\_rewrite, set the **index** option in **application/config/application.php** to an empty string.

[Back To Top](#top)

<a name="config"></a>
## Basic Configuration

### Quick Start

When starting a new project, you shouldn't be bombarded with loads of confusing configuration decisions. For that reason, Laravel is intelligently configured out of the box. The **application/config/application.php** file contains the basic configuration options for your application.

There is only one option that **must** be set when starting a new application. Laravel needs to know the URL you will use to access your application. Simply set the url in the **application/config/application.php** file:

	'url' => 'http://localhost';

> **Note:** If you are using mod_rewrite for cleaner URLs, you should set the index option to an empty string.

<a name="config-clean"></a>
### Cleaner URLs

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

[Back To Top](#top)

<a name="routes"></a>
## Defining Routes

- [The Basics](#routes-basics)
- [Route Wildcards & Parameters](#routes-parameters)
- [Route Filters](#route-filters)
- [Named Routes](#routes-named)
- [Organizing Routes](#routes-folder)

<a name="routes-basics"></a>
### The Basics

Unlike other PHP frameworks, Laravel places routes and their corresponding functions in one file: **application/routes.php**. This file contains the "definition", or public API, of your application. To add functionality to your application, you add to the array located in this file.

All you need to do is tell Laravel the request methods and URIs it should respond to. You define the behavior of the route using an anonymous method:

	'GET /home' => function()
	{
		// Handles GET requests to http://example.com/index.php/home
	},

	'PUT /user/update' => function()
	{
		// Handles PUT requests to http://example.com/index.php/user/update
	}

You can easily define a route to handle requests to more than one URI. Just use commas:

	'POST /, POST /home' => function()
	{
		// Handles POST requests to http://example.com and http://example.com/index.php/home
	}

> **Note:** The routes.php file replaces the "controllers" found in most frameworks. Have a fat model and keep this file light and clean. Thank us later.

[Back To Top](#top)