Installation
============

There are multiple ways to include the PHP parser into your project:

Installing from the Zip- or Tarball
-----------------------------------

Download the latest version from [the download page][2], unpack it and move the files somewhere into your project.

Installing via Composer
-----------------------

Create a `composer.json` file in your project root and use it to define your dependencies:

    {
        "require": {
            "nikic/php-parser": "0.9.5"
        }
    }

Then install Composer in your project (or [download the composer.phar][1] directly):

    curl -s http://getcomposer.org/installer | php

And finally ask Composer to install the dependencies:

    php composer.phar install

Installing as a PEAR package
----------------------------

Run the following two commands:

    pear channel-discover nikic.github.com/pear
    pear install nikic/PHPParser-0.9.5

Installing as a Git Submodule
-----------------------------

Run the following command to install the parser into the `vendor/PHP-Parser` folder:

    git submodule add git://github.com/nikic/PHP-Parser.git vendor/PHP-Parser



 [1]: http://getcomposer.org/composer.phar
 [2]: https://github.com/nikic/PHP-Parser/tags