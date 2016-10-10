Class Preloader for PHP
=======================

This tool is used to generate a single PHP script containing all of the classes
required for a specific use case. Using a single compiled PHP script instead of relying on autoloading can help to improve the performance of specific use cases. For example, if your application executes the same bootstrap code on every request, then you could generate a preloader (the compiled output of this tool) to reduce the cost of autoloading the required classes over and over.

What it actually does
---------------------

This tool listens for each file that is autoloaded, creates a list of files, traverses the parsed PHP file using [PHPParser](https://github.com/nikic/PHP-Parser) and any visitors of a Config object, wraps the code of each file in a namespace block if necessary, and writes the contents of every autoloaded file (in order) to a single PHP file.

Notice
------

This tool should only be used for specific use cases. There is a tradeoff between preloading classes and autoloading classes. The point at which it is no longer beneficial to generate a preloader is application specific. You'll need to perform your own benchmarks to determine if this tool will speed up your application.

Installation
------------

Add the ClassPreloader as a dependency to your composer.json file:

```javascript
{
    "require": {
        "classpreloader/classpreloader": "1.0.*"
    },
    "config": {
        "bin-dir": "bin"
    }
}
```

Using the tool
--------------

You use the bin/classpreloader.php compile command with a few command line flags to generate a preloader.

`--config`: A CSV containing a list of files to combine into a classmap, or the full path to a PHP script that returns an array of classes or a `\ClassPreloader\Config` object.

`--output`: The path to the file to store the compiled PHP code. If the directory does not exist, the tool will attempt to create it.

`--fix_dir`: (defaults to 1) Set to 0 to not replace "__DIR__" constants with the actual directory of the original file.

`--fix_file`: (defaults to 1) Set to 0 to not replace "__FILE__" constants with the actual location of the original file.

Writing a config file
---------------------

Creating a PHP based configuration file is fairly simple. Just include the vendor/classpreloader/classpreloader/src/ClassPreloader/ClassLoader.php file and call the `ClassLoader::getIncludes()` method, passing a function as the only  argument. This function should accept a `ClassLoader` object and register the passed in object's autoloader using `$loader->register()`. It is important to register the `ClassLoader` autoloader after all other autoloaders are registered.

An array or `\ClassPreloader\Config` must be returned from the config file. You can attach custom node visitors if you need to perform any sort of translation on each matching file before writing it to the output.

```php
<?php
// Here's an example of creating a preloader for using Amazon DynamoDB and the
// AWS SDK for PHP 2.

require __DIR__ . '/src/ClassPreloader/ClassLoader.php';

use ClassPreloader\ClassLoader;

$config = ClassLoader::getIncludes(function(ClassLoader $loader) {
    require __DIR__ . '/vendor/autoload.php';
    $loader->register();
    $aws = Aws\Common\Aws::factory(array(
        'key'    => '***',
        'secret' => '***',
        'region' => 'us-east-1'
    ));
    $client = $aws->get('dynamodb');
    $client->listTables()->getAll();
});

// Add a regex filter that requires all classes to match the regex
// $config->addInclusiveFilter('/Foo/');

// Add a regex filter that requires that a class does not match the filter
// $config->addExclusiveFilter('/Foo/');

return $config;
```

You would then run the classpreloader.php script and pass in the full path to the above PHP script.

`php bin/classpreloader.php compile --config="/path/to/the_example.php" --output="/tmp/preloader.php"`

The above command will create a file in /tmp/preloader.php that contains every file that was autoloaded while running the snippet of code in the anonymous function. You would generate this file and include it in your production script.

Automating the process with Composer
------------------------------------

You can automate the process of creating preloaders using Composer's script functionality. For example, if you wanted to automatically create a preloader each time the AWS SDK for PHP is installed, you could define a script like the following in your composer.json file:

```javascript
{
    "require": {
        "classpreloader/classpreloader": "1.0.*"
    },
    "scripts": {
        "post-autoload-dump": "php bin/classpreloader.php compile --config=/path/to/the_example.php --output=/path/to/preload.php"
    },
    "config": {
        "bin-dir": "bin"
    }
}
```

Using the above composer.json file, each time the project's autoloader is recreated using the install or update command, the classpreloader.php file will be executed. This script would generate a preload.php containing the classes required to run the previously demonstrated "the_example.php" configuration file.
