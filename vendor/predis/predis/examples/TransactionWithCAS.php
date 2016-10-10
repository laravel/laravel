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

// This is an implementation of an atomic client-side ZPOP using the support for
// check-and-set (CAS) operations with MULTI/EXEC transactions, as described in
// "WATCH explained" from http://redis.io/topics/transactions
//
// First, populate your database with a tiny sample data set:
//
// ./redis-cli
// SELECT 15
// ZADD zset 1 a
// ZADD zset 2 b
// ZADD zset 3 c

function zpop($client, $key)
{
    $element = null;
    $options = array(
        'cas'   => true,    // Initialize with support for CAS operations
        'watch' => $key,    // Key that needs to be WATCHed to detect changes
        'retry' => 3,       // Number of retries on aborted transactions, after
                            // which the client bails out with an exception.
    );

    $client->transaction($options, function ($tx) use ($key, &$element) {
        @list($element) = $tx->zrange($key, 0, 0);

        if (isset($element)) {
            $tx->multi();   // With CAS, MULTI *must* be explicitly invoked.
            $tx->zrem($key, $element);
        }
    });

    return $element;
}

$client = new Predis\Client($single_server);
$zpopped = zpop($client, 'zset');

echo isset($zpopped) ? "ZPOPed $zpopped" : "Nothing to ZPOP!", "\n";
