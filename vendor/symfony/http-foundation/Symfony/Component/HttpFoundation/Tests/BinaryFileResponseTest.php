<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Resources\stubs\FakeFile;

class BinaryFileResponseTest extends ResponseTestCase
{
    public function testConstruction()
    {
        $file = __DIR__.'/../README.md';
        $response = new BinaryFileResponse($file, 404, array('X-Header' => 'Foo'), true, null, true, true);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Foo', $response->headers->get('X-Header'));
        $this->assertTrue($response->headers->has('ETag'));
        $this->assertTrue($response->headers->has('Last-Modified'));
        $this->assertFalse($response->headers->has('Content-Disposition'));

        $response = BinaryFileResponse::create($file, 404, array(), true, ResponseHeaderBag::DISPOSITION_INLINE);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($response->headers->has('ETag'));
        $this->assertEquals('inline; filename="README.md"', $response->headers->get('Content-Disposition'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetContent()
    {
        $response = new BinaryFileResponse(__FILE__);
        $response->setContent('foo');
    }

    public function testGetContent()
    {
        $response = new BinaryFileResponse(__FILE__);
        $this->assertFalse($response->getContent());
    }

    /**
     * @dataProvider provideRanges
     */
    public function testRequests($requestRange, $offset, $length, $responseRange)
    {
        $response = BinaryFileResponse::create(__DIR__.'/File/Fixtures/test.gif')->setAutoEtag();

        // do a request to get the ETag
        $request = Request::create('/');
        $response->prepare($request);
        $etag = $response->headers->get('ETag');

        // prepare a request for a range of the testing file
        $request = Request::create('/');
        $request->headers->set('If-Range', $etag);
        $request->headers->set('Range', $requestRange);

        $file = fopen(__DIR__.'/File/Fixtures/test.gif', 'r');
        fseek($file, $offset);
        $data = fread($file, $length);
        fclose($file);

        $this->expectOutputString($data);
        $response = clone $response;
        $response->prepare($request);
        $response->sendContent();

        $this->assertEquals(206, $response->getStatusCode());
        $this->assertEquals('binary', $response->headers->get('Content-Transfer-Encoding'));
        $this->assertEquals($responseRange, $response->headers->get('Content-Range'));
    }

    public function provideRanges()
    {
        return array(
            array('bytes=1-4', 1, 4, 'bytes 1-4/35'),
            array('bytes=-5', 30, 5, 'bytes 30-34/35'),
            array('bytes=30-', 30, 5, 'bytes 30-34/35'),
            array('bytes=30-30', 30, 1, 'bytes 30-30/35'),
            array('bytes=30-34', 30, 5, 'bytes 30-34/35'),
        );
    }

    /**
     * @dataProvider provideFullFileRanges
     */
    public function testFullFileRequests($requestRange)
    {
        $response = BinaryFileResponse::create(__DIR__.'/File/Fixtures/test.gif')->setAutoEtag();

        // prepare a request for a range of the testing file
        $request = Request::create('/');
        $request->headers->set('Range', $requestRange);

        $file = fopen(__DIR__.'/File/Fixtures/test.gif', 'r');
        $data = fread($file, 35);
        fclose($file);

        $this->expectOutputString($data);
        $response = clone $response;
        $response->prepare($request);
        $response->sendContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('binary', $response->headers->get('Content-Transfer-Encoding'));
    }

    public function provideFullFileRanges()
    {
        return array(
            array('bytes=0-'),
            array('bytes=0-34'),
            array('bytes=-35'),
            // Syntactical invalid range-request should also return the full resource
            array('bytes=20-10'),
            array('bytes=50-40'),
        );
    }

    /**
     * @dataProvider provideInvalidRanges
     */
    public function testInvalidRequests($requestRange)
    {
        $response = BinaryFileResponse::create(__DIR__.'/File/Fixtures/test.gif')->setAutoEtag();

        // prepare a request for a range of the testing file
        $request = Request::create('/');
        $request->headers->set('Range', $requestRange);

        $response = clone $response;
        $response->prepare($request);
        $response->sendContent();

        $this->assertEquals(416, $response->getStatusCode());
        $this->assertEquals('binary', $response->headers->get('Content-Transfer-Encoding'));
        #$this->assertEquals('', $response->headers->get('Content-Range'));
    }

    public function provideInvalidRanges()
    {
        return array(
            array('bytes=-40'),
            array('bytes=30-40'),
        );
    }

    public function testXSendfile()
    {
        $request = Request::create('/');
        $request->headers->set('X-Sendfile-Type', 'X-Sendfile');

        BinaryFileResponse::trustXSendfileTypeHeader();
        $response = BinaryFileResponse::create(__DIR__.'/../README.md');
        $response->prepare($request);

        $this->expectOutputString('');
        $response->sendContent();

        $this->assertContains('README.md', $response->headers->get('X-Sendfile'));
    }

    /**
     * @dataProvider getSampleXAccelMappings
     */
    public function testXAccelMapping($realpath, $mapping, $virtual)
    {
        $request = Request::create('/');
        $request->headers->set('X-Sendfile-Type', 'X-Accel-Redirect');
        $request->headers->set('X-Accel-Mapping', $mapping);

        $file = new FakeFile($realpath, __DIR__.'/File/Fixtures/test');

        BinaryFileResponse::trustXSendFileTypeHeader();
        $response = new BinaryFileResponse($file);
        $reflection = new \ReflectionObject($response);
        $property = $reflection->getProperty('file');
        $property->setAccessible(true);
        $property->setValue($response, $file);

        $response->prepare($request);
        $this->assertEquals($virtual, $response->headers->get('X-Accel-Redirect'));
    }

    public function testSplFileObject()
    {
        $filePath = __DIR__.'/File/Fixtures/test';
        $file = new \SplFileObject($filePath);

        $response = new BinaryFileResponse($file);

        $this->assertEquals(realpath($response->getFile()->getPathname()), realpath($filePath));
    }

    public function getSampleXAccelMappings()
    {
        return array(
            array('/var/www/var/www/files/foo.txt', '/files/=/var/www/', '/files/var/www/files/foo.txt'),
            array('/home/foo/bar.txt', '/files/=/var/www/,/baz/=/home/foo/', '/baz/bar.txt'),
        );
    }

    protected function provideResponse()
    {
        return new BinaryFileResponse(__DIR__.'/../README.md');
    }
}
