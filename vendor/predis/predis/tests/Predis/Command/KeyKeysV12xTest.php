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
 * We only perform disconnected tests for this commands because
 * it is too old (Redis v1.2) and expects a different response
 * format.
 *
 * @group commands
 * @group realm-key
 */
class KeyKeysV12xTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\KeyKeysV12x';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'KEYS';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('pattern:*');
        $expected = array('pattern:*');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = 'key1 key2 key3';
        $parsed = array('key1', 'key2', 'key3');

        $this->assertSame($parsed, $this->getCommand()->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('pattern');
        $expected = array('prefix:pattern');

        $command = $this->getCommandWithArgumentsArray($arguments);
        $command->prefixKeys('prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeysIgnoredOnEmptyArguments()
    {
        $command = $this->getCommand();
        $command->prefixKeys('prefix:');

        $this->assertSame(array(), $command->getArguments());
    }
}
