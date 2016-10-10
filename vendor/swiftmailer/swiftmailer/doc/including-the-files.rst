Including Swift Mailer (Autoloading)
====================================

If you are using Composer, Swift Mailer will be automatically autoloaded.

If not, you can use the built-in autoloader by requiring the
``swift_required.php`` file::

    require_once '/path/to/swift-mailer/lib/swift_required.php';

    /* rest of code goes here */

If you want to override the default Swift Mailer configuration, call the
``init()`` method on the ``Swift`` class and pass it a valid PHP callable (a
PHP function name, a PHP 5.3 anonymous function, ...)::

    require_once '/path/to/swift-mailer/lib/swift_required.php';

    function swiftmailer_configurator() {
        // configure Swift Mailer

        Swift_DependencyContainer::getInstance()->...
        Swift_Preferences::getInstance()->...
    }

    Swift::init('swiftmailer_configurator');

    /* rest of code goes here */

The advantage of using the ``init()`` method is that your code will be
executed only if you use Swift Mailer in your script.

.. note::

    While Swift Mailer's autoloader is designed to play nicely with other
    autoloaders, sometimes you may have a need to avoid using Swift Mailer's
    autoloader and use your own instead. Include the ``swift_init.php``
    instead of the ``swift_required.php`` if you need to do this. The very
    minimum include is the ``swift_init.php`` file since Swift Mailer will not
    work without the dependency injection this file sets up:

    .. code-block:: php

        require_once '/path/to/swift-mailer/lib/swift_init.php';

        /* rest of code goes here */
