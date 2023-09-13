<!-- markdownlint-disable MD033 -->
<!-- markdownlint-disable-next-line MD041 -->
<p align="center"><a href="https://lightit.io" target="_blank"><img src="https://lightit.io/images/Logo_purple.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
<!-- markdownlint-enable MD033 -->

We help digital health startups, clinics, and medtech companies ideate, design, and develop custom web & mobile applications that transform the future of healthcare.

## Install

Requirements: Php >= 8.2.0 & Composer

- `brew install php@8.2 composer` Mac OS X with brew
- `apt-get install php8.2` Ubuntu with apt-get (use sudo if is necessary)

This step is not necessary when you use Docker.

### Techs

- Docker
  - Laravel Sail
- Laravel 10.X & Php 8.2
  - Tools
    - Clockwork Debug Bar
    - Ide Helper
    - Phpstan
    - Php ECS
    - Rector Php
    - XDebug
  - Single Action and Clean Controllers
  - Request Classes
  - Strict Mode
- Vite
- Postcss
- Prettier
- Typescript
- React 18
- Tailwindcss 3
- Mysql 8
- Redis
- Meilisearch
- Minio
- Mailpit
- Pest Php for Backend Testing
  - Coverage HTML Report
- Browser Testing with Dusk (using selenium)
- Git
  - PR Template
  - Issue Template
  - Git Hooks with CaptainHook

### Backend Installation

1. Clone GitHub repo for this project locally:

   ```bash
   git clone git@github.com:Light-it-labs/lightranet
   ```

2. cd into your project and create a copy of your .env file

   ```bash
   cd lightranet
   cp .env.example .env
   ```

3. If you have Php installed you can install composer dependencies with sail included with

    <!-- cspell: disable -->

   ```bash
   docker run --rm \
     -u "$(id -u):$(id -g)" \
     -v $(pwd):/var/www/html \
     -w /var/www/html \
     laravelsail/php82-composer:latest \
     composer install --ignore-platform-reqs
   ```

   <!-- cspell: enable -->

4. After that you can use laravel sail for running your project.

   ```bash
   sail up
   ```

   See more detail below.

5. When the app is running, into the bash container (`sail bash`) you can use the following commands:

   ```bash
   php artisan key:generate
   php artisan storage:link
   php artisan ide-helper:generate
   php artisan migrate --seed
   ```

### Frontend

You need nvm installed in your machine. <https://github.com/nvm-sh/nvm#install--update-script>

For frontend environment you need install npm dependencies with `npm install` and after that to compile assets for Frontend SPA in local you can run: `npm run dev` or `npm run watch`
In production environment is necessary run `npm run production`

1. Install npm dependencies:

   ```bash
   nvm use
   npm install
   ```

2. Run the app:

   ```bash
   npm run dev
   ```

### Hooks

You must activate the hooks in your local git repository. To do so, just run the following command.

```bash
vendor/bin/captainhook install
```

Executing this will create the hook script located in your .git/hooks directory, for each hook you choose to install while running the command. So now every time git triggers a hook, CaptainHook gets executed.

If you don't have PHP installed locally or you have installed a different version, you can use Docker to execute CaptainHook. To do so you must install the hooks a bit differently.

```bash
vendor/bin/captainhook install --run-mode=docker --run-exec="docker exec CONTAINER_NAME"
```

You can choose your preferred docker command e.g.:

```bash
docker exec MY_CONTAINER_NAME
docker run --rm -v $(pwd):/var/www/html MY_IMAGE_NAME
docker-compose -f docker/docker-compose.yml run --rm -T MY_SERVICE_NAME
```

If you want to know more you can see de documentation in the official page <https://captainhookphp.github.io/captainhook/>

## Running

We use Laravel Sail, is a light-weight command-line interface for interacting with Laravel's default Docker development environment. Sail provides a great starting point for building a Laravel application without requiring prior Docker experience.

### Configuring A Bash Alias

By default, Sail commands are invoked using the `vendor/bin/sail` script that is included with all new Laravel applications:

However, instead of repeatedly typing vendor/bin/sail to execute Sail commands, you may wish to configure a Bash alias that allows you to execute Sail's commands more easily:

```bash
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

### Starting & Stopping Sail

```bash
sail up
```

To start all the Docker containers in the background, you may start Sail in "detached" mode:

```bash
sail up -d
```

To stop all of the containers, you may simply press Control + C to stop the container's execution. Or, if the containers are running in the background, you may use the stop command:

```bash
sail stop
```

### Executing Commands

```bash
# Running Artisan commands locally...
php artisan queue:work

# Running Artisan commands within Laravel Sail...
sail artisan queue:work

# Executing PHP Commands
sail php script.php

