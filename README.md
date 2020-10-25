# Laravel Blueprint

This laravel blueprint intends to speed up your development.

## Features

This laravel blueprint has the following features / packages included.

- [Laradock](https://laradock.io)
- [Lighthouse](https://lighthouse-php.com) for GraphQL
- [Orchid](https://orchid.software/) as admin panel

## Installation

To install this blueprint you need to clone this repository.

```
git clone git@github.com:tjventurini/laravel-blueprint.git
```

Now copy the environment file of the laradock setup.

```
cp laravel-blueprint/laradock/.env.example laravel-blueprint/laradock/.env
```

Then you should go ahead and update the laradock configuration. Make sure to update the `DATA_PATH_HOST` and `COMPOSE_PROJECT_NAME` environment variables. Also you will have to set the `UID`s and `GID`s that match your host systems. You can find them out by running `id -a`.

```
vim laravel-blueprint/laradock/.env
```

Now it is time to boot up laradock. Your first boot can take some time since the docker containers need to be build.

```
cd laradock
docker-composer -d nginx php-fpm mysql workspace
```

Now you are ready to login to the laravel container using `zsh`.

```
docker-compose exec --user=laradock workspace zsh
```

This will directly bring you to the location of your laravel application. Now it's time to install the composer dependencies and to generate the application key. Composer is set to do this automatically after installation, so all what's left to do is to run the following well know command.

```
composer install
```