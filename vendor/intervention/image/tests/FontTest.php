<?php

use Intervention\Image\Font;
use Intervention\Image\Image;

class FontTest extends PHPUnit_Framework_Testcase
{
    public function testConstructorWithoutParameters()
    {
        $font = new Font;
        $this->assertInstanceOf('Intervention\Image\Font', $font);
    }

    public function testConstructorWithParameters()
    {
        $font = new Font('The quick brown fox jumps over the lazy dog.');
        $this->assertInstanceOf('Intervention\Image\Font', $font);
        $this->assertEquals($font->getText(), 'The quick brown fox jumps over the lazy dog.');
    }

    public function testText()
    {
        $font = new Font;
        $font->text('The quick brown fox jumps over the lazy dog.');
        $this->assertEquals($font->getText(), 'The quick brown fox jumps over the lazy dog.');
    }

    public function testSize()
    {
        $font = new Font;
        $font->size(24);
        $this->assertInternalType('int', $font->getSize());
        $this->assertEquals($font->getSize(), 24);
    }

    public function testPointSize()
    {
        $font = new Font;
        $font->size(24);
        $this->assertInternalType('int', $font->getPointSize());
        $this->assertEquals($font->getPointSize(), 18);
    }

    public function testColor()
    {
        $font = new Font;
        $font->color('b53717');
        $this->assertInternalType('string', $font->getColor());
        $this->assertEquals($font->getColor(), 'b53717');
    }

    public function testAngle()
    {
        $font = new Font;
        $font->angle(45);
        $this->assertInternalType('int', $font->getAngle());
        $this->assertEquals($font->getAngle(), 45);
    }

    public function testAlign()
    {
        $font = new Font;
        $font->align('center');
        $this->assertInternalType('string', $font->getAlign());
        $this->assertEquals($font->getAlign(), 'center');
    }

    public function testValign()
    {
        $font = new Font;
        $font->valign('bottom');
        $this->assertInternalType('string', $font->getValign());
        $this->assertEquals($font->getValign(), 'bottom');
    }

    public function testFile()
    {
        $font = new Font;
        $font->file('foo.ttf');
        $this->assertInternalType('string', $font->getFile());
        $this->assertEquals($font->getFile(), 'foo.ttf');
    }

    public function testGetBoxsizeInternal()
    {
        $font = new Font;
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(0, $box['width']);
        $this->assertEquals(0, $box['height']);

        $font = new Font('000');
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(15, $box['width']);
        $this->assertEquals(8, $box['height']);

        $font = new Font('000');
        $font->file(1);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(15, $box['width']);
        $this->assertEquals(8, $box['height']);

        $font = new Font('000');
        $font->file(2);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(18, $box['width']);
        $this->assertEquals(14, $box['height']);

        $font = new Font('000');
        $font->file(3);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(21, $box['width']);
        $this->assertEquals(14, $box['height']);

        $font = new Font('000');
        $font->file(4);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(24, $box['width']);
        $this->assertEquals(16, $box['height']);

        $font = new Font('000');
        $font->file(5);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(27, $box['width']);
        $this->assertEquals(16, $box['height']);
    }

    /*
    public function testGetBoxsizeFontfile()
    {
        $font = new Font;
        $font->file('public/Vera.ttf');
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(0, $box['width']);
        $this->assertEquals(0, $box['height']);

        $font = new Font('000');
        $font->file('public/Vera.ttf');
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(22, $box['width']);
        $this->assertEquals(9, $box['height']);

        $font = new Font('000');
        $font->file('public/Vera.ttf');
        $font->size(16);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(28, $box['width']);
        $this->assertEquals(12, $box['height']);

        $font = new Font('000');
        $font->file('public/Vera.ttf');
        $font->size(24);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(42, $box['width']);
        $this->assertEquals(18, $box['height']);
    }

    
    public function testGetBoxsizeFontfileWithAngle()
    {
        $font = new Font('000');
        $font->file('public/Vera.ttf');
        $font->angle(45);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(22, $box['width']);
        $this->assertEquals(9, $box['height']);

        $font = new Font('000');
        $font->file('public/Vera.ttf');
        $font->size(16);
        $font->angle(45);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(28, $box['width']);
        $this->assertEquals(12, $box['height']);

        $font = new Font('000');
        $font->file('public/Vera.ttf');
        $font->size(24);
        $font->angle(45);
        $box = $font->getboxsize();
        $this->assertInternalType('int', $box['width']);
        $this->assertInternalType('int', $box['height']);
        $this->assertEquals(42, $box['width']);
        $this->assertEquals(18, $box['height']);
    }
    */