# Executing Composer Commands
sail composer require laravel/sanctum

# Running Tests
sail test

# Running with Coverage
sail composer test
```

For more info <https://laravel.com/docs/10.x/sail>

## Php Standards

Run: `composer fixer` and execute php cs, php cs fixer, php stan and rector.

Read <https://lightit.slite.com/app/docs/rd0tnuQ5w>

## Testing

To run all test and generate report coverage you can use:
`sail composer test`

In computer science, code coverage is a measure used to describe the degree to which the source code of a program is tested by a particular test suite. A program with high code coverage has been more thoroughly tested and has a lower chance of containing software bugs than a program with low code coverage.

You can see the report open _index.html_ in the reports folder.

### Dusk Test

If you want to run Dusk test, first you need compile files with `vite build` and after that you can use `sail dusk` for running browser test.
_Important_: make sure the vite server is not running

## XDebug

_PHPStorm configuration_ <!-- markdownlint-disable-line MD036 -->

First open `Preferences > PHP > Debug.`
Just to make sure we will be able to catch the interrupt from xdebug, we just need to check the “Break at first line in PHP scripts”.
Next, we need to turn on `Run > Start Listening for PHP Debug Connection`.

Finally, let’s configure the server configuration in `Preferences > PHP > Servers`

Couple of things to ensure

- “Name” must be identical to the one set in the docker-compose.yml
- Check “Use path mappings”
- Map your local project folder to the docker folder. (`/var/www/html`)

That’s it. Create a breakpoint somewhere in the project and check that all is working. Once everything is working, you can remove the “Break at first line in PHP scripts”.

<https://laravel.com/docs/10.x/sail#debugging-with-xdebug>

## Debugbar

The web page is using a clockwork as debugbar, it is similar to debugbar from laravel, The most important difference between clockwork and debugbar (standard) is that clockwork only display information in console so, you need to open console (F12)

Full documentation of clockwork in this [link](https://underground.works/clockwork/).

It is mandatory to install plugin of clockwork in your browser check this [link](https://chrome.google.com/webstore/detail/clockwork/dmggabnehkmmfmdffgajcflpdjlnoemp/related?hl=es)

## HTTP Codes References

The next list contains the HTTP codes returned by the API and the meaning in the present context:

- HTTP 200 Ok: the request has been processed successfully.
- HTTP 201 Created: the resource has been created. It's associated with a POST Request.
- HTTP 204 No Content: the request has been processed successfully but does not need to return an entity-body.
- HTTP 400 Bad Request: the request could not been processed by the API. You should review the data sent to.
- HTTP 401 Unauthorized: When the request was performed to the login endpoint, means that credentials are not matching with any. When the request was performed to another endpoint means that the token it's not valid anymore due TTL expiration.
- HTTP 403 Forbidden: the credentials provided with the request has not the necessary permission to be processed.
- HTTP 404 Not Found: the endpoint requested does not exist in the API.
- HTTP 422: the payload sent to the API did not pass the validation process.
- HTTP 500: an unknown error was triggered during the process.

Please refer to <https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html> for reference

## Host aliases

To access services in local environment with host aliases, add the following aliases in the `/etc/hosts` file.

1. Edit the file with the following command:

   ```bash
   sudo nano /etc/hosts
   ```

2. Paste the following hosts aliases:

   ```bash
   127.0.0.1       lightranet.test
   127.0.0.1       db
   127.0.0.1       s3
   127.0.0.1       redis
   127.0.0.1       mailpit
   ```

## System Requirements

- php: 8.2.x
- php ini configurations:
  - `upload_max_filesize = 100M`
  - `post_max_size = 100M`
  - These numbers are illustrative. Set them according to your project needs.

## Emoji Guide

**For reviewers: Emojis can be added to comments to call out blocking versus non-blocking feedback.**

E.g: Praise, minor suggestions, or clarifying questions that don’t block merging the PR.

> 🟢 Nice refactor!

<!-- markdownlint-disable-line MD028 -->

> 🟡 Why was the default value removed?

E.g: Blocking feedback must be addressed before merging.

> 🔴 This change will break something important

|              |                |                                     |
| ------------ | -------------- | ----------------------------------- |
| Blocking     | 🔴 ❌ 🚨       | RED                                 |
| Non-blocking | 🟡 💡 🤔 💭    | Yellow, thinking, etc               |
| Praise       | 🟢 💚 😍 👍 🙌 | Green, hearts, positive emojis, etc |

## Links

- [Git Flow](https://lightit.slite.com/app/docs/SC8usN2Ju)
- [Handbook of good practices for reviewers in Code Reviews](https://lightit.slite.com/app/docs/ddNGohWthVB3fO)
