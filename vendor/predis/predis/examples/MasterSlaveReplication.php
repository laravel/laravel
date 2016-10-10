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

// Predis supports master / slave replication scenarios where write operations are
// performed on the master server and read operations are executed against one of
// the slaves. The behaviour of commands or EVAL scripts can be customized at will.
// As soon as a write operation is performed, all the subsequent requests (reads
// or writes) will be served by the master server.
//
// This example must be executed with the second Redis server acting as the slave
// of the first one using the SLAVEOF command.
//

$parameters = array(
    'tcp://127.0.0.1:6379?database=15&alias=master',
    'tcp://127.0.0.1:6380?database=15&alias=slave',
);

$options = array('replication' => true);

$client = new Predis\Client($parameters, $options);

// Read operation.
$exists = $client->exists('foo') ? 'yes' : 'no';
$current = $client->getConnection()->getCurrent()->getParameters();
echo "Does 'foo' exist on {$current->alias}? $exists.\n";

// Write operation.
$client->set('foo', 'bar');
$current = $client->getConnection()->getCurrent()->getParameters();
echo "Now 'foo' has been set to 'bar' on {$current->alias}!\n";

// Read operation.
$bar = $client->get('foo');
$current = $client->getConnection()->getCurrent()->getParameters();
echo "We just fetched 'foo' from {$current->alias} and its value is '$bar'.\n";

/* OUTPUT:
Does 'foo' exist on slave? yes.
Now 'foo' has been set to 'bar' on master!
We just fetched 'foo' from master and its value is 'bar'.
*/
