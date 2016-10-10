<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require 'SharedConfigurations.php';

// When you have a whole set of consecutive commands to send to
// a redis server, you can use a pipeline to improve performances.

$client = new Predis\Client($single_server);

$replies = $client->pipeline(function ($pipe) {
    $pipe->ping();
    $pipe->flushdb();
    $pipe->incrby('counter', 10);
    $pipe->incrby('counter', 30);
    $pipe->exists('counter');
    $pipe->get('counter');
    $pipe->mget('does_not_exist', 'counter');
});

print_r($replies);

/* OUTPUT:
Array
(
    [0] => 1
    [1] => 1
    [2] => 10
    [3] => 40
    [4] => 1
    [5] => 40
    [6] => Array
        (
            [0] =>
            [1] => 40
        )

)
*/
