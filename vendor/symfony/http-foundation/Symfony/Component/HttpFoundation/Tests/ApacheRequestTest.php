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

use Symfony\Component\HttpFoundation\ApacheRequest;

class ApacheRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideServerVars
     */
    public function testUriMethods($server, $expectedRequestUri, $expectedBaseUrl, $expectedPathInfo)
    {
        $request = new ApacheRequest();
        $request->server->replace($server);

        $this->assertEquals($expectedRequestUri, $request->getRequestUri(), '->getRequestUri() is correct');
        $this->assertEquals($expectedBaseUrl, $request->getBaseUrl(), '->getBaseUrl() is correct');
        $this->assertEquals($expectedPathInfo, $request->getPathInfo(), '->getPathInfo() is correct');
    }

    public function provideServerVars()
    {
        return array(
            array(
                array(
                    'REQUEST_URI' => '/foo/app_dev.php/bar',
                    'SCRIPT_NAME' => '/foo/app_dev.php',
                    'PATH_INFO'   => '/bar',
                ),
                '/foo/app_dev.php/bar',
                '/foo/app_dev.php',
                '/bar',
            ),
            array(
                array(
                    'REQUEST_URI' => '/foo/bar',
                    'SCRIPT_NAME' => '/foo/app_dev.php',
                ),
                '/foo/bar',
                '/foo',
                '/bar',
            ),
            array(
                array(
                    'REQUEST_URI' => '/app_dev.php/foo/bar',
                    'SCRIPT_NAME' => '/app_dev.php',
                    'PATH_INFO'   => '/foo/bar',
                ),
                '/app_dev.php/foo/bar',
                '/app_dev.php',
                '/foo/bar',
            ),
            array(
                array(
                    'REQUEST_URI' => '/foo/bar',
                    'SCRIPT_NAME' => '/app_dev.php',
                ),
                '/foo/bar',
                '',
                '/foo/bar',
            ),
            array(
                array(
                    'REQUEST_URI' => '/app_dev.php',
                    'SCRIPT_NAME' => '/app_dev.php',
                ),
                '/app_dev.php',
                '/app_dev.php',
                '/',
            ),
            array(
                array(
                    'REQUEST_URI' => '/',
                    'SCRIPT_NAME' => '/app_dev.php',
                ),
                '/',
                '',
                '/',
            ),
        );
    }
}
