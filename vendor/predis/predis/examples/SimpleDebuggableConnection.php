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

use Predis\Command\CommandInterface;
use Predis\Connection\StreamConnection;

class SimpleDebuggableConnection extends StreamConnection
{
    private $tstart = 0;
    private $debugBuffer = array();

    public function connect()
    {
        $this->tstart = microtime(true);

        parent::connect();
    }

    private function storeDebug(CommandInterface $command, $direction)
    {
        $firtsArg  = $command->getArgument(0);
        $timestamp = round(microtime(true) - $this->tstart, 4);

        $debug  = $command->getId();
        $debug .= isset($firtsArg) ? " $firtsArg " : ' ';
        $debug .= "$direction $this";
        $debug .= " [{$timestamp}s]";

        $this->debugBuffer[] = $debug;
    }

    public function writeCommand(CommandInterface $command)
    {
        parent::writeCommand($command);

        $this->storeDebug($command, '->');
    }

    public function readResponse(CommandInterface $command)
    {
        $reply = parent::readResponse($command);
        $this->storeDebug($command, '<-');

        return $reply;
    }

    public function getDebugBuffer()
    {
        return $this->debugBuffer;
    }
}

$options = array(
    'connections' => array(
        'tcp' => 'SimpleDebuggableConnection',
    ),
);

$client = new Predis\Client($single_server, $options);
$client->set('foo', 'bar');
$client->get('foo');
$client->info();

print_r($client->getConnection()->getDebugBuffer());

/* OUTPUT:
Array
(
    [0] => SELECT 15 -> 127.0.0.1:6379 [0.0008s]
    [1] => SELECT 15 <- 127.0.0.1:6379 [0.0012s]
    [2] => SET foo -> 127.0.0.1:6379 [0.0014s]
    [3] => SET foo <- 127.0.0.1:6379 [0.0014s]
    [4] => GET foo -> 127.0.0.1:6379 [0.0016s]
    [5] => GET foo <- 127.0.0.1:6379 [0.0018s]
    [6] => INFO -> 127.0.0.1:6379 [0.002s]
    [7] => INFO <- 127.0.0.1:6379 [0.0025s]
)
*/