    public function testAlignmentsInternalFonts()
    {
        $haligns = array('left', 'center', 'right');
        $valigns = array('top', 'middle', 'bottom');
        $fontfiles = array(1, 2, 3, 4, 5);
        $position = array(80, 40);

        $checksums = array(
            '1' => array(
                'top' => array(
                    'left' => 'fa8bfa9a8cce6071515d9aac18007a50',
                    'center' => '86c9fa152b5b3e1a637cb17b70a6edfc',
                    'right' => 'a386f9155ef5da46872c73958b668799',
                ),
                'middle' => array(
                    'left' => '45af47bbda0ba771aa4963f447d3bbb9',
                    'center' => '700182599f461d37efb8297170ea7eda',
                    'right' => '288bab5bf92dead0ed909167d072b8bb',
                ),
                'bottom' => array(
                    'left' => 'a85e0d2329957b27b3cf4c3f21721c45',
                    'center' => '507a590cdae9b6dce1a6fc9b30a744cb',
                    'right' => '530ac7978230241c555dd8c5374f029e',
                ),
            ),
            '2' => array(
                'top' => array(
                    'left' => '24e4dc99e7e2b9b7d553b0fce1abf3a3',
                    'center' => '1099aae99acad2dac796d33da99cb9d3',
                    'right' => '06a888438b2a6bc5e673f8c6d9b70e42',
                ),
                'middle' => array(
                    'left' => 'ae2629402f215aef11226413b71b8b7a',
                    'center' => '8ce82b42da6ad6b5ceb7da7bf637022d',
                    'right' => '870034642cfd26ebd165ca0de5a8c12c',
                ),
                'bottom' => array(
                    'left' => 'd6b53fdd3e68f64287e988937917a6e3',
                    'center' => '3a4c8fdaae0f056c3b24c2d13d09a865',
                    'right' => 'd4db9c844cf73a78b0ddadaa7a230c04',
                ),
            ),
            '3' => array(
                'top' => array(
                    'left' => 'd6dd07b730f8793ba08b5f2052965254',
                    'center' => 'b8ae1ca956b74b066572dd76c1fbc2cb',
                    'right' => '1e3327bf7e540487aa1514e490339ff3',
                ),
                'middle' => array(
                    'left' => '2b69adba28256ac1b10d67bde6f2ac71',
                    'center' => '55cdc747bd51513640be777d15561f58',
                    'right' => '1918bcf0810b38454d84591b356a5f1e',
                ),
                'bottom' => array(
                    'left' => 'aac8e8a56e9f464b5223c568fe660ff9',
                    'center' => 'e9c381bfac9690e1b7c330b9ed9bd6fa',
                    'right' => '3966301d2c445fe5f73c10bda7ba7993',
                ),
            ),
            '4' => array(
                'top' => array(
                    'left' => '9b61dcbbf8c1d61db7301f38677cb094',
                    'center' => 'b3c6f738493d38ba6e658100fa5592f7',
                    'right' => '65b7525ee23c7e4d3db3e82b24b3f175',
                ),
                'middle' => array(
                    'left' => '060933279d8a34d0234c1d0e25c41357',
                    'center' => 'f06c06b4604a72f7b8b9068ffa306990',
                    'right' => '3cc4f152c671021decca21656ac078a2',
                ),
                'bottom' => array(
                    'left' => '83201c48862f4ccf218b7ae018cfc61f',
                    'center' => 'e07fd632d487f1fe507c4adf7c4a8f71',
                    'right' => 'da2cf30237fcd2724ba2b7248026d73b',
                ),
            ),
            '5' => array(
                'top' => array(
                    'left' => '02cabb064130730206bfc05d86842bcd',
                    'center' => '50c46cf1ccf9776cc118ad1102a3f259',
                    'right' => 'e1e7bc8a72c0b64b20cc3e96e4cb7573',
                ),
                'middle' => array(
                    'left' => '45f263ba8a4ae63f4275a8b76a0f526b',
                    'center' => 'a76f4d65901b30ed5eb58a9975560b80',
                    'right' => '7d04bceecc1c576e84b6184a89b2b2c7',
                ),
                'bottom' => array(
                    'left' => '285741dbdb636724dc56b2ff8a0c5814',
                    'center' => 'ed274bfc9d9e3e7644baedc043312f7b',
                    'right' => 'fa00447bb085ec03d6a865bbc39faf36',
                ),
            ),
        );

        foreach ($haligns as $halign) {
            foreach ($valigns as $valign) {
                foreach ($fontfiles as $file) {
                    $canvas = new Image(null, 160, 80, 'ffffff');
                    $font = new Font('00000');
                    $font->file($file);
                    $font->align($halign);
                    $font->valign($valign);
                    $font->applyToImage($canvas, $position[0], $position[1]);
                    $checksum = $canvas->checksum();
                    $this->assertEquals($checksum, $checksums[$file][$valign][$halign]);
                }
            }
        }
    }

    /**
     * @expectedException Intervention\Image\Exception\FontNotFoundException
     */
    public function testInternalFontNotAvailable()
    {
        $image = new Image(null, 25, 25);
        $font = new Font;
        $font->file(10);
        $font->applyToImage($image);
    }

    /**
     * @expectedException Intervention\Image\Exception\FontNotFoundException
     */
    public function testFontfileNotAvailable()
    {
        $image = new Image(null, 25, 25);
        $font = new Font;
        $font->file('foo/bar.ttf');
        $font->applyToImage($image);
    }
}
