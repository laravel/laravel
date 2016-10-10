<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

/**
 * @group commands
 * @group realm-scripting
 */
class ServerScriptTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerScript';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SCRIPT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('EXISTS', '9d0c0826bde023cc39eebaaf832c32a890f3b088', 'ffffffffffffffffffffffffffffffffffffffff');
        $expected = array('EXISTS', '9d0c0826bde023cc39eebaaf832c32a890f3b088', 'ffffffffffffffffffffffffffffffffffffffff');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertTrue($this->getCommand()->parseResponse(true));
    }

    /**
     * @group connected
     * @todo We should probably convert integers to booleans.
     */
    public function testExistsReturnAnArrayOfValues()
    {
        $redis = $this->getClient();

        $redis->eval($lua = 'return true', 0);
        $sha1 = sha1($lua);

        $this->assertSame(array(1, 0), $redis->script('EXISTS', $sha1, 'ffffffffffffffffffffffffffffffffffffffff'));
    }

    /**
     * @group connected
     */
    public function testLoadReturnsHashOfScripts()
    {
        $redis = $this->getClient();

        $lua = 'return true';
        $sha1 = sha1($lua);

        $this->assertSame($sha1, $redis->script('LOAD', $lua));
    }

    /**
     * @group connected
     */
    public function testFlushesExistingScripts()
    {
        $redis = $this->getClient();

        $sha1 = $redis->script('LOAD', 'return true');

        $this->assertTrue($redis->script('FLUSH'));
        $this->assertSame(array(0), $redis->script('EXISTS', $sha1));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnInvalidSubcommand()
    {
        $redis = $this->getClient();

        $redis->script('INVALID');
    }
}
