# project-name

## Installation
1. Clone repository
2. Start the containers
2. Access the PHP container and:
> A. **RUN** `composer config -g github-oauth.github.com <token>`
(To create the token go to: https://github.com/settings/tokens/new and set the **repo** permissions)

> B. **RUN** `composer install`

> C. **RUN** `ln -s /proxies proxies`

> D. **RUN** `composer build`

## Minio configuration
1. ADD "127.0.0.1 s3" to your hosts file
2. ACCESS `http://localhost:9000/minio`,
3. Login with the access and secret keys located in `docker-compose.yml`.
4. Create a bucket with some name. Add a R/W policy to the bucket.
5. Configure the bucket name in your env file
6. Change the filesystem driver to `minio`

## Sentry configuration
2. Configure your .env variables
3. Enable Sentry on your .env file

## System Configuration

* Remember that all sites must use HTTPS. And please choose a proper redirect:
    * http://www.site.com should redirect to https://www.site.com
    * http://site.com should redirect to https://www.site.com
    * https://site.com should redirect to https://www.site.com
    * The sub-domain (www), can be interchanged in the examples. But you must choose to use it and redirect TO it or not use it and redirect FROM it.
    * Please see docker/apache/default.conf for the server configuration to access the site and resources.

## Publish Assets
1. `php artisan vendor:publish --provider "Digbang\\Backoffice\\BackofficeServiceProvider" --tag assets --force`

## SPECIAL DIRECTORIES
Permissions
```
// at the root of the project (only on linux)
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

Proxies (ensure the symlink exists...)
```
// ... if not; inside of the PHP container run
ln -s /proxies /proxies
```

## Repositories on Request

If you need a repository you may do this:

Example: 
```php
public function users(): ?array
{
    if ($this->input(self::USER_IDS)) {
        return $this->repository(UserRepository::class)->find($this->input(self::USER_IDS));
    }

    return null;
}
```

If you need a repository method that doesnt exist in ReadRepository, you must create a private method into your request.

Example:

```php
private function roleRepository(): RoleRepository
{
    /** @var RoleRepository $repository */
    $repository = $this->repository(RoleRepository::class);

    return $repository;
}


public function roles(): ?array
{
    if ($this->input(self::ROLE_NAME)) {
        return $this->roleRepository()->findByName($this->input(self::ROLE_NAME));
    }

    return null;
}
```

## HTTP Codes references
The next list contains the HTTP codes returned by the API and the meaning in the present context:

* HTTP 200 Ok: the request has been processed successfully.
* HTTP 201 Created: the resource has been created. It's associated with a POST Request.
* HTTP 204 No Content: the request has been processed successfully but does not need to return an entity-body.
* HTTP 400 Bad Request: the request could not been processed by the API. You should review the data sent to.
* HTTP 401 Unauthorized: When the request was performed to the login endpoint, means that credentials are not matching with any. When the request was performed to another endpoint means that the token it's not valid anymore due TTL expiration.
* HTTP 403 Forbidden: the credentials provided with the request has not the necessary permission to be processed.
* HTTP 404 Not Found: the endpoint requested does not exist in the API. 
* HTTP 422: the payload sent to the API did not pass the validation process.
* HTTP 500: an unknown error was triggered during the process.

Please refer to https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html for reference

## Php Stan

You should now have a ``phpstan.neon`` file that allows you to configure the basics of this package.

https://github.com/nunomaduro/larastan
https://phpstan.org/user-guide/getting-started

## Php Insight

### Config File

You should now have a ``config/insights.php`` file that allows you to configure the basics of this package.

https://phpinsights.com/get-started.html

## Laravel Responder

### Creating Responses

```php
return responder()
    ->success($user, UserProfileTransformer::class)
    ->meta([
        'meta' => $this->shouldRefreshToken(),
    ])
    ->respond();
```

### Creating Transformers
``php artisan make:transformer ProductTransformer --plain``

### Handling Exceptions

#### Convert Custom Exceptions

In addition to letting the package convert Laravel exceptions, you can also convert your own exceptions using the `convert` method in the `render` method:

```php
$this->convert($exception, [
    InvalidValueException => PageNotFoundException,
]);
```

You can optionally give it a closure that throws the new exception, if you want to give it constructor parameters: 

```php
$this->convert($exception, [
    MaintenanceModeException => function ($exception) {
        throw new ServerDownException($exception->retryAfter);
    },
]);
```

### Creating HTTP Exceptions

An exception class is a convenient place to store information about an error. The package provides an abstract exception class `Flugg\Responder\Exceptions\Http\HttpException`, which has knowledge about status code, an error code and an error message. Continuing on our product example from above, we could create our own `HttpException` class:

```php
<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class SoldOutException extends HttpException
{
    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected $status = 400;

    /**
     * The error code.
     *
     * @var string|null
     */
    protected $errorCode = 'sold_out_error';

    /**
     * The error message.
     *
     * @var string|null
     */
    protected $message = 'The requested product is sold out.';
}
```

You can also add a `data` method returning additional error data:

```php
/**
 * Retrieve additional error data.
 *
 * @return array|null
 */
public function data()
{
    return [
        'shipments' => Shipment::all()
    ];
}
```

If you're letting the package handle exceptions, you can now throw the exception anywhere in your application and it will automatically be rendered to an error response.

```php
throw new SoldOutException();
```

https://github.com/flugger/laravel-responder

## JWT Auth

#### Config File

You should now have a ``config/jwt.php`` file that allows you to configure the basics of this package.

####Generate secret key

I have included a helper command to generate a key for you:

``php artisan jwt:secret``

This will update your ``.env`` file with something like ``JWT_SECRET=foobar``

It is the key that will be used to sign your tokens. How that happens exactly will depend on the algorithm that you choose to use.

https://github.com/tymondesigns/jwt-auth

## System Requirements
* php: 7.4.x
* php ini configurations:
    * `upload_max_filesize = 100M`
    * `post_max_size = 100M`
    * This numbers are illustrative. Set them according to your project needs.  

* php extensions:
    * bcmath
    * Core
    * ctype
    * curl
    * date
    * dom
    * fileinfo
    * filter
    * ftp
    * gd
    * hash
    * iconv
    * imagick
    * intl
    * json
    * libxml
    * mbstring
    * mcrypt
    * mysqlnd
    * openssl
    * pcntl
    * pcre
    * PDO
    * pdo_pgsql
    * pdo_sqlite
    * Phar
    * posix
    * readline
    * redis
    * Reflection
    * session
    * SimpleXML
    * soap
    * SPL
    * sqlite3
    * standard
    * tidy
    * tokenizer
    * xdebug
    * xml
    * xmlreader
    * xmlwriter
    * ZendOPcache
    * zip
    * zlib
* Composer PHP
* apache: 2.4.x / nginx
* postgres: 11.x / 12.x
* postgres extensions:
  * Unaccent Extension
* redis
* node
* npm
* yarn 
* SO Packages:
    * locales
    * locales-all

## Overriding php configuration

### Overriding php ini default configurations
In order to override .ini configurations, use the `custom.ini` file in `./docker/php/conf.d` directory on the root of the project.
After changing the file, you will need to rebuild your container: `docker-compose up -d --build php`.

> It's recommended to change `upload_max_filesize`, `post_max_size`. Have in mind this values should be changed in all environments.

> It's recommended to change `memory_limit` if you need it. In this case, only in dev environments.

### Overriding extensions configuration
In order to override any extension setting you should be able to put the corresponding `.ini` file inside `/docker/php/conf.d` directory on the root of the project.
After changing the file, you will need to rebuild your container: `docker-compose up -d --build php`.

> Remember, by convention any extension config file should be named as `docker-php-ext-`` followed by the extension name itself.

For example, if you wish to override `opcache` (enabled by default) you should create the following file:
`./docker/php/conf.d/docker-php-ext-opcache.ini` and fill it with everything you need.

If you wish to override `xdebug` (disabled by default), then create this file `./docker/php/conf.d/docker-php-ext-xdebug.ini` and change the needed configurations.

### Notes
```
If you want to test different configurations, you can mount the files in conf.d as volumes. That way, restarting apache or the container will use the new configurations.
To do so, you can add this (as an example) to the volumes configured on docker-compose.yml:

- .:/docker/php/conf.d/custom.ini:/usr/local/etc/php/conf.d/custom.ini:ro

After finishing with the tests and changes, please remove the volume configuration and rebuild the container with the new configs.
```
