<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Normalizer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class DataUriNormalizerTest extends \PHPUnit_Framework_TestCase
{
    const TEST_GIF_DATA = 'data:image/gif;base64,R0lGODdhAQABAIAAAP///////ywAAAAAAQABAAACAkQBADs=';
    const TEST_TXT_DATA = 'data:text/plain,K%C3%A9vin%20Dunglas%0A';
    const TEST_TXT_CONTENT = "Kévin Dunglas\n";

    /**
     * @var DataUriNormalizer
     */
    private $normalizer;

    protected function setUp()
    {
        $this->normalizer = new DataUriNormalizer();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\NormalizerInterface', $this->normalizer);
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\DenormalizerInterface', $this->normalizer);
    }

    public function testSupportNormalization()
    {
        $this->assertFalse($this->normalizer->supportsNormalization(new \stdClass()));
        $this->assertTrue($this->normalizer->supportsNormalization(new \SplFileObject('data:,Hello%2C%20World!')));
    }

    /**
     * @requires extension fileinfo
     */
    public function testNormalizeHttpFoundationFile()
    {
        $file = new File(__DIR__.'/../Fixtures/test.gif');

        $this->assertSame(self::TEST_GIF_DATA, $this->normalizer->normalize($file));
    }

    /**
     * @requires extension fileinfo
     */
    public function testNormalizeSplFileInfo()
    {
        $file = new \SplFileInfo(__DIR__.'/../Fixtures/test.gif');

        $this->assertSame(self::TEST_GIF_DATA, $this->normalizer->normalize($file));
    }

    /**
     * @requires extension fileinfo
     */
    public function testNormalizeText()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/test.txt');

        $data = $this->normalizer->normalize($file);

        $this->assertSame(self::TEST_TXT_DATA, $data);
        $this->assertSame(self::TEST_TXT_CONTENT, file_get_contents($data));
    }

    public function testSupportsDenormalization()
    {
        $this->assertFalse($this->normalizer->supportsDenormalization('foo', 'Bar'));
        $this->assertTrue($this->normalizer->supportsDenormalization(self::TEST_GIF_DATA, 'SplFileInfo'));
        $this->assertTrue($this->normalizer->supportsDenormalization(self::TEST_GIF_DATA, 'SplFileObject'));
        $this->assertTrue($this->normalizer->supportsDenormalization(self::TEST_TXT_DATA, 'Symfony\Component\HttpFoundation\File\File'));
    }

    public function testDenormalizeSplFileInfo()
    {
        $file = $this->normalizer->denormalize(self::TEST_TXT_DATA, 'SplFileInfo');

        $this->assertInstanceOf('SplFileInfo', $file);
        $this->assertSame(file_get_contents(self::TEST_TXT_DATA), $this->getContent($file));
    }

    public function testDenormalizeSplFileObject()
    {
        $file = $this->normalizer->denormalize(self::TEST_TXT_DATA, 'SplFileObject');

        $this->assertInstanceOf('SplFileObject', $file);
        $this->assertEquals(file_get_contents(self::TEST_TXT_DATA), $this->getContent($file));
    }

    public function testDenormalizeHttpFoundationFile()
    {
        $file = $this->normalizer->denormalize(self::TEST_GIF_DATA, 'Symfony\Component\HttpFoundation\File\File');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\File\File', $file);
        $this->assertSame(file_get_contents(self::TEST_GIF_DATA), $this->getContent($file->openFile()));
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\UnexpectedValueException
     * @expectedExceptionMessage The provided "data:" URI is not valid.
     */
    public function testGiveNotAccessToLocalFiles()
    {
        $this->normalizer->denormalize('/etc/shadow', 'SplFileObject');
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\UnexpectedValueException
     * @dataProvider invalidUriProvider
     */
    public function testInvalidData($uri)
    {
        $this->normalizer->denormalize($uri, 'SplFileObject');
    }

    public function invalidUriProvider()
    {
        return array(
            array('dataxbase64'),
            array('data:HelloWorld'),
            array('data:text/html;charset=,%3Ch1%3EHello!%3C%2Fh1%3E'),
            array('data:text/html;charset,%3Ch1%3EHello!%3C%2Fh1%3E'),
            array('data:base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD///+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4Ug9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC'),
            array(''),
            array('http://wikipedia.org'),
            array('base64'),
            array('iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD///+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4Ug9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC'),
            array(' data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIBAMAAAA2IaO4AAAAFVBMVEXk5OTn5+ft7e319fX29vb5+fn///++GUmVAAAALUlEQVQIHWNICnYLZnALTgpmMGYIFWYIZTA2ZFAzTTFlSDFVMwVyQhmAwsYMAKDaBy0axX/iAAAAAElFTkSuQmCC'),
            array('   data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIBAMAAAA2IaO4AAAAFVBMVEXk5OTn5+ft7e319fX29vb5+fn///++GUmVAAAALUlEQVQIHWNICnYLZnALTgpmMGYIFWYIZTA2ZFAzTTFlSDFVMwVyQhmAwsYMAKDaBy0axX/iAAAAAElFTkSuQmCC'),
        );
    }

    /**
     * @dataProvider validUriProvider
     */
    public function testValidData($uri)
    {
        $this->assertInstanceOf('SplFileObject', $this->normalizer->denormalize($uri, 'SplFileObject'));
    }

    public function validUriProvider()
    {
        $data = array(
            array('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD///+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4Ug9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC'),
            array('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIBAMAAAA2IaO4AAAAFVBMVEXk5OTn5+ft7e319fX29vb5+fn///++GUmVAAAALUlEQVQIHWNICnYLZnALTgpmMGYIFWYIZTA2ZFAzTTFlSDFVMwVyQhmAwsYMAKDaBy0axX/iAAAAAElFTkSuQmCC'),
            array('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIBAMAAAA2IaO4AAAAFVBMVEXk5OTn5+ft7e319fX29vb5+fn///++GUmVAAAALUlEQVQIHWNICnYLZnALTgpmMGYIFWYIZTA2ZFAzTTFlSDFVMwVyQhmAwsYMAKDaBy0axX/iAAAAAElFTkSuQmCC   '),
            array('data:,Hello%2C%20World!'),
            array('data:text/html,%3Ch1%3EHello%2C%20World!%3C%2Fh1%3E'),
            array('data:,A%20brief%20note'),
            array('data:text/html;charset=US-ASCII,%3Ch1%3EHello!%3C%2Fh1%3E'),
        );

        if (!defined('HHVM_VERSION')) {
            // See https://github.com/facebook/hhvm/issues/6354
            $data[] = array('data:text/plain;charset=utf-8;base64,SGVsbG8gV29ybGQh');
        }

        return $data;
    }

    private function getContent(\SplFileObject $file)
    {
        $buffer = '';
        while (!$file->eof()) {
            $buffer .= $file->fgets();
        }

        return $buffer;
    }
}
