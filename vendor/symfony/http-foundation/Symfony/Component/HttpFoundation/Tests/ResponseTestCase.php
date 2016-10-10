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

use Symfony\Component\HttpFoundation\Request;

abstract class ResponseTestCase extends \PHPUnit_Framework_TestCase
{
    public function testNoCacheControlHeaderOnAttachmentUsingHTTPSAndMSIE()
    {
        // Check for HTTPS and IE 8
        $request = new Request();
        $request->server->set('HTTPS', true);
        $request->server->set('HTTP_USER_AGENT', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)');

        $response = $this->provideResponse();
        $response->headers->set('Content-Disposition', 'attachment; filename="fname.ext"');
        $response->prepare($request);

        $this->assertFalse($response->headers->has('Cache-Control'));

        // Check for IE 10 and HTTPS
        $request->server->set('HTTP_USER_AGENT', 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)');

        $response = $this->provideResponse();
        $response->headers->set('Content-Disposition', 'attachment; filename="fname.ext"');
        $response->prepare($request);

        $this->assertTrue($response->headers->has('Cache-Control'));

        // Check for IE 9 and HTTPS
        $request->server->set('HTTP_USER_AGENT', 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 7.1; Trident/5.0)');

        $response = $this->provideResponse();
        $response->headers->set('Content-Disposition', 'attachment; filename="fname.ext"');
        $response->prepare($request);

        $this->assertTrue($response->headers->has('Cache-Control'));

        // Check for IE 9 and HTTP
        $request->server->set('HTTPS', false);

        $response = $this->provideResponse();
        $response->headers->set('Content-Disposition', 'attachment; filename="fname.ext"');
        $response->prepare($request);

        $this->assertTrue($response->headers->has('Cache-Control'));

        // Check for IE 8 and HTTP
        $request->server->set('HTTP_USER_AGENT', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)');

        $response = $this->provideResponse();
        $response->headers->set('Content-Disposition', 'attachment; filename="fname.ext"');
        $response->prepare($request);

        $this->assertTrue($response->headers->has('Cache-Control'));

        // Check for non-IE and HTTPS
        $request->server->set('HTTPS', true);
        $request->server->set('HTTP_USER_AGENT', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.60 Safari/537.17');

        $response = $this->provideResponse();
        $response->headers->set('Content-Disposition', 'attachment; filename="fname.ext"');
        $response->prepare($request);

        $this->assertTrue($response->headers->has('Cache-Control'));

        // Check for non-IE and HTTP
        $request->server->set('HTTPS', false);

        $response = $this->provideResponse();
        $response->headers->set('Content-Disposition', 'attachment; filename="fname.ext"');
        $response->prepare($request);

        $this->assertTrue($response->headers->has('Cache-Control'));
    }

    abstract protected function provideResponse();
}
