<?php

use Intervention\Image\Image;

class ImageTest extends PHPUnit_Framework_Testcase
{
    protected function setUp()
    {

    }

    protected function tearDown()
    {

    }

    private function getTestImage()
    {
        return new Image('public/test.jpg');
    }

    public function testConstructorPlain()
    {
        $img = new Image;
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 1);
        $this->assertEquals($img->height, 1);

        $color = $img->pickColor(0, 0, 'array');
        $this->assertInternalType('int', $color['r']);
        $this->assertInternalType('int', $color['g']);
        $this->assertInternalType('int', $color['b']);
        $this->assertInternalType('float', $color['a']);
        $this->assertEquals($color['r'], 0);
        $this->assertEquals($color['g'], 0);
        $this->assertEquals($color['b'], 0);
        $this->assertEquals($color['a'], 0);
    }

    public function testConstructorWithPath()
    {
        $img = new Image('public/test.jpg');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
        $this->assertEquals($img->dirname, 'public');
        $this->assertEquals($img->basename, 'test.jpg');
        $this->assertEquals($img->extension, 'jpg');
        $this->assertEquals($img->filename, 'test');
        $this->assertEquals($img->mime, 'image/jpeg');
    }

    /**
     * @expectedException Intervention\Image\Exception\ImageNotFoundException
     */
    public function testConstructorWithInvalidPath()
    {
        $img = new Image('public/foo/bar/invalid_image_path.jpg');
    }

    /**
     * @expectedException Intervention\Image\Exception\ImageNotFoundException
     */
    public function testConstructorWithNonAsciiCharacters()
    {
        // file does not exists but path string should NOT be considered
        // as binary data. (should _NOT_ throw InvalidImageDataStringException)
        $img = new Image('public/Über.jpg');
    }

    /**
     * @expectedException Intervention\Image\Exception\InvalidImageTypeException
     */
    public function testContructorWithPathInvalidType()
    {
        $img = new Image('public/text.txt');
    }

    public function testConstructoWithString()
    {
        $data = file_get_contents('public/test.jpg');
        $img = new Image($data);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
    }

    /**
     * @expectedException Intervention\Image\Exception\InvalidImageDataStringException
     */
    public function testConstructionWithInvalidString()
    {
        // the semi-random string is base64_decoded to allow it to
        // pass the isBinary conditional.
        $data = base64_decode('6KKjdeyUAhRPNzxeYybZ');
        $img = new Image($data);
    }

    public function testConstructorWithResource()
    {
        $resource = imagecreatefromjpeg('public/test.jpg');
        $img = new Image($resource);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
    }

    /**
     * @expectedException Intervention\Image\Exception\InvalidImageResourceException
     */
    public function testConstructorWithInvalidResource()
    {
        $resource = fopen('public/test.jpg', 'r+');
        $img = new Image($resource);
    }

    public function testConstructorCanvas()
    {
        $img = new Image(null, 800, 600);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);

        $color = $img->pickColor(50, 50, 'array');
        $this->assertInternalType('int', $color['r']);
        $this->assertInternalType('int', $color['g']);
        $this->assertInternalType('int', $color['b']);
        $this->assertInternalType('float', $color['a']);
        $this->assertEquals($color['r'], 0);
        $this->assertEquals($color['g'], 0);
        $this->assertEquals($color['b'], 0);
        $this->assertEquals($color['a'], 0);
    }

    public function testStaticCallMakeFromPath()
    {
        $img = Image::make('public/test.jpg');
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
        $this->assertEquals($img->dirname, 'public');
        $this->assertEquals($img->basename, 'test.jpg');
        $this->assertEquals($img->extension, 'jpg');
        $this->assertEquals($img->filename, 'test');
        $this->assertEquals($img->mime, 'image/jpeg');
    }

    public function testStaticCallMakeFromString()
    {
        $data = file_get_contents('public/test.jpg');
        $img = Image::make($data);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
        $this->assertEquals($img->mime, 'image/jpeg');
    }

    public function testStaticCallMakeFromResource()
    {
        $resource = imagecreatefromjpeg('public/test.jpg');
        $img = Image::make($resource);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
    }

    public function testStaticCallCanvas()
    {
        $img = Image::canvas(300, 200);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);

        $img = Image::canvas(32, 32, 'b53717');
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals($img->pickColor(15, 15, 'hex'), '#b53717');
    }

    public function testStaticCallRaw()
    {
        $data = file_get_contents('public/test.jpg');
        $img = Image::raw($data);
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
    }

    public function testCreateCanvasWithTransparentBackground()
    {
        $img = Image::canvas(100, 100);
        $color = $img->pickColor(50, 50, 'array');
        $this->assertInternalType('int', $color['r']);
        $this->assertInternalType('int', $color['g']);
        $this->assertInternalType('int', $color['b']);
        $this->assertInternalType('float', $color['a']);
        $this->assertEquals($color['r'], 0);
        $this->assertEquals($color['g'], 0);
        $this->assertEquals($color['b'], 0);
        $this->assertEquals($color['a'], 0);
    }

    public function testOpenImage()
    {
        $img = new Image;
        $img->open('public/test.jpg');
        $this->assertInternalType('resource', $img->resource);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
        $this->assertEquals($img->dirname, 'public');
        $this->assertEquals($img->basename, 'test.jpg');
        $this->assertEquals($img->extension, 'jpg');
        $this->assertEquals($img->filename, 'test');
        $this->assertEquals($img->mime, 'image/jpeg');
    }

    public function testResizeImage()
    {
        $img = $this->getTestImage();
        $img->resize(320, 240);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 320);
        $this->assertEquals($img->height, 240);
        $this->assertEquals($img->pickColor(50, 50, 'hex'), '#fffaf4');
        $this->assertEquals($img->pickColor(260, 190, 'hex'), '#ffa600');

        // Only resize the width.
        $img = $this->getTestImage();
        $height = $img->height;
        $img->resize(320);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 320);
        // Check if the height is still the same.
        $this->assertEquals($img->height, $height);
        $this->assertEquals($img->pickColor(75, 65, 'hex'), '#fffcf3');
        $this->assertEquals($img->pickColor(250, 150, 'hex'), '#ffc150');

        // Only resize the width.
        $img = $this->getTestImage();
        $width = $img->width;
        $img->resize(null, 240);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        // Check if the width is still the same.
        $this->assertEquals($img->width, $width);
        $this->assertEquals($img->height, 240);
        $this->assertEquals($img->pickColor(150, 75, 'hex'), '#fff4e0');
        $this->assertEquals($img->pickColor(540, 10, 'hex'), '#ffda96');

        // auto height
        $img = $this->getTestImage();
        $img->resize(320, null, true);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 320);
        $this->assertEquals($img->height, 240);
        $this->assertEquals($img->pickColor(50, 50, 'hex'), '#fffaf4');
        $this->assertEquals($img->pickColor(260, 190, 'hex'), '#ffa600');

        // auto width
        $img = $this->getTestImage();
        $img->resize(null, 240, true);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 320);
        $this->assertEquals($img->height, 240);
        $this->assertEquals($img->pickColor(50, 50, 'hex'), '#fffaf4');
        $this->assertEquals($img->pickColor(260, 190, 'hex'), '#ffa600');

        // preserve simple upsizing
        $img = $this->getTestImage();
        $img->resize(1000, 1000, true, false);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);

        // test dominant width for auto-resizing
        $img = $this->getTestImage();
        $img->resize(1000, 1200, true);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 1000);
        $this->assertEquals($img->height, 750);

        // Test image upsizing.
        $img = $this->getTestImage();
        // Keep original width and height.
        $original_width = $img->width;
        $original_height = $img->height;
        // Increase values a bit.
        $width = $original_width + 500;
        $height = $original_height + 350;
        // Try resizing to higher values while upsizing is set to false.
        $img->resize($width, $height, false, false);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        // Check if width and height are still the same.
        $this->assertEquals($img->width, $original_width);
        $this->assertEquals($img->height, $original_height);
    }

    /**
     * @expectedException Intervention\Image\Exception\DimensionOutOfBoundsException
     */
    public function testResizeImageWithoutDimensions()
    {
        $img = $this->getTestImage();
        $img->resize();
    }

    public function testWidenImage()
    {
        $img = $this->getTestImage();

        $img->widen(100);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 100);
        $this->assertEquals($img->height, 75);

        $img->widen(1000);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 1000);
        $this->assertEquals($img->height, 750);
    }

    public function testHeightenImage()
    {
        $img = $this->getTestImage();

        $img->heighten(150);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 200);
        $this->assertEquals($img->height, 150);

        $img->heighten(900);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 1200);
        $this->assertEquals($img->height, 900);
    }

    public function testResizeCanvas()
    {
        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200); // pin center
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#ffe8bc', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffaf1c', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'top-left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#fee3ae', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'top');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#fffbf2', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffc559', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'top-right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#ffe2ae', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffac12', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#fefdf9', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffca6a', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#ffca66', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'bottom-left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#ffedcc', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffb42b', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'bottom');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#ffd179', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(299, 199, 'hex'));

        $img = $this->getTestImage();
        $img->resizeCanvas(300, 200, 'bottom-right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#ffb42a', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(299, 199, 'hex'));

        // resize relative from center 5px border in magenta
        $img = $this->getTestImage();
        $img->resizeCanvas(10, 10, 'center', true, 'ff00ff');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 810);
        $this->assertEquals($img->height, 610);
        $this->assertEquals('#ff00ff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ff00ff', $img->pickColor(809, 609, 'hex'));

        // resize just width
        $img = $this->getTestImage();
        $img->resizeCanvas(300, null);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 600);
        $this->assertEquals('#fffbf2', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(299, 599, 'hex'));

        // resize just height
        $img = $this->getTestImage();
        $img->resizeCanvas(null, 200);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#fefdf9', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(799, 199, 'hex'));

        // smaller width, larger height
        $img = $this->getTestImage();
        $img->resizeCanvas(300, 800);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 300);
        $this->assertEquals($img->height, 800);
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(299, 799, 'hex'));

        // larger width, smaller height
        $img = $this->getTestImage();
        $img->resizeCanvas(900, 200);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 900);
        $this->assertEquals($img->height, 200);
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(899, 199, 'hex'));

        // test negative values (for relative resize)
        $img = $this->getTestImage();
        $img->resizeCanvas(-200, -200);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 600);
        $this->assertEquals($img->height, 400);
        $this->assertEquals('#fffefc', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(599, 399, 'hex'));

        // resize to larger size with anchor and only height set
        $img = $this->getTestImage();
        $img->resizeCanvas(null, 650, 'bottom-left', false, '#00000');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 650);
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ffffff', $img->pickColor(3, 50, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(799, 649, 'hex'));

        // resize with emerging transparent area
        $img = $this->getTestImage();
        $img->resizeCanvas(900, 700, 'center', false, array(0, 0, 0, 0));
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 900);
        $this->assertEquals($img->height, 700);
        $transparency_1 = $img->pickColor(0, 0, 'array');
        $transparency_2 = $img->pickColor(899, 699, 'array');
        $this->assertEquals(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0.0), $transparency_1);
        $this->assertEquals(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0.0), $transparency_2);

        // preserve transparency when resizing canvas
        $img = new Image('public/circle.png');
        $img->resizeCanvas(40, 40, 'center');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 40);
        $this->assertEquals($img->height, 40);
        $this->assertEquals(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0.0), $img->pickColor(0, 0, 'array'));
        $this->assertEquals(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0.8), $img->pickColor(20, 20, 'array'));

    }

    public function testCropImage()
    {
        $img = $this->getTestImage();
        $img->crop(100, 100);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 100);
        $this->assertEquals($img->height, 100);
        $this->assertEquals('#ffbe46', $img->pickColor(99, 99, 'hex'));

        $img = $this->getTestImage();
        $img->crop(100, 100, 650, 400);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 100);
        $this->assertEquals($img->height, 100);
        $this->assertEquals('#ffa600', $img->pickColor(99, 99, 'hex'));
    }

    /**
     * @expectedException Intervention\Image\Exception\DimensionOutOfBoundsException
     */
    public function testCropImageWithoutDimensions()
    {
        $img = $this->getTestImage();
        $img->crop(null, null);
    }

    /**
     * @expectedException Intervention\Image\Exception\DimensionOutOfBoundsException
     */
    public function testCropImageWithNonNumericDimensions()
    {
        $img = $this->getTestImage();
        $img->crop('a', 'z');
    }

    public function testLegacyResize()
    {
        // auto height
        $img = $this->getTestImage();
        $img->resize(array('width' => '320'));
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 320);
        $this->assertEquals($img->height, 240);

        // auto width
        $img = $this->getTestImage();
        $img->resize(array('height' => '240'));
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 320);
        $this->assertEquals($img->height, 240);
    }

    public function testGrabImage()
    {
        $img = $this->getTestImage();
        $img->grab(200);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 200);
        $this->assertEquals($img->height, 200);
        $this->assertEquals($img->pickColor(50, 50, 'hex'), '#feedcc');
        $this->assertEquals($img->pickColor(140, 20, 'hex'), '#fed891');

        $img = $this->getTestImage();
        $img->grab(200, 100);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 200);
        $this->assertEquals($img->height, 100);
        $this->assertEquals($img->pickColor(50, 25, 'hex'), '#ffeccb');
        $this->assertEquals($img->pickColor(180, 40, 'hex'), '#fead15');

        $img = $this->getTestImage();
        $img->grab(null, 100);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 100);
        $this->assertEquals($img->height, 100);
        $this->assertEquals($img->pickColor(30, 30, 'hex'), '#fee5b7');
        $this->assertEquals($img->pickColor(95, 20, 'hex'), '#ffbe47');

        $img = $this->getTestImage();
        $img->grab(array('width' => '100'));
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 100);
        $this->assertEquals($img->height, 100);
        $this->assertEquals($img->pickColor(30, 30, 'hex'), '#fee5b7');
        $this->assertEquals($img->pickColor(95, 20, 'hex'), '#ffbe47');

        $img = $this->getTestImage();
        $img->grab(array('height' => '200'));
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 200);
        $this->assertEquals($img->height, 200);
        $this->assertEquals($img->pickColor(30, 30, 'hex'), '#fff9ed');
        $this->assertEquals($img->pickColor(95, 20, 'hex'), '#ffe8bf');
    }

    /**
     * @expectedException Intervention\Image\Exception\DimensionOutOfBoundsException
     */
    public function testGrabImageWithoutDimensions()
    {
        $img = $this->getTestImage();
        $img->grab();
    }

    public function testFlipImage()
    {
        $img = $this->getTestImage();
        $img->flip('h');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#ffbf47', $img->pickColor(0, 0, 'hex'));

        $img = $this->getTestImage();
        $img->flip('v');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#fed78c', $img->pickColor(0, 0, 'hex'));
    }

    public function testRotateImage()
    {
        $img = $this->getTestImage();
        $img->rotate(90);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 600);
        $this->assertEquals($img->height, 800);
        $this->assertEquals('#ffbf47', $img->pickColor(0, 0, 'hex'));

        $img = $this->getTestImage();
        $img->rotate(180);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
        $this->assertEquals('#ffa600', $img->pickColor(0, 0, 'hex'));

        // rotate transparent png and keep transparency
        $img = Image::make('public/circle.png');
        $img->rotate(180);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 50);
        $checkColor = $img->pickColor(0, 0, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0);
    }

    public function testInsertImage()
    {
        $watermark = Image::canvas(16, 16, '#0000ff'); // create watermark

        // top-left anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'top-left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(16, 16, 'hex'));

        // top-left anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'top-left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#ff0000', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#0000ff', $img->pickColor(10, 10, 'hex'));

        // top anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'top');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#ff0000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#0000ff', $img->pickColor(23, 15, 'hex'));

        // top anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'top');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(18, 10, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(31, 26, 'hex'));

        // top-right anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'top-right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#ff0000', $img->pickColor(15, 0, 'hex'));
        $this->assertEquals('#0000ff', $img->pickColor(31, 0, 'hex'));

        // top-right anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'top-right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#ff0000', $img->pickColor(6, 9, 'hex'));
        $this->assertEquals('#0000ff', $img->pickColor(21, 25, 'hex'));

        // left anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(15, 23, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(0, 7, 'hex'));

        // left anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(25, 31, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(10, 17, 'hex'));

        // right anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(31, 23, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(15, 15, 'hex'));

        // right anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(21, 31, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(5, 18, 'hex'));

        // bottom-left anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'bottom-left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(15, 31, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(0, 15, 'hex'));

        // bottom-left anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'bottom-left');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(10, 21, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(9, 20, 'hex'));

        // bottom anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'bottom');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(8, 16, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(8, 15, 'hex'));

        // bottom anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'bottom');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(18, 21, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(17, 21, 'hex'));

        // bottom-right anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'bottom-right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(16, 16, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(15, 16, 'hex'));

        // bottom-right anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'bottom-right');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(21, 21, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(22, 22, 'hex'));

        // center anchor
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 0, 0, 'center');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(23, 23, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(8, 7, 'hex'));

        // center anchor coordinates
        $img = Image::canvas(32, 32, '#ff0000'); // create canvas
        $img->insert($watermark, 10, 10, 'center');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#0000ff', $img->pickColor(31, 31, 'hex'));
        $this->assertEquals('#ff0000', $img->pickColor(18, 17, 'hex'));
    }

    public function testInsertAfterResize()
    {
        $watermark = 'public/circle.png';
        $img = Image::make('public/test.jpg');
        $img->resize(50, 50)->insert($watermark, 0, 0, 'center');
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 50);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#322715', $img->pickColor(24, 24, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(49, 49, 'hex'));
    }

    public function testInsertImageFromResource()
    {
        $resource = imagecreatefrompng('public/tile.png');
        $img = Image::canvas(16, 16)->insert($resource);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(15, 15, 'hex'));
    }

    public function testInsertImageFromBinary()
    {
        $data = file_get_contents('public/tile.png');
        $img = Image::canvas(16, 16)->insert($data);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(15, 15, 'hex'));
    }

    public function testInsertImageFromObject()
    {
        $obj = new Image('public/tile.png');
        $img = Image::canvas(16, 16)->insert($obj);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(15, 15, 'hex'));
    }

    public function testInsertImageFromPath()
    {
        $img = Image::canvas(16, 16)->insert('public/tile.png');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(15, 15, 'hex'));
    }

    public function testOpacity()
    {
        // simple image mask
        $img = Image::make('public/test.jpg');
        $img->resize(32, 32)->opacity(50);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $checkColor = $img->pickColor(15, 15, 'array');
        $this->assertEquals($checkColor['r'], 254);
        $this->assertEquals($checkColor['g'], 204);
        $this->assertEquals($checkColor['b'], 112);
        $this->assertEquals($checkColor['a'], 0.5);
        $checkColor = $img->pickColor(31, 31, 'array');
        $this->assertEquals($checkColor['r'], 255);
        $this->assertEquals($checkColor['g'], 166);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0.5);
    }

    /**
     * @expectedException Intervention\Image\Exception\OpacityOutOfBoundsException
     */
    public function testOpacityTooHigh()
    {
        $img = $this->getTestImage();
        $img->opacity(101);
    }

    /**
     * @expectedException Intervention\Image\Exception\OpacityOutOfBoundsException
     */
    public function testOpacityTooLow()
    {
        $img = $this->getTestImage();
        $img->opacity(-1);
    }

    /**
     * @expectedException Intervention\Image\Exception\OpacityOutOfBoundsException
     */
    public function testOpacityAlphaChar()
    {
        $img = $this->getTestImage();
        $img->opacity('a');
    }

    public function testMaskImage()
    {
        // simple image mask
        $img = Image::make('public/test.jpg');
        $img->resize(32, 32)->mask('public/mask1.png', false);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $checkColor = $img->pickColor(16, 2, 'array');
        $this->assertEquals($checkColor['r'], 254);
        $this->assertEquals($checkColor['g'], 230);
        $this->assertEquals($checkColor['b'], 186);
        $this->assertEquals($checkColor['a'], 0.83);
        $checkColor = $img->pickColor(31, 31, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0);

        // use alpha channel as mask
        $img = Image::make('public/test.jpg');
        $img->resize(32, 32)->mask('public/mask2.png', true);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $checkColor = $img->pickColor(5, 5, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0);
        $checkColor = $img->pickColor(20, 15, 'array');
        $this->assertEquals($checkColor['r'], 254);
        $this->assertEquals($checkColor['g'], 190);
        $this->assertEquals($checkColor['b'], 69);
        $this->assertEquals($checkColor['a'], 1);

        // preserve existing alpha channel
        $img = Image::make('public/circle.png');
        $img->resize(32, 32)->mask('public/mask2.png', true);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $checkColor = $img->pickColor(5, 5, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0);
        $checkColor = $img->pickColor(15, 15, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0.8);
    }

    public function testMaskWithResource()
    {
        $img = Image::make('public/circle.png');
        $resource = imagecreatefrompng('public/mask2.png');
        $img->resize(32, 32)->mask($resource, true);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $checkColor = $img->pickColor(5, 5, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0);
        $checkColor = $img->pickColor(15, 15, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0.8);
    }

    public function testMaskWithBinary()
    {
        $img = Image::make('public/circle.png');
        $data = file_get_contents('public/mask2.png');
        $img->resize(32, 32)->mask($data, true);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $checkColor = $img->pickColor(5, 5, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0);
        $checkColor = $img->pickColor(15, 15, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0.8);
    }

    public function testMaskWithObject()
    {
        $img = Image::make('public/circle.png');
        $obj = Image::make('public/mask2.png');
        $img->resize(32, 32)->mask($obj, true);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $checkColor = $img->pickColor(5, 5, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0);
        $checkColor = $img->pickColor(15, 15, 'array');
        $this->assertEquals($checkColor['r'], 0);
        $this->assertEquals($checkColor['g'], 0);
        $this->assertEquals($checkColor['b'], 0);
        $this->assertEquals($checkColor['a'], 0.8);
    }

    public function testPixelateImage()
    {
        $img = $this->getTestImage();
        $img->pixelate(20);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
    }

    public function testGreyscaleImage()
    {
        $img = $this->getTestImage();
        $img->greyscale();
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#adadad', $img->pickColor(660, 450, 'hex'));
    }

    public function testInvertImage()
    {
        $img = $this->getTestImage();
        $img->invert();
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));
    }

    public function testBlurImage()
    {
        $img = Image::make('public/tile.png')->blur();
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#98bc18', $img->pickColor(0, 7, 'hex'));
    }

    public function testFillImage()
    {
        $img = new Image(null, 32, 32);
        $img = $img->fill('fdf5e4');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#fdf5e4', $img->pickColor(0, 0, 'hex'));

        $img = $img->fill('#fdf5e4');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#fdf5e4', $img->pickColor(0, 0, 'hex'));

        $img = $img->fill('ccc');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#cccccc', $img->pickColor(0, 0, 'hex'));

        $img = $img->fill('#ccc');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#cccccc', $img->pickColor(0, 0, 'hex'));

        $img = $img->fill(array(155, 155, 155), rand(1,10), rand(1,10));
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#9b9b9b', $img->pickColor(0, 0, 'hex'));

        $img = new Image(null, 32, 32);
        $img = $img->fill('rgba(180, 224, 0, 0.65)', rand(1,10), rand(1,10));
        $checkColor = $img->pickColor(0, 0, 'array');
        $this->assertEquals(180, $checkColor['r']);
        $this->assertEquals(224, $checkColor['g']);
        $this->assertEquals(0, $checkColor['b']);
        $this->assertEquals(0.65, $checkColor['a']);
    }

    public function testFillImageWithResource()
    {
        $resource = imagecreatefrompng('public/tile.png');
        $img = Image::canvas(32, 32)->fill($resource);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(31, 31, 'hex'));
    }

    public function testFillImageWithBinary()
    {
        $data = file_get_contents('public/tile.png');
        $img = Image::canvas(32, 32)->fill($data);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(31, 31, 'hex'));
    }

    public function testFillImageWithObject()
    {
        $obj = new Image('public/tile.png');
        $img = Image::canvas(32, 32)->fill($obj);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(31, 31, 'hex'));
    }

    public function testFillImageWithPath()
    {
        $img = Image::canvas(100, 100, '#ffa600')->fill('public/circle.png');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 100);
        $this->assertEquals($img->height, 100);
        $this->assertEquals('#ffa600', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#322000', $img->pickColor(12, 12, 'hex'));
        $this->assertEquals('#ffa600', $img->pickColor(99, 99, 'hex'));
        $this->assertEquals('#322000', $img->pickColor(80, 80, 'hex'));
    }

    public function testFillImageWithTransparentImage()
    {
        $img = Image::canvas(32, 32)->fill('public/tile.png');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 32);
        $this->assertEquals('#b4e000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(31, 31, 'hex'));
    }

    public function testFillWithPosition()
    {
        $img = Image::make('public/tile.png')->fill('#ff00ff', 0, 0);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#ff00ff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#445160', $img->pickColor(15, 15, 'hex'));
    }

    public function testFillWithoutPosition()
    {
        $img = Image::make('public/tile.png')->fill('#ff00ff');
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('#ff00ff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#ff00ff', $img->pickColor(15, 15, 'hex'));
    }

    public function testPixelImage()
    {
        $img = $this->getTestImage();
        $coords = array(array(5, 5), array(100, 100));
        $img = $img->pixel('fdf5e4', $coords[0][0], $coords[0][1]);
        $img = $img->pixel(array(255, 255, 255), $coords[1][0], $coords[1][1]);
        $this->assertEquals('#fdf5e4', $img->pickColor($coords[0][0], $coords[0][1], 'hex'));
        $this->assertEquals('#ffffff', $img->pickColor($coords[1][0], $coords[1][1], 'hex'));
    }

    public function testLegacyTextImage()
    {
        $img = $this->getTestImage();
        $img = $img->text('Fox', 10, 10, 16, '000000', 0, null);
        $this->assertInstanceOf('Intervention\Image\Image', $img);

        $img = $img->text('Fox', 10, 10, 16, '#000000', 0, null);
        $this->assertInstanceOf('Intervention\Image\Image', $img);

        $img = $img->text('Fox', 10, 10, 16, array(155, 155, 155), 0, null);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
    }

    public function testTextImage()
    {
        $img = new Image(null, 160, 80, 'ffffff');
        $img = $img->text('00000', 80, 40);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('a85e0d2329957b27b3cf4c3f21721c45', $img->checksum());

        $img = new Image(null, 160, 80, 'ffffff');
        $img = $img->text('00000', 80, 40, function($font) {
            $font->align('center');
            $font->valign('top');
            $font->color('000000');
        });
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('86c9fa152b5b3e1a637cb17b70a6edfc', $img->checksum());

        $img = new Image(null, 160, 80, 'ffffff');
        $img = $img->text('00000', 80, 40, function($font) {
            $font->align('right');
            $font->valign('middle');
            $font->file(2);
            $font->color('000000');
        });
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $this->assertEquals('870034642cfd26ebd165ca0de5a8c12c', $img->checksum());
    }

    public function testRectangleImage()
    {
        $img = $this->getTestImage();
        $img = $img->rectangle('cccccc', 10, 10, 100, 100, false);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(10, 10, 'hex'));
        $this->assertEquals('#ffffff', $img->pickColor(50, 50, 'hex'));

        $img = $this->getTestImage();
        $img = $img->rectangle('cccccc', 10, 10, 100, 100, true);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(10, 10, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(50, 50, 'hex'));
    }

    public function testLineImage()
    {
        $img = $this->getTestImage();
        $img = $img->line('cccccc', 10, 10, 100, 100);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(10, 10, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(100, 100, 'hex'));
    }

    public function testEllipseImage()
    {
        $img = $this->getTestImage();
        $img = $img->ellipse('cccccc', 0, 0, 100, 50, false);
        $img = $img->ellipse('666666', 100, 100, 50, 100, true);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(50, 0, 'hex'));
        $this->assertEquals('#666666', $img->pickColor(100, 100, 'hex'));
    }

    public function testCircleImage()
    {
        $img = $this->getTestImage();
        $img = $img->circle('cccccc', 0, 0, 100, false);
        $img = $img->circle('666666', 100, 100, 50, true);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(100, 0, 'hex'));
        $this->assertEquals('#cccccc', $img->pickColor(0, 100, 'hex'));
        $this->assertEquals('#666666', $img->pickColor(100, 100, 'hex'));
    }

    public function testInsertImageWithAlphaChannel()
    {
        $img = new Image(null, 50, 50, '#ff0000');
        $img->insert('public/circle.png');
        $this->assertEquals('#ff0000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#320000', $img->pickColor(30, 30, 'hex'));
    }

    public function testInsertPng8WithAlphaChannel()
    {
        $img = new Image(null, 16, 16, '#ff0000');
        $img->insert('public/png8.png');
        $this->assertEquals('#ff0000', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#8c8c8c', $img->pickColor(10, 10, 'hex'));
    }

    public function testResetImage()
    {
        $img = $this->getTestImage();
        $img->backup();
        $img->resize(300, 200);
        $img->reset();
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
    }

    public function testResetEmptyImage()
    {
        $img = new Image(null, 800, 600, '#0000ff');
        $img->backup();
        $img->resize(300, 200);
        $img->fill('#00ff00');
        $img->reset();
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
        $this->assertEquals('#0000ff', $img->pickColor(0, 0, 'hex'));
    }

    /**
     * @expectedException Intervention\Image\Exception\ImageBackupNotAvailableException
     */
    public function testResetImageWithoutBackup()
    {
        $img = $this->getTestImage();
        $img->reset();
    }

    public function testBackup()
    {
        $img = new Image(null, 800, 600, '#0000ff');
        $img->fill('#00ff00');
        $img->backup();
        $img->resize(200, 200);
        $img->reset();
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 800);
        $this->assertEquals($img->height, 600);
        $this->assertEquals('#00ff00', $img->pickColor(0, 0, 'hex'));

        $img = new Image('public/tile.png');
        $img->resize(10, 10);
        $img->fill('#00ff00');
        $img->backup();
        $img->resize(5, 5);
        $img->reset();
        $this->assertInternalType('int', $img->width);
        $this->assertInternalType('int', $img->height);
        $this->assertEquals($img->width, 10);
        $this->assertEquals($img->height, 10);
        $this->assertEquals('#00ff00', $img->pickColor(0, 0, 'hex'));
    }

    public function testBackupKeepTransparency($value='')
    {
        $img = new Image('public/circle.png');
        $img->backup();
        $img->reset();
        $transparent = array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0.0);
        $this->assertEquals($transparent, $img->pickColor(0, 0, 'array'));
    }

    public function testLimitColors()
    {
        // reduce colors
        $img = Image::make('public/test.jpg');
        $img->limitColors(10);
        $this->assertEquals(imagecolorstotal($img->resource), 11);

        // reduce colors + keep transparency with matte
        $img = Image::make('public/mask2.png');
        $img->limitColors(10, '#ff0000'); // red matte
        $this->assertEquals(imagecolorstotal($img->resource), 11);
        $color1 = $img->pickColor(0, 0); // full transparent
        $color2 = $img->pickColor(9, 17); // part of matte gradient
        $this->assertEquals($color1['r'], 255);
        $this->assertEquals($color1['g'], 0);
        $this->assertEquals($color1['b'], 0);
        $this->assertEquals($color1['a'], 0);
        $this->assertEquals($color2['r'], 252);
        $this->assertEquals($color2['g'], 10);
        $this->assertEquals($color2['b'], 11);
        $this->assertEquals($color2['a'], 1);

        // increase colors
        $img = Image::make('public/png8.png');
        $img->limitColors(null); // set image to true color
        $this->assertEquals(imagecolorstotal($img->resource), 0);

        // increase colors + keep transparency with matte
        $img = Image::make('public/png8.png');
        $img->limitColors(null); // set image to true color
        $this->assertEquals(imagecolorstotal($img->resource), 0);
        $color1 = $img->pickColor(0, 0); // full transparent
        $color2 = $img->pickColor(10, 10); // solid color
        $this->assertEquals($color1['r'], 0);
        $this->assertEquals($color1['g'], 0);
        $this->assertEquals($color1['b'], 0);
        $this->assertEquals($color1['a'], 0);
        $this->assertEquals($color2['r'], 140);
        $this->assertEquals($color2['g'], 140);
        $this->assertEquals($color2['b'], 140);
        $this->assertEquals($color2['a'], 1);
    }

    public function testInterlaceImage()
    {
        $img = Image::make('public/trim.png');
        $img->interlace();
        $contents = $img->encode();
        $this->assertEquals(( ord($contents[28]) != '0' ), true);
        $img->interlace(false);
        $contents = $img->encode();
        $this->assertEquals(( ord($contents[28]) != '0' ), false);
    }

    public function testGammaImage()
    {
        $img = Image::make('public/tile.png');
        $img->gamma(1.0, 1.6);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
        $color1 = $img->pickColor(0, 0, 'hex');
        $color2 = $img->pickColor(10, 10, 'hex');
        $this->assertEquals('#cdeb00', $color1);
        $this->assertEquals('#707d8a', $color2);
    }

    public function testSaveImage()
    {
        $save_as = 'public/test2.jpg';
        $img = $this->getTestImage();
        $img->save($save_as);
        $this->assertFileExists($save_as);
        $this->assertEquals($img->dirname, 'public');
        $this->assertEquals($img->basename, 'test2.jpg');
        $this->assertEquals($img->extension, 'jpg');
        $this->assertEquals($img->filename, 'test2');
        @unlink($save_as);

        $save_as = 'public/test2.png';
        $img = $this->getTestImage();
        $img->save($save_as, 80);
        $this->assertEquals($img->dirname, 'public');
        $this->assertEquals($img->basename, 'test2.png');
        $this->assertEquals($img->extension, 'png');
        $this->assertEquals($img->filename, 'test2');
        $this->assertFileExists($save_as);
        @unlink($save_as);

        $save_as = 'public/test2.jpg';
        $img = $this->getTestImage();
        $img->save($save_as, 0);
        $this->assertEquals($img->dirname, 'public');
        $this->assertEquals($img->basename, 'test2.jpg');
        $this->assertEquals($img->extension, 'jpg');
        $this->assertEquals($img->filename, 'test2');
        $this->assertFileExists($save_as);
        @unlink($save_as);
    }

    public function testStringConversion()
    {
        $img = $this->getTestImage();
        $img = strval($img);
        $this->assertInternalType('string', $img);
    }

    public function testPickColor()
    {
        $img = $this->getTestImage();

        // rgb color array (default)
        $color = $img->pickColor(799, 599);
        $this->assertInternalType('array', $color);
        $this->assertInternalType('int', $color['r']);
        $this->assertEquals($color['r'], 255);
        $this->assertInternalType('int', $color['g']);
        $this->assertEquals($color['g'], 166);
        $this->assertInternalType('int', $color['b']);
        $this->assertEquals($color['b'], 0);
        $this->assertInternalType('float', $color['a']);
        $this->assertEquals($color['a'], 1);

        // int color
        $color = $img->pickColor(100, 100, 'int');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 16776956);

        // rgb color string
        $color = $img->pickColor(799, 599, 'rgb');
        $this->assertInternalType('string', $color);
        $this->assertEquals($color, 'rgb(255, 166, 0)');

        // hex color string
        $color = $img->pickColor(799, 599, 'hex');
        $this->assertInternalType('string', $color);
        $this->assertEquals($color, '#ffa600');

        // pick semi-transparent color
        $img = Image::make('public/circle.png');
        $color = $img->pickColor(20, 20, 'array');
        $this->assertInternalType('array', $color);
        $this->assertInternalType('int', $color['r']);
        $this->assertInternalType('int', $color['g']);
        $this->assertInternalType('int', $color['b']);
        $this->assertInternalType('float', $color['a']);
        $this->assertEquals($color['r'], 0);
        $this->assertEquals($color['g'], 0);
        $this->assertEquals($color['b'], 0);
        $this->assertEquals($color['a'], 0.8);
        $color = $img->pickColor(20, 20, 'rgba');
        $this->assertInternalType('string', $color);
        $this->assertEquals($color, 'rgba(0, 0, 0, 0.80)');
    }

    public function testParseColor()
    {
        $img = $this->getTestImage();
        $color = $img->parseColor(array(155, 155, 155));
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 10197915);

        $color = $img->parseColor('#cccccc');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 13421772);

        $color = $img->parseColor('cccccc');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 13421772);

        $color = $img->parseColor('#ccc');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 13421772);

        $color = $img->parseColor('ccc');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 13421772);

        $color = $img->parseColor('rgb(1, 14, 144)');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 69264);

        $color = $img->parseColor('rgb (255, 255, 255)');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 16777215);

        $color = $img->parseColor('rgb(0,0,0)');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 0);

        $color = $img->parseColor('rgba(0,0,0,0)');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 2130706432);

        $color = $img->parseColor('rgba(0,0,0,0.5)');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 1073741824);

        $color = $img->parseColor('rgba(255, 0, 0, 0.5)');
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 1090453504);

        $color = $img->parseColor(array(0, 0, 0, 0.5));
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 1073741824);

        $color = $img->parseColor(array(0, 0, 0, 0));
        $this->assertInternalType('int', $color);
        $this->assertEquals($color, 2130706432);
    }

    /**
     * @expectedException Intervention\Image\Exception\ImageColorException
     */
    public function testParseColorInvalidRGBColor()
    {
        $img = $this->getTestImage();
        $img->parseColor('rgb()');
    }

    /**
     * @expectedException Intervention\Image\Exception\ImageColorException
     */
    public function testParseColorInvalidHexColor()
    {
        $img = $this->getTestImage();
        $img->parseColor('ab');
    }

    public function testBrightnessImage()
    {
        $img = $this->getTestImage();
        $img->brightness(100);
        $img->brightness(-100);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
    }

    /**
     * @expectedException Intervention\Image\Exception\BrightnessOutOfBoundsException
     */
    public function testBrightnessOutOfBoundsHigh()
    {
        $img = $this->getTestImage();
        $img->brightness(101);
    }

    /**
     * @expectedException Intervention\Image\Exception\BrightnessOutOfBoundsException
     */
    public function testBrightnessOutOfBoundsLow()
    {
        $img = $this->getTestImage();
        $img->brightness(-101);
    }

    public function testContrastImage()
    {
        $img = $this->getTestImage();
        $img->contrast(100);
        $img->contrast(-100);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
    }

    /**
     * @expectedException Intervention\Image\Exception\ContrastOutOfBoundsException
     */
    public function testContrastOutOfBoundsHigh()
    {
        $img = $this->getTestImage();
        $img->contrast(101);
    }

    /**
     * @expectedException Intervention\Image\Exception\ContrastOutOfBoundsException
     */
    public function testContrastOutOfBoundsLow()
    {
        $img = $this->getTestImage();
        $img->contrast(-101);
    }

    public function testColorizeImage()
    {
        $img = $this->getTestImage();
        $img->colorize(-100, 0, 100);
        $img->colorize(100, -100, -100);
        $this->assertInstanceOf('Intervention\Image\Image', $img);
    }

    /**
     * @expectedException Intervention\Image\Exception\ColorizeOutOfBoundsException
     */
    public function testColorizeOutOfBounds()
    {
        $img = $this->getTestImage();
        $img->colorize(-101, 0, 0);
    }

    public function testEncode()
    {
        // default encoding
        $data = Image::make('public/circle.png')->encode();
        $this->assertInternalType('resource', @imagecreatefromstring($data));

        // jpg encoding
        $data = Image::make('public/circle.png')->encode('jpg');
        $this->assertInternalType('resource', @imagecreatefromstring($data));

        // gif encoding
        $data = Image::make('public/circle.png')->encode('gif');
        $this->assertInternalType('resource', @imagecreatefromstring($data));

        // data-url encoding
        $data = Image::make('public/circle.png')->encode('data-url');
        $encoded = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAACrUlEQVRogeXav09TURjG8Q/XuBDCQIjiRCQMBLUDCQkDLBgnnAz/A9H/yB+MOjto4gJxMJGEhIGYhgGIGAfjAEthcnB4aYFrW9rb0vbE79b7o/d57rnnnPe87xnSPWYxgylMYgJjGDk/X8ExfuEIh9hDuRsPH+rw/hIWMI+5gv+xg21sYbeokKJGFrGMxy7eeKdUsIFNfGn35naNPMRTrGC43Ye1yBk+4gO+tXrTrTYesIrnWMLttqS1x208wKPz3y31oVaM3MEaXojO2yvGxEsbwQFOm118nZEp0QrPuiKtGCWM4ztOGl3UzEjVxJOuyirGtDBzoIGZRkbuGBwTVe5jVAwA/3xmjYys6e/n1IhpMdJ+zZ+oZ2RVdOxBpSQihCujWd7IQ/FJ9XJ0KsI9Ed78rh7Ichc8Fc036EwLrTUuG1kUM3YqrAjNuGpk2c2FHTfBsNCMCyMlEQCmxmOhvWZkQfei2F4yIrTXjMz3T0vHzBNGZhVfFA0Cc5jNxPI0dWYyERymzlQmEgWpM5mJbEfqTGQGP65qhbFMmvNHnpF80JgsmcgnpU4lE4uU1DnORC42dX5lIqGcOkeZyIqnzmEm1r6ps5eJbMROv5V0wA7K1Xlku59KOmSbi4XVljTnk4rQXjOyK4osqbHhvMp1OUTZFEWWVDgTmnE10/gDd0WRJQXe4131Rz5o/ID9nsopxr7QWiOf+63mUpd6Iqc4L/H58oF62fiyWKOUeiCoCG+xnj/YqD5yICpEg5bQ/oTX2ij0nIqa3bioFA0Cm3iFn/VONqshnoiWGdX/lvkkTDQMcK+r6p6Imt2Q/vWZt+JzqtsSVVqps5+Kmt2xqBT1KuuyL0anddfU2Glv50NZhPx/RL+5qd0PZ2KyeyM3xDbjv91Ukyf5bU716OvGs7+Rl30L8vF70gAAAABJRU5ErkJggg==';
        $this->assertEquals($data, $encoded);
    }

    public function testExifRead()
    {
        // read all data
        $data = Image::make('public/exif.jpg')->exif();
        $this->assertInternalType('array', $data);
        $this->assertEquals(count($data), 19);

        // read key
        $data = Image::make('public/exif.jpg')->exif('Artist');
        $this->assertInternalType('string', $data);
        $this->assertEquals($data, 'Oliver Vogel');

        // read image with no exif data
        $data = Image::make('public/test.jpg')->exif();
        $this->assertEquals($data, null);

        // read key that doesn't exist
        $data = Image::make('public/exif.jpg')->exif('xxx');
        $this->assertEquals($data, null);
    }

    public function testTrim()
    {  
        $img = Image::make('public/trim.png');
        $img->trim();
        $this->assertEquals($img->width, 28);
        $this->assertEquals($img->height, 28);
        $this->assertEquals('#ffa601', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#ffa601', $img->pickColor(21, 21, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left');
        $this->assertEquals($img->width, 28);
        $this->assertEquals($img->height, 28);
        $this->assertEquals('#ffa601', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#ffa601', $img->pickColor(21, 21, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('bottom-right');
        $this->assertEquals($img->width, 28);
        $this->assertEquals($img->height, 28);
        $this->assertEquals('#ffa601', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#ffa601', $img->pickColor(21, 21, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('transparent');
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 50);
        $this->assertEquals('#00aef0', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#ffa601', $img->pickColor(21, 21, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('top', 'bottom'));
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 28);
        $this->assertEquals('#00aef0', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#f6a609', $img->pickColor(25, 0, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('left', 'right'));
        $this->assertEquals($img->width, 28);
        $this->assertEquals($img->height, 50);
        $this->assertEquals('#f6a609', $img->pickColor(0, 24, 'hex'));
        $this->assertEquals('#00aef0', $img->pickColor(27, 49, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('bottom', 'right'));
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 39);
        $this->assertEquals('#4eaca7', $img->pickColor(38, 20, 'hex'));
        $this->assertEquals('#88aa71', $img->pickColor(28, 38, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('bottom', 'left'));
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 39);
        $this->assertEquals('#b8a844', $img->pickColor(0, 22, 'hex'));
        $this->assertEquals('#b8a844', $img->pickColor(11, 11, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('top', 'left'));
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 39);
        $this->assertEquals('#f6a609', $img->pickColor(14, 0, 'hex'));
        $this->assertEquals('#b8a844', $img->pickColor(0, 16, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('top', 'right'));
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 39);
        $this->assertEquals('#f6a609', $img->pickColor(24, 0, 'hex'));
        $this->assertEquals('#b8a844', $img->pickColor(11, 11, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('top', 'right', 'bottom'));
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 28);
        $this->assertEquals('#f6a609', $img->pickColor(24, 0, 'hex'));
        $this->assertEquals('#b8a844', $img->pickColor(11, 11, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('right', 'bottom', 'left'));
        $this->assertEquals($img->width, 28);
        $this->assertEquals($img->height, 39);
        $this->assertEquals('#b8a844', $img->pickColor(0, 22, 'hex'));
        $this->assertEquals('#b8a844', $img->pickColor(27, 27, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('bottom', 'left', 'top'));
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 28);
        $this->assertEquals('#f6a609', $img->pickColor(13, 0, 'hex'));
        $this->assertEquals('#88aa71', $img->pickColor(0, 17, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('left', 'top', 'right'));
        $this->assertEquals($img->width, 28);
        $this->assertEquals($img->height, 39);
        $this->assertEquals('#f6a609', $img->pickColor(13, 0, 'hex'));
        $this->assertEquals('#ffa601', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#88aa71', $img->pickColor(0, 17, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('bottom'));
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 39);
        $this->assertEquals('#00aef0', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#f6a609', $img->pickColor(11, 24, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', array('right'));
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 50);
        $this->assertEquals('#00aef0', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#f6a609', $img->pickColor(11, 25, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('top-left', 'right');
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 50);
        $this->assertEquals('#00aef0', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#f6a609', $img->pickColor(11, 25, 'hex'));

        $img = Image::make('public/trim.png');
        $img->trim('bottom-right', 'right');
        $this->assertEquals($img->width, 39);
        $this->assertEquals($img->height, 50);
        $this->assertEquals('#00aef0', $img->pickColor(6, 6, 'hex'));
        $this->assertEquals('#f6a609', $img->pickColor(11, 25, 'hex'));

        $img = Image::make('public/mask1.png');
        $img->trim('bottom-right');
        $this->assertEquals($img->width, 17);
        $this->assertEquals($img->height, 17);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(16, 16, 'hex'));

        $img = Image::make('public/mask2.png');
        $img->trim('bottom-right');
        $this->assertEquals($img->width, 20);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('rgba(255, 255, 255, 0.72)', $img->pickColor(6, 6, 'rgba'));
        $this->assertEquals('rgba(0, 0, 0, 0.00)', $img->pickColor(19, 19, 'rgba'));

        $img = Image::make('public/mask2.png');
        $img->trim('transparent');
        $this->assertEquals($img->width, 20);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('rgba(255, 255, 255, 0.72)', $img->pickColor(6, 6, 'rgba'));
        $this->assertEquals('rgba(0, 0, 0, 0.00)', $img->pickColor(19, 19, 'rgba'));

        $img = Image::make('public/mask2.png');
        $img->trim('transparent', array('TOP', 'BOTTOM'));
        $this->assertEquals($img->width, 32);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('rgba(255, 255, 255, 0.72)', $img->pickColor(12, 6, 'rgba'));
        $this->assertEquals('rgba(0, 0, 0, 0.00)', $img->pickColor(31, 19, 'rgba'));

        $img = Image::make('public/exif.jpg');
        $img->trim(); // trim nothing because image is just one color
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);

        // trim selfmade image
        $img = Image::canvas(1, 1, '000000');
        $img->resizeCanvas(25, 25, 'center', false, 'ffffff');
        $this->assertEquals($img->width, 25);
        $this->assertEquals($img->height, 25);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));
        $img->trim();
        $this->assertEquals($img->width, 1);
        $this->assertEquals($img->height, 1);
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        // try to trim non-transparent image with transparency
        $img = Image::make('public/gradient.png');
        $img->trim('transparent', null);
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 50);
    }

    public function testTrimWithTolerance()
    {
        // prepare test image
        $canvas = Image::canvas(1, 1, '000000');
        $canvas->resizeCanvas(5, 5, 'center', false, '808080');
        $canvas->resizeCanvas(11, 11, 'center', false, 'ffffff');
        $this->assertEquals($canvas->width, 11);
        $this->assertEquals($canvas->height, 11);
        $this->assertEquals('#000000', $canvas->pickColor(5, 5, 'hex'));
        $this->assertEquals('#808080', $canvas->pickColor(3, 3, 'hex'));
        $this->assertEquals('#ffffff', $canvas->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(); // trim without tolerance (should trim away ffffff)
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('#000000', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#808080', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 30); // trim with 40 tolerance (should not touch 808080)
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('#000000', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#808080', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 50); // trim with 50 tolerance (should only leave 000000)
        $this->assertEquals($img->width, 1);
        $this->assertEquals($img->height, 1);
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 100); // trim with 100 tolerance (should leave image as is)
        $this->assertEquals($img->width, 11);
        $this->assertEquals($img->height, 11);
        $this->assertEquals('#000000', $img->pickColor(5, 5, 'hex'));
        $this->assertEquals('#808080', $img->pickColor(3, 3, 'hex'));
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));

        // prepare test image
        $canvas = Image::canvas(1, 1, '000000');
        $canvas->resizeCanvas(5, 5, 'center', false, '804040');
        $canvas->resizeCanvas(11, 11, 'center', false, 'ffffff');
        $this->assertEquals($canvas->width, 11);
        $this->assertEquals($canvas->height, 11);
        $this->assertEquals('#000000', $canvas->pickColor(5, 5, 'hex'));
        $this->assertEquals('#804040', $canvas->pickColor(3, 3, 'hex'));
        $this->assertEquals('#ffffff', $canvas->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(); // trim without tolerance (should trim away ffffff)
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('#000000', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#804040', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 30); // trim with 40 tolerance (should not touch 804040)
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('#000000', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#804040', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 50); // trim with 50 tolerance (should not touch 804040)
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('#000000', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#804040', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 80); // trim with 80 tolerance (should only leave 000000)
        $this->assertEquals($img->width, 1);
        $this->assertEquals($img->height, 1);
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 100); // trim with 100 tolerance (should leave image as is)
        $this->assertEquals($img->width, 11);
        $this->assertEquals($img->height, 11);
        $this->assertEquals('#000000', $img->pickColor(5, 5, 'hex'));
        $this->assertEquals('#804040', $img->pickColor(3, 3, 'hex'));
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));

        // prepare test image
        $canvas = Image::canvas(1, 1, 'ffffff'); // core
        $canvas->resizeCanvas(5, 5, 'center', false, 'd90000'); // 85%
        $canvas->resizeCanvas(11, 11, 'center', false, '008000'); // 50%
        $canvas->resizeCanvas(16, 16, 'center', false, '333333'); // 20%
        $canvas->resizeCanvas(20, 20, 'center', false, '000000'); // outer
        $this->assertEquals($canvas->width, 20);
        $this->assertEquals($canvas->height, 20);
        $this->assertEquals('#ffffff', $canvas->pickColor(9, 9, 'hex'));
        $this->assertEquals('#d90000', $canvas->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $canvas->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $canvas->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $canvas->pickColor(0, 0, 'hex'));
        
        $img = clone $canvas;
        $img->trim(); // trim without tolerance (should only trim outer)
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);
        $this->assertEquals('#ffffff', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(5, 5, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 19); // trim with 19% tolerance (should leave 20%)
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);
        $this->assertEquals('#ffffff', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(5, 5, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 20); // trim with 20% tolerance (should trim 20% and leave 50%)
        $this->assertEquals($img->width, 11);
        $this->assertEquals($img->height, 11);
        $this->assertEquals('#ffffff', $img->pickColor(5, 5, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(3, 3, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 49); // trim with 49% tolerance (should leave 49%)
        $this->assertEquals($img->width, 11);
        $this->assertEquals($img->height, 11);
        $this->assertEquals('#ffffff', $img->pickColor(5, 5, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(3, 3, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 50); // trim with 50% tolerance (should trim 50% and leave 85%)
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('#ffffff', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 84); // trim with 84% tolerance (should leave 85%)
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('#ffffff', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 85); // trim with 85% tolerance (should trim 85% and leave core)
        $this->assertEquals($img->width, 1);
        $this->assertEquals($img->height, 1);
        $this->assertEquals('#ffffff', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, null, 100); // trim with 100% tolerance (should leave image as is)
        $this->assertEquals($img->width, 20);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left'); // trim without tolerance (should only trim right outer)
        $this->assertEquals($img->width, 18);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(7, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left', 19); // trim with 19% tolerance (should leave 20%)
        $this->assertEquals($img->width, 18);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(7, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left', 20); // trim with 20% tolerance (should trim 20% and leave 50%)
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(5, 9, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left', 49); // trim with 49% tolerance (should leave 50%)
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(5, 9, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left', 50); // trim with 50% tolerance (should trim 50% and leave 85%)
        $this->assertEquals($img->width, 13);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(2, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(0, 9, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left', 84); // trim with 84% tolerance (should leave 85%)
        $this->assertEquals($img->width, 13);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(2, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(0, 9, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left', 85); // trim with 85% tolerance (should trim 85% and leave core)
        $this->assertEquals($img->width, 11);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(0, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(0, 8, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        $img = clone $canvas;
        $img->trim(null, 'left', 100); // trim with 100% tolerance (should leave image as is)
        $this->assertEquals($img->width, 20);
        $this->assertEquals($img->height, 20);
        $this->assertEquals('#ffffff', $img->pickColor(9, 9, 'hex'));
        $this->assertEquals('#d90000', $img->pickColor(7, 7, 'hex'));
        $this->assertEquals('#008000', $img->pickColor(4, 4, 'hex'));
        $this->assertEquals('#333333', $img->pickColor(2, 2, 'hex'));
        $this->assertEquals('#000000', $img->pickColor(0, 0, 'hex'));

        // prepare test image
        $canvas = Image::canvas(1, 1, '000000');
        $canvas->resizeCanvas(5, 5, 'center', false, array(255, 255, 255, 0.5));
        $canvas->resizeCanvas(11, 11, 'center', false, array(0, 0, 0, 0));
        $this->assertEquals($canvas->width, 11);
        $this->assertEquals($canvas->height, 11);
        $this->assertEquals('rgba(0, 0, 0, 0.00)', $canvas->pickColor(0, 0, 'rgba'));
        $this->assertEquals('rgba(255, 255, 255, 0.50)', $canvas->pickColor(3, 3, 'rgba'));
        $this->assertEquals('rgba(0, 0, 0, 1.00)', $canvas->pickColor(5, 5, 'rgba'));

        $img = clone $canvas;
        $img->trim('transparent', null);
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('rgba(255, 255, 255, 0.50)', $img->pickColor(0, 0, 'rgba'));
        $this->assertEquals('rgba(0, 0, 0, 1.00)', $img->pickColor(2, 2, 'rgba'));

        $img = clone $canvas;
        $img->trim('transparent', null, 40);
        $this->assertEquals($img->width, 5);
        $this->assertEquals($img->height, 5);
        $this->assertEquals('rgba(255, 255, 255, 0.50)', $img->pickColor(0, 0, 'rgba'));
        $this->assertEquals('rgba(0, 0, 0, 1.00)', $img->pickColor(2, 2, 'rgba'));

        $img = clone $canvas;
        $img->trim('transparent', null, 50);
        $this->assertEquals($img->width, 1);
        $this->assertEquals($img->height, 1);
        $this->assertEquals('rgba(0, 0, 0, 1.00)', $img->pickColor(0, 0, 'rgba'));

        $img = clone $canvas;
        $img->trim('transparent', null, 100);
        $this->assertEquals($img->width, 11);
        $this->assertEquals($img->height, 11);
        $this->assertEquals('rgba(0, 0, 0, 0.00)', $img->pickColor(0, 0, 'rgba'));
        $this->assertEquals('rgba(255, 255, 255, 0.50)', $img->pickColor(3, 3, 'rgba'));
        $this->assertEquals('rgba(0, 0, 0, 1.00)', $img->pickColor(5, 5, 'rgba'));

        // trim gradient
        $canvas = Image::make('public/gradient.png');

        $img = clone $canvas;
        $img->trim();
        $this->assertEquals($img->width, 46);
        $this->assertEquals($img->height, 46);

        $img = clone $canvas;
        $img->trim(null, null, 10);
        $this->assertEquals($img->width, 38);
        $this->assertEquals($img->height, 38);

        $img = clone $canvas;
        $img->trim(null, null, 20);
        $this->assertEquals($img->width, 34);
        $this->assertEquals($img->height, 34);

        $img = clone $canvas;
        $img->trim(null, null, 30);
        $this->assertEquals($img->width, 30);
        $this->assertEquals($img->height, 30);

        $img = clone $canvas;
        $img->trim(null, null, 40);
        $this->assertEquals($img->width, 26);
        $this->assertEquals($img->height, 26);

        $img = clone $canvas;
        $img->trim(null, null, 50);
        $this->assertEquals($img->width, 22);
        $this->assertEquals($img->height, 22);

        $img = clone $canvas;
        $img->trim(null, null, 60);
        $this->assertEquals($img->width, 20);
        $this->assertEquals($img->height, 20);

        $img = clone $canvas;
        $img->trim(null, null, 70);
        $this->assertEquals($img->width, 16);
        $this->assertEquals($img->height, 16);

        $img = clone $canvas;
        $img->trim(null, null, 80);
        $this->assertEquals($img->width, 12);
        $this->assertEquals($img->height, 12);

        $img = clone $canvas;
        $img->trim(null, null, 90);
        $this->assertEquals($img->width, 8);
        $this->assertEquals($img->height, 8);
    }

    /**
     * @expectedException Intervention\Image\Exception\TrimToleranceOutOfBoundsException
     */
    public function testTrimToleranceOutOfBounds()
    {
        $img = new Image;
        $img->trim(null, null, 200);
    }

    public function testTrimWithFeather()
    {
        $canvas = Image::make('public/trim.png');

        $img = clone $canvas;
        $feather = 5;
        $img->trim(null, null, null, $feather);
        $this->assertEquals($img->width, 28 + $feather * 2);
        $this->assertEquals($img->height, 28 + $feather * 2);

        $img = clone $canvas;
        $feather = 10;
        $img->trim(null, null, null, $feather);
        $this->assertEquals($img->width, 28 + $feather * 2);
        $this->assertEquals($img->height, 28 + $feather * 2);

        $img = clone $canvas;
        $feather = 20; // must respect original dimensions of image
        $img->trim(null, null, null, $feather);
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 50);

        $img = clone $canvas;
        $feather = -5;
        $img->trim(null, null, null, $feather);
        $this->assertEquals($img->width, 28 + $feather * 2);
        $this->assertEquals($img->height, 28 + $feather * 2);

        $img = clone $canvas;
        $feather = -10;
        $img->trim(null, null, null, $feather);
        $this->assertEquals($img->width, 28 + $feather * 2);
        $this->assertEquals($img->height, 28 + $feather * 2);

        // trim only left and right with feather
        $img = clone $canvas;
        $feather = 10;
        $img->trim(null, array('left', 'right'), null, $feather);
        $this->assertEquals($img->width, 28 + $feather * 2);
        $this->assertEquals($img->height, 50);

        // trim only top and bottom with feather
        $img = clone $canvas;
        $feather = 10;
        $img->trim(null, array('top', 'bottom'), null, $feather);
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 28 + $feather * 2);

        // trim with tolerance and feather
        $canvas = Image::make('public/gradient.png');

        $img = clone $canvas;
        $feather = 2;
        $img->trim(null, null, 10, $feather);
        $this->assertEquals($img->width, 38 + $feather * 2);
        $this->assertEquals($img->height, 38 + $feather * 2);

        $img = clone $canvas;
        $feather = 5;
        $img->trim(null, null, 10, $feather);
        $this->assertEquals($img->width, 38 + $feather * 2);
        $this->assertEquals($img->height, 38 + $feather * 2);

        $img = clone $canvas;
        $feather = 10; // should respect original dimensions
        $img->trim(null, null, 20, $feather);
        $this->assertEquals($img->width, 50);
        $this->assertEquals($img->height, 50);
    }

    public function testEncoded()
    {
        $img = Image::make('public/test.jpg');
        $img->encode();
        $this->assertEquals($img->encoded, $img->encode());
    }

    public function testDestroy()
    {
        $img = $this->getTestImage();
        $img->destroy();
        $this->assertEquals(get_resource_type($img->resource), 'Unknown');
    }

    public function testChecksum()
    {
        $img = new Image('public/circle.png');
        $checksum = $img->checksum();
        $this->assertEquals($checksum, '149432c4e99e8bf8c295afb85be64e78');
    }
}
