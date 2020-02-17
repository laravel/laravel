# project-name

## Installation
1. Clone repository
2. `composer install`
3. `npm install` in node container
4. `composer build` 
5. `npm run dev` in node container for frontend dev server

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

## System Requirements
* php: 7.2.x
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
* postgres: 9.6.x / 10.x
* postgres extensions:
  * Unaccent Extension
* redis
* node
* npm
* yarn 
* SO Packages:
    * locales
    * locales-all

## System Configuration

* Remember that all sites must use HTTPS. And please choose a proper redirect:
    * http://www.site.com should redirect to https://www.site.com
    * http://site.com should redirect to https://www.site.com
    * https://site.com should redirect to https://www.site.com
    * The sub-domain (www), can be interchanged in the examples. But you must choose to use it and redirect TO it or not use it and redirect FROM it.
    * Please see docker/apache/default.conf for the server configuration to access the site and resources.

## Publish Assets
1. `php artisan vendor:publish --provider "Digbang\\Backoffice\\BackofficeServiceProvider" --tag assets --force`


## Folder Permissions
Permissions
```
// at the root of the project
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```
Fixes proxy error
```
// at the root of the project
mkdir proxies
chmod -R 755 proxies
chown www-data:www-data proxies/
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
