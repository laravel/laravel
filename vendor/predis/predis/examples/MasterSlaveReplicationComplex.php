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

// Predis allows to set Lua scripts as read-only operations in the context of
// replication. This works for both EVAL and EVALSHA and also for the client-side
// abstraction built upon them (Predis\Command\ScriptedCommand). This example
// shows a slightly more complex configuration that injects a new scripted command
// in the server profile used by the new client instance and marks it marks it as
// a read-only operation for replication so that it will be executed on slaves.

use Predis\Command\ScriptedCommand;
use Predis\Connection\MasterSlaveReplication;
use Predis\Replication\ReplicationStrategy;

// ------------------------------------------------------------------------- //

// Define a new scripted command that returns all the fields
// of a variable number of hashes with a single roundtrip.

class HashMultipleGetAll extends ScriptedCommand
{
    const BODY = <<<EOS
local hashes = {}
for _, key in pairs(KEYS) do
    table.insert(hashes, key)
    table.insert(hashes, redis.call('hgetall', key))
end
return hashes
EOS;

    public function getScript()
    {
        return self::BODY;
    }
}

// ------------------------------------------------------------------------- //

$parameters = array(
    'tcp://127.0.0.1:6379/?alias=master',
    'tcp://127.0.0.1:6380/?alias=slave',
);

$options = array(
    'profile' => function ($options, $option) {
        $profile = $options->getDefault($option);
        $profile->defineCommand('hmgetall', 'HashMultipleGetAll');

        return $profile;
    },
    'replication' => function ($options) {
        $strategy = new ReplicationStrategy();
        $strategy->setScriptReadOnly(HashMultipleGetAll::BODY);

        $replication = new MasterSlaveReplication($strategy);

        return $replication;
    },
);

// ------------------------------------------------------------------------- //

$client = new Predis\Client($parameters, $options);

// Execute the following commands on the master server using redis-cli:
// $ ./redis-cli HMSET metavars foo bar hoge piyo
// $ ./redis-cli HMSET servers master host1 slave host2

$hashes = $client->hmgetall('metavars', 'servers');

$replication = $client->getConnection();
$stillOnSlave = $replication->getCurrent() === $replication->getConnectionById('slave');

echo "Is still on slave? ", $stillOnSlave ? 'YES' : 'NO', "!\n";
var_export($hashes);
