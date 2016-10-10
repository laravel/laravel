<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Autoloader and dependency injection initialization for Swift Mailer.
 */

if (class_exists('Swift', false)) {
    return;
}

// Load Swift utility class
require dirname(__FILE__).'/classes/Swift.php';

if (!function_exists('_swiftmailer_init')) {
    function _swiftmailer_init()
    {
        require dirname(__FILE__).'/swift_init.php';
    }
}

// Start the autoloader and lazy-load the init script to set up dependency injection
Swift::registerAutoload('_swiftmailer_init');
