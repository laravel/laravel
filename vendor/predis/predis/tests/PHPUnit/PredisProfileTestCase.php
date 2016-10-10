<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Profile;

use PredisTestCase;

/**
 *
 */
abstract class PredisProfileTestCase extends PredisTestCase
{
    /**
     * Returns a new instance of the tested profile.
     *
     * @param  string                 $version Version of Redis.
     * @return ServerProfileInterface
     */
    protected function getProfile($version = null)
    {
        $this->markTestIncomplete("Server profile must be defined in ".get_class($this));
    }

    /**
     * Returns the expected version string for the tested profile.
     *
     * @return string Version string.
     */
    abstract protected function getExpectedVersion();

    /**
     * Returns the expected list of commands supported by the tested profile.
     *
     * @return array List of supported commands.
     */
    abstract protected function getExpectedCommands();

    /**
     * Returns the list of commands supported by the current
     * server profile.
     *
     * @param  ServerProfileInterface $profile Server profile instance.
     * @return array
     */
    protected function getCommands(ServerProfileInterface $profile)
    {
        $commands = $profile->getSupportedCommands();

        return array_keys($commands);
    }

    /**
     * @group disconnected
     */
    public function testGetVersion()
    {
        $profile = $this->getProfile();

        $this->assertEquals($this->getExpectedVersion(), $profile->getVersion());
    }

    /**
     * @group disconnected
     */
    public function testSupportedCommands()
    {
        $profile = $this->getProfile();
        $expected = $this->getExpectedCommands();
        $commands = $this->getCommands($profile);

        $this->assertSame($expected, $commands);
    }
}
