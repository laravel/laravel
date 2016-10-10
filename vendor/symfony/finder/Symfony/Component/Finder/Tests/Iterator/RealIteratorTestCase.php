<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\Iterator;

abstract class RealIteratorTestCase extends IteratorTestCase
{
    protected static $tmpDir;
    protected static $files;

    public static function setUpBeforeClass()
    {
        self::$tmpDir = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.'symfony2_finder';

        self::$files = array(
            '.git/',
            '.foo/',
            '.foo/.bar',
            '.foo/bar',
            '.bar',
            'test.py',
            'foo/',
            'foo/bar.tmp',
            'test.php',
            'toto/',
            'foo bar',
        );

        self::$files = self::toAbsolute(self::$files);

        if (is_dir(self::$tmpDir)) {
            self::tearDownAfterClass();
        } else {
            mkdir(self::$tmpDir);
        }

        foreach (self::$files as $file) {
            if (DIRECTORY_SEPARATOR === $file[strlen($file) - 1]) {
                mkdir($file);
            } else {
                touch($file);
            }
        }

        file_put_contents(self::toAbsolute('test.php'), str_repeat(' ', 800));
        file_put_contents(self::toAbsolute('test.py'), str_repeat(' ', 2000));

        touch(self::toAbsolute('foo/bar.tmp'), strtotime('2005-10-15'));
        touch(self::toAbsolute('test.php'), strtotime('2005-10-15'));
    }

    public static function tearDownAfterClass()
    {
        foreach (array_reverse(self::$files) as $file) {
            if (DIRECTORY_SEPARATOR === $file[strlen($file) - 1]) {
                @rmdir($file);
            } else {
                @unlink($file);
            }
        }
    }

    protected static function toAbsolute($files = null)
    {
        /*
         * Without the call to setUpBeforeClass() property can be null.
         */
        if (!self::$tmpDir) {
            self::$tmpDir = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.'symfony2_finder';
        }

        if (is_array($files)) {
            $f = array();
            foreach ($files as $file) {
                if (is_array($file)) {
                    $f[] = self::toAbsolute($file);
                } else {
                    $f[] = self::$tmpDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $file);
                }
            }

            return $f;
        }

        if (is_string($files)) {
            return self::$tmpDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $files);
        }

        return self::$tmpDir;
    }

    protected static function toAbsoluteFixtures($files)
    {
        $f = array();
        foreach ($files as $file) {
            $f[] = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.$file);
        }

        return $f;
    }
}
