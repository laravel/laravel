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

// redis can set keys and their relative values in one go
// using MSET, then the same values can be retrieved with
// a single command using MGET.

$mkv = array(
    'usr:0001' => 'First user',
    'usr:0002' => 'Second user',
    'usr:0003' => 'Third user'
);

$client = new Predis\Client($single_server);

$client->mset($mkv);
$retval = $client->mget(array_keys($mkv));

print_r($retval);

/* OUTPUT:
Array
(
    [0] => First user
    [1] => Second user
    [2] => Third user
)
*/
