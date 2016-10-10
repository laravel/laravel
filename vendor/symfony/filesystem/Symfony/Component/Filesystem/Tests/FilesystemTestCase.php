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

class FilesystemTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string $workspace
     */
    protected $workspace = null;

    protected static $symlinkOnWindows = null;

    public static function setUpBeforeClass()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            static::$symlinkOnWindows = true;
            $originDir = tempnam(sys_get_temp_dir(), 'sl');
            $targetDir = tempnam(sys_get_temp_dir(), 'sl');
            if (true !== @symlink($originDir, $targetDir)) {
                $report = error_get_last();
                if (is_array($report) && false !== strpos($report['message'], 'error code(1314)')) {
                    static::$symlinkOnWindows = false;
                }
            }
        }
    }

    public function setUp()
    {
        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    public function tearDown()
    {
        $this->clean($this->workspace);
    }

    /**
     * @param string $file
     */
    protected function clean($file)
    {
        if (is_dir($file) && !is_link($file)) {
            $dir = new \FilesystemIterator($file);
            foreach ($dir as $childFile) {
                $this->clean($childFile);
            }

            rmdir($file);
        } else {
            unlink($file);
        }
    }

    /**
     * @param int $expectedFilePerms expected file permissions as three digits (i.e. 755)
     * @param string $filePath
     */
    protected function assertFilePermissions($expectedFilePerms, $filePath)
    {
        $actualFilePerms = (int) substr(sprintf('%o', fileperms($filePath)), -3);
        $this->assertEquals(
            $expectedFilePerms,
            $actualFilePerms,
            sprintf('File permissions for %s must be %s. Actual %s', $filePath, $expectedFilePerms, $actualFilePerms)
        );
    }

    protected function getFileOwner($filepath)
    {
        $this->markAsSkippedIfPosixIsMissing();

        $infos = stat($filepath);
        if ($datas = posix_getpwuid($infos['uid'])) {
            return $datas['name'];
        }
    }

    protected function getFileGroup($filepath)
    {
        $this->markAsSkippedIfPosixIsMissing();

        $infos = stat($filepath);
        if ($datas = posix_getgrgid($infos['gid'])) {
            return $datas['name'];
        }
    }

    protected function markAsSkippedIfSymlinkIsMissing()
    {
        if (!function_exists('symlink')) {
            $this->markTestSkipped('symlink is not supported');
        }

        if (defined('PHP_WINDOWS_VERSION_MAJOR') && false === static::$symlinkOnWindows) {
            $this->markTestSkipped('symlink requires "Create symbolic links" privilege on windows');
        }
    }

    protected function markAsSkippedIfChmodIsMissing()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('chmod is not supported on windows');
        }
    }

    protected function markAsSkippedIfPosixIsMissing()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR') || !function_exists('posix_isatty')) {
            $this->markTestSkipped('Posix is not supported');
        }
    }
}
