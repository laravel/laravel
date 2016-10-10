# whoops 
php errors for cool kids

[![Build Status](https://travis-ci.org/filp/whoops.png?branch=master)](https://travis-ci.org/filp/whoops) [![Total Downloads](https://poser.pugx.org/filp/whoops/downloads.png)](https://packagist.org/packages/filp/whoops)  [![Latest Stable Version](https://poser.pugx.org/filp/whoops/v/stable.png)](https://packagist.org/packages/filp/whoops)


-----

![Whoops!](http://i.imgur.com/xiZ1tUU.png)

**whoops** is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

## (current) Features

- Flexible, stack-based error handling
- Stand-alone library with (currently) no required dependencies
- Simple API for dealing with exceptions, trace frames & their data
- Includes a pretty rad error page for your webapp projects
- **NEW** Includes the ability to open referenced files directly in your editor and IDE
- Includes a Silex Service Provider for painless integration with [Silex](http://silex.sensiolabs.org/)
- Includes a Phalcon Service Provider for painless integration with [Phalcon](http://phalconphp.com/)
- Includes a Module for equally painless integration with [Zend Framework 2](http://framework.zend.com/)
- Easy to extend and integrate with existing libraries
- Clean, well-structured & tested code-base (well, except `pretty-template.php`, for now...)

## Installing

- Install [Composer](http://getcomposer.org) and place the executable somewhere in your `$PATH` (for the rest of this README,
I'll reference it as just `composer`)

- Add `filp/whoops` to your project's `composer.json` file:

```json
{
    "require": {
        "filp/whoops": "1.*"
    }
}
```

- Install/update your dependencies

```bash
$ cd my_project
$ composer install
```

And you're good to go! Have a look at the **example files** in `examples/` to get a feel for how things work.
I promise it's really simple!

## API Documentation

Initial API documentation of the whoops library is available here:
https://github.com/filp/whoops/wiki/API-Documentation

## Usage

### Integrating with Silex

**whoops** comes packaged with a Silex Service Provider: `Whoops\Provider\Silex\WhoopsServiceProvider`. Using it
in your existing Silex project is easy:

```php

require 'vendor/autoload.php';

use Silex\Application;

// ... some awesome code here ...

if($app['debug']) {
    $app->register(new Whoops\Provider\Silex\WhoopsServiceProvider);
}

// ...

$app->run();
```

And that's about it. By default, you'll get the pretty error pages if something goes awry in your development
environment, but you also have full access to the **whoops** library, obviously. For example, adding a new handler
into your app is as simple as extending `whoops`:

```php
$app['whoops'] = $app->extend('whoops', function($whoops) {
    $whoops->pushHandler(new DeleteWholeProjectHandler);
    return $whoops;
});
```
### Integrating with Phalcon

**whoops** comes packaged with a Phalcon Service Provider: `Whoops\Provider\Phalcon\WhoopsServiceProvider`. Using it
in your existing Phalcon project is easy. The provider uses the default Phalcon DI unless you pass a DI instance into the constructor.

```php
new Whoops\Provider\Phalcon\WhoopsServiceProvider;

// --- or ---

$di = Phalcon\DI\FactoryDefault;
new Whoops\Provider\Phalcon\WhoopsServiceProvider($di);
```

### Integrating with Laravel 4/Illuminate

If you're using Laravel 4, as of [this commit to laravel/framework](https://github.com/laravel/framework/commit/64f3a79aae254b71550a8097880f0b0e09062d24), you're already using Whoops! Yay!

### Integrating with Laravel 3

User [@hugomrdias](https://github.com/hugomrdias) contributed a simple guide/example to help you integrate **whoops** with Laravel 3's IoC container, available at:

https://gist.github.com/hugomrdias/5169713#file-start-php

### Integrating with Zend Framework 2

User [@zsilbi](https://github.com/zsilbi) contributed a provider for ZF2 integration,
available in the following location:

https://github.com/filp/whoops/tree/master/src/Whoops/Provider/Zend

**Instructions:**

- Add Whoops as a module to you app (/vendor/Whoops)
- Whoops must be the first module:

```php
'modules' => array(
        'Whoops',
        'Application'
   )
```

- Move Module.php from /Whoops/Provider/Zend/Module.php to /Whoops/Module.php
- Use optional configurations in your controller config:

```php
return array(
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'json_exceptions' => array(
            'display' => true,
            'ajax_only' => true,
            'show_trace' => true
        )
    ),
);
```

- NOTE: ob_clean(); is used to remove previous output, so you may use ob_start(); at the beginning of your app (index.php)

### Opening referenced files with your favorite editor or IDE

When using the pretty error page feature, whoops comes with the ability to
open referenced files directly in your IDE or editor.

```php
<?php

use Whoops\Handler\PrettyPageHandler;

$handler = new PrettyPageHandler;
$handler->setEditor('sublime');
```

The following editors are currently supported by default.

- `sublime`  - Sublime Text 2
- `emacs`    - Emacs
- `textmate` - Textmate
- `macvim`   - MacVim
- `xdebug`   - xdebug (uses [xdebug.file_link_format](http://xdebug.org/docs/all_settings#file_link_format))

Adding your own editor is simple:

```php

$handler->setEditor(function($file, $line) {
    return "whatever://open?file=$file&line=$line";
});

```

You can add PhpStorm support with [PhpStormOpener](https://github.com/pinepain/PhpStormOpener#phpstormopener) (Mac OS X only):
```php

$handler->setEditor(
    function ($file, $line) {
        // if your development server is not local it's good to map remote files to local
        $translations = array('^' . __DIR__ => '~/Development/PhpStormOpener'); // change to your path
        
        foreach ($translations as $from => $to) {
            $file = preg_replace('#' . $from . '#', $to, $file, 1);
        }

        return "pstorm://$file:$line";
    }
);

```

### Available Handlers

**whoops** currently ships with the following built-in handlers, available in the `Whoops\Handler` namespace:

- [`PrettyPageHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/PrettyPageHandler.php) - Shows a pretty error page when something goes pants-up
- [`CallbackHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/CallbackHandler.php) - Wraps a closure or other callable as a handler. You do not need to use this handler explicitly, **whoops** will automatically wrap any closure or callable you pass to `Whoops\Run::pushHandler`
- [`JsonResponseHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/JsonResponseHandler.php) - Captures exceptions and returns information on them as a JSON string. Can be used to, for example, play nice with AJAX requests.

## Contributing

If you want to give me some feedback or make a suggestion, send me a message through
twitter: [@imfilp](https://twitter.com/imfilp)

If you want to get your hands dirty, great! Here's a couple of steps/guidelines:

- Fork/clone this repo, and update dev dependencies using Composer

```bash
$ git clone git@github.com:filp/whoops.git
$ cd whoops
$ composer install --dev
```

- Create a new branch for your feature or fix

```bash
$ git checkout -b feature/flames-on-the-side
```

- Add your changes & tests for those changes (in `tests/`).
- Remember to stick to the existing code style as best as possible. When in doubt, follow `PSR-2`.
- Send me a pull request!

If you don't want to go through all this, but still found something wrong or missing, please
let me know, and/or **open a new issue report** so that I or others may take care of it.

## Authors

This library was primarily developed by [Filipe Dobreira](https://github.com/filp).

A lot of awesome fixes and enhancements were also sent in by contributors, which you can find **[in this page right here](https://github.com/filp/whoops/contributors)**.


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/filp/whoops/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

