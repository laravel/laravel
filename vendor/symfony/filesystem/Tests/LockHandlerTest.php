<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Filesystem\Tests;

use Symfony\Component\Filesystem\LockHandler;

class LockHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     * @expectedExceptionMessage Failed to create "/a/b/c/d/e": mkdir(): Permission denied.
     */
    public function testConstructWhenRepositoryDoesNotExist()
    {
        if (!getenv('USER') || 'root' === getenv('USER')) {
            $this->markTestSkipped('This test will fail if run under superuser');
        }
        new LockHandler('lock', '/a/b/c/d/e');
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     * @expectedExceptionMessage The directory "/" is not writable.
     */
    public function testConstructWhenRepositoryIsNotWriteable()
    {
        if (!getenv('USER') || 'root' === getenv('USER')) {
            $this->markTestSkipped('This test will fail if run under superuser');
        }
        new LockHandler('lock', '/');
    }

    public function testConstructSanitizeName()
    {
        $lock = new LockHandler('<?php echo "% hello word ! %" ?>');

        $file = sprintf('%s/sf.-php-echo-hello-word-.4b3d9d0d27ddef3a78a64685dda3a963e478659a9e5240feaf7b4173a8f28d5f.lock', sys_get_temp_dir());
        // ensure the file does not exist before the lock
        @unlink($file);

        $lock->lock();

        $this->assertFileExists($file);

        $lock->release();
    }

    public function testLockRelease()
    {
        $name = 'symfony-test-filesystem.lock';

        $l1 = new LockHandler($name);
        $l2 = new LockHandler($name);

        $this->assertTrue($l1->lock());
        $this->assertFalse($l2->lock());

        $l1->release();

        $this->assertTrue($l2->lock());
        $l2->release();
    }

    public function testLockTwice()
    {
        $name = 'symfony-test-filesystem.lock';

        $lockHandler = new LockHandler($name);

        $this->assertTrue($lockHandler->lock());
        $this->assertTrue($lockHandler->lock());

        $lockHandler->release();
    }

    public function testLockIsReleased()
    {
        $name = 'symfony-test-filesystem.lock';

        $l1 = new LockHandler($name);
        $l2 = new LockHandler($name);

        $this->assertTrue($l1->lock());
        $this->assertFalse($l2->lock());

        $l1 = null;

        $this->assertTrue($l2->lock());
        $l2->release();
    }
}
