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

// Operations such as LRANGE, ZRANGE and others can potentially generate replies
// containing a huge number of items. In some corner cases, such replies might
// end up exhausting the maximum allowed memory allocated for a PHP process.
// Multibulk iterators can be handy because they allow you to stream multibulk
// replies using plain old PHP iterators, making it possible to iterate them with
// a classic `foreach` loop and avoiding to consume an excessive amount of memory.
//
// PS: please note that multibulk iterators are supported only by the standard
// connection backend class (Predis\Connection\StreamConnection) and not the
// phpiredis-based one (Predis\Connection\PhpiredisConnection).

// Create a client and force the connection to use iterable multibulk responses.
$client = new Predis\Client($single_server + array('iterable_multibulk' => true));

// Prepare an hash with some fields and their respective values.
$client->hmset('metavars', array('foo' => 'bar', 'hoge' => 'piyo', 'lol' => 'wut'));

// By default multibulk iterators iterate over the reply as a list of items...
foreach ($client->hgetall('metavars') as $index => $item) {
    echo "[$index] $item\n";
}

/* OUTPUT:
[0] foo
[1] bar
[2] hoge
[3] piyo
[4] lol
[5] wut
*/

// ... but certain multibulk replies are better represented as lists of tuples.
foreach ($client->hgetall('metavars')->asTuple() as $index => $kv) {
    list($key, $value) = $kv;

    echo "[$index] $key => $value\n";
}

/* OUTPUT:
[0] foo => bar
[1] hoge => piyo
[2] lol => wut
*/
