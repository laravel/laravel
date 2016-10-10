<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Client/Server.php';

use GuzzleHttp\Tests\Ring\Client\Server;

Server::start();

register_shutdown_function(function () {
    Server::stop();
});
