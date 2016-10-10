<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\File\MimeType;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\FileBinaryMimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class MimeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $path;

    public function testGuessImageWithoutExtension()
    {
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        }
    }

    public function testGuessImageWithDirectory()
    {
        $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException');

        MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/directory');
    }

    public function testGuessImageWithFileBinaryMimeTypeGuesser()
    {
        $guesser = MimeTypeGuesser::getInstance();
        $guesser->register(new FileBinaryMimeTypeGuesser());
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test'));
        }
    }

    public function testGuessImageWithKnownExtension()
    {
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('image/gif', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test.gif'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/test.gif'));
        }
    }

    public function testGuessFileWithUnknownExtension()
    {
        if (extension_loaded('fileinfo')) {
            $this->assertEquals('application/octet-stream', MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/.unknownextension'));
        } else {
            $this->assertNull(MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/.unknownextension'));
        }
    }

    public function testGuessWithIncorrectPath()
    {
        $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException');
        MimeTypeGuesser::getInstance()->guess(__DIR__.'/../Fixtures/not_here');
    }

    public function testGuessWithNonReadablePath()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Can not verify chmod operations on Windows');
        }

        if (in_array(get_current_user(), array('root'))) {
            $this->markTestSkipped('This test will fail if run under superuser');
        }

        $path = __DIR__.'/../Fixtures/to_delete';
        touch($path);
        @chmod($path, 0333);

        if (get_current_user() != 'root' && substr(sprintf('%o', fileperms($path)), -4) == '0333') {
            $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException');
            MimeTypeGuesser::getInstance()->guess($path);
        } else {
            $this->markTestSkipped('Can not verify chmod operations, change of file permissions failed');
        }
    }

    public static function tearDownAfterClass()
    {
        $path = __DIR__.'/../Fixtures/to_delete';
        if (file_exists($path)) {
            @chmod($path, 0666);
            @unlink($path);
        }
    }
}
