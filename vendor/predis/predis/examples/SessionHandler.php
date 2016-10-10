<?php

require 'SharedConfigurations.php';

// This example demonstrates how to leverage Predis to save PHP sessions on Redis.
//
// The value of `session.gc_maxlifetime` in `php.ini` will be used by default as the
// the TTL for keys holding session data on Redis, but this value can be overridden
// when creating the session handler instance with the `gc_maxlifetime` option.
//
// Note that this class needs PHP >= 5.4 but can be used on PHP 5.3 if a polyfill for
// SessionHandlerInterface (see http://www.php.net/class.sessionhandlerinterface.php)
// is provided either by you or an external package like `symfony/http-foundation`.

if (!interface_exists('SessionHandlerInterface')) {
    die("ATTENTION: the session handler implemented by Predis needs PHP >= 5.4.0 or a polyfill ".
        "for \SessionHandlerInterface either provided by you or an external package.\n");
}

// Instantiate a new client just like you would normally do. We'll prefix our session keys here.
$client = new Predis\Client($single_server, array('prefix' => 'sessions:'));

// Set `gc_maxlifetime` so that a session will be expired after 5 seconds since last access.
$handler = new Predis\Session\SessionHandler($client, array('gc_maxlifetime' => 5));

// Register our session handler (it uses `session_set_save_handler()` internally).
$handler->register();

// Set a fixed session ID just for the sake of our example.
session_id('example_session_id');

session_start();

if (isset($_SESSION['foo'])) {
    echo "Session has `foo` set to {$_SESSION['foo']}\n";
} else {
    $_SESSION['foo'] = $value = mt_rand();
    echo "Empty session, `foo` has been set with $value\n";
}
