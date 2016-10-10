<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\BrowserKit\Tests;

use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

class SpecialResponse extends Response
{
}

class TestClient extends Client
{
    protected $nextResponse = null;
    protected $nextScript = null;

    public function setNextResponse(Response $response)
    {
        $this->nextResponse = $response;
    }

    public function setNextScript($script)
    {
        $this->nextScript = $script;
    }

    protected function doRequest($request)
    {
        if (null === $this->nextResponse) {
            return new Response();
        }

        $response = $this->nextResponse;
        $this->nextResponse = null;

        return $response;
    }

    protected function filterResponse($response)
    {
        if ($response instanceof SpecialResponse) {
            return new Response($response->getContent(), $response->getStatus(), $response->getHeaders());
        }

        return $response;
    }

    protected function getScript($request)
    {
        $r = new \ReflectionClass('Symfony\Component\BrowserKit\Response');
        $path = $r->getFileName();

        return <<<EOF
<?php

require_once('$path');

echo serialize($this->nextScript);
EOF;
    }
}

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\Component\BrowserKit\Client::getHistory
     */
    public function testGetHistory()
    {
        $client = new TestClient(array(), $history = new History());
        $this->assertSame($history, $client->getHistory(), '->getHistory() returns the History');
    }

    /**
     * @covers Symfony\Component\BrowserKit\Client::getCookieJar
     */
    public function testGetCookieJar()
    {
        $client = new TestClient(array(), null, $cookieJar = new CookieJar());
        $this->assertSame($cookieJar, $client->getCookieJar(), '->getCookieJar() returns the CookieJar');
    }

    /**
     * @covers Symfony\Component\BrowserKit\Client::getRequest
     */
    public function testGetRequest()
    {
        $client = new TestClient();
        $client->request('GET', 'http://example.com/');

        $this->assertEquals('http://example.com/', $client->getRequest()->getUri(), '->getCrawler() returns the Request of the last request');
    }

    public function testGetRequestWithIpAsHost()
    {
        $client = new TestClient();
        $client->request('GET', 'https://example.com/foo', array(), array(), array('HTTP_HOST' => '127.0.0.1'));

        $this->assertEquals('https://127.0.0.1/foo', $client->getRequest()->getUri());
    }

    public function testGetResponse()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('foo'));
        $client->request('GET', 'http://example.com/');

        $this->assertEquals('foo', $client->getResponse()->getContent(), '->getCrawler() returns the Response of the last request');
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Response', $client->getResponse(), '->getCrawler() returns the Response of the last request');
    }

    public function testGetInternalResponse()
    {
        $client = new TestClient();
        $client->setNextResponse(new SpecialResponse('foo'));
        $client->request('GET', 'http://example.com/');

        $this->assertInstanceOf('Symfony\Component\BrowserKit\Response', $client->getInternalResponse());
        $this->assertNotInstanceOf('Symfony\Component\BrowserKit\Tests\SpecialResponse', $client->getInternalResponse());
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Tests\SpecialResponse', $client->getResponse());
    }

    public function testGetContent()
    {
        $json = '{"jsonrpc":"2.0","method":"echo","id":7,"params":["Hello World"]}';

        $client = new TestClient();
        $client->request('POST', 'http://example.com/jsonrpc', array(), array(), array(), $json);
        $this->assertEquals($json, $client->getRequest()->getContent());
    }

    /**
     * @covers Symfony\Component\BrowserKit\Client::getCrawler
     */
    public function testGetCrawler()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('foo'));
        $crawler = $client->request('GET', 'http://example.com/');

        $this->assertSame($crawler, $client->getCrawler(), '->getCrawler() returns the Crawler of the last request');
    }

    public function testRequestHttpHeaders()
    {
        $client = new TestClient();
        $client->request('GET', '/');
        $headers = $client->getRequest()->getServer();
        $this->assertEquals('localhost', $headers['HTTP_HOST'], '->request() sets the HTTP_HOST header');

        $client = new TestClient();
        $client->request('GET', 'http://www.example.com');
        $headers = $client->getRequest()->getServer();
        $this->assertEquals('www.example.com', $headers['HTTP_HOST'], '->request() sets the HTTP_HOST header');

        $client->request('GET', 'https://www.example.com');
        $headers = $client->getRequest()->getServer();
        $this->assertTrue($headers['HTTPS'], '->request() sets the HTTPS header');

        $client = new TestClient();
        $client->request('GET', 'http://www.example.com:8080');
        $headers = $client->getRequest()->getServer();
        $this->assertEquals('www.example.com:8080', $headers['HTTP_HOST'], '->request() sets the HTTP_HOST header with port');
    }

    public function testRequestURIConversion()
    {
        $client = new TestClient();
        $client->request('GET', '/foo');
        $this->assertEquals('http://localhost/foo', $client->getRequest()->getUri(), '->request() converts the URI to an absolute one');

        $client = new TestClient();
        $client->request('GET', 'http://www.example.com');
        $this->assertEquals('http://www.example.com', $client->getRequest()->getUri(), '->request() does not change absolute URIs');

        $client = new TestClient();
        $client->request('GET', 'http://www.example.com/');
        $client->request('GET', '/foo');
        $this->assertEquals('http://www.example.com/foo', $client->getRequest()->getUri(), '->request() uses the previous request for relative URLs');

        $client = new TestClient();
        $client->request('GET', 'http://www.example.com/foo');
        $client->request('GET', '#');
        $this->assertEquals('http://www.example.com/foo#', $client->getRequest()->getUri(), '->request() uses the previous request for #');
        $client->request('GET', '#');
        $this->assertEquals('http://www.example.com/foo#', $client->getRequest()->getUri(), '->request() uses the previous request for #');
        $client->request('GET', '#foo');
        $this->assertEquals('http://www.example.com/foo#foo', $client->getRequest()->getUri(), '->request() uses the previous request for #');

        $client = new TestClient();
        $client->request('GET', 'http://www.example.com/foo/');
        $client->request('GET', 'bar');
        $this->assertEquals('http://www.example.com/foo/bar', $client->getRequest()->getUri(), '->request() uses the previous request for relative URLs');

        $client = new TestClient();
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $client->request('GET', 'bar');
        $this->assertEquals('http://www.example.com/foo/bar', $client->getRequest()->getUri(), '->request() uses the previous request for relative URLs');
    }

    public function testRequestURIConversionByServerHost()
    {
        $client = new TestClient();

        $server = array('HTTP_HOST' => 'www.exampl+e.com:8000');
        $parameters = array();
        $files = array();

        $client->request('GET', 'http://exampl+e.com', $parameters, $files, $server);
        $this->assertEquals('http://www.exampl+e.com:8000', $client->getRequest()->getUri(), '->request() uses HTTP_HOST to add port');

        $client->request('GET', 'http://exampl+e.com:8888', $parameters, $files, $server);
        $this->assertEquals('http://www.exampl+e.com:8000', $client->getRequest()->getUri(), '->request() uses HTTP_HOST to modify existing port');

        $client->request('GET', 'http://exampl+e.com:8000', $parameters, $files, $server);
        $this->assertEquals('http://www.exampl+e.com:8000', $client->getRequest()->getUri(), '->request() uses HTTP_HOST respects correct set port');
    }

    public function testRequestReferer()
    {
        $client = new TestClient();
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $client->request('GET', 'bar');
        $server = $client->getRequest()->getServer();
        $this->assertEquals('http://www.example.com/foo/foobar', $server['HTTP_REFERER'], '->request() sets the referer');
    }

    public function testRequestHistory()
    {
        $client = new TestClient();
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $client->request('GET', 'bar');

        $this->assertEquals('http://www.example.com/foo/bar', $client->getHistory()->current()->getUri(), '->request() updates the History');
        $this->assertEquals('http://www.example.com/foo/foobar', $client->getHistory()->back()->getUri(), '->request() updates the History');
    }

    public function testRequestCookies()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('<html><a href="/foo">foo</a></html>', 200, array('Set-Cookie' => 'foo=bar')));
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $this->assertEquals(array('foo' => 'bar'), $client->getCookieJar()->allValues('http://www.example.com/foo/foobar'), '->request() updates the CookieJar');

        $client->request('GET', 'bar');
        $this->assertEquals(array('foo' => 'bar'), $client->getCookieJar()->allValues('http://www.example.com/foo/foobar'), '->request() updates the CookieJar');
    }

    public function testRequestSecureCookies()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('<html><a href="/foo">foo</a></html>', 200, array('Set-Cookie' => 'foo=bar; path=/; secure')));
        $client->request('GET', 'https://www.example.com/foo/foobar');

        $this->assertTrue($client->getCookieJar()->get('foo', '/', 'www.example.com')->isSecure());
    }

    public function testClick()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('<html><a href="/foo">foo</a></html>'));
        $crawler = $client->request('GET', 'http://www.example.com/foo/foobar');

        $client->click($crawler->filter('a')->link());

        $this->assertEquals('http://www.example.com/foo', $client->getRequest()->getUri(), '->click() clicks on links');
    }

    public function testClickForm()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('<html><form action="/foo"><input type="submit" /></form></html>'));
        $crawler = $client->request('GET', 'http://www.example.com/foo/foobar');

        $client->click($crawler->filter('input')->form());

        $this->assertEquals('http://www.example.com/foo', $client->getRequest()->getUri(), '->click() Form submit forms');
    }

    public function testSubmit()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('<html><form action="/foo"><input type="submit" /></form></html>'));
        $crawler = $client->request('GET', 'http://www.example.com/foo/foobar');

        $client->submit($crawler->filter('input')->form());

        $this->assertEquals('http://www.example.com/foo', $client->getRequest()->getUri(), '->submit() submit forms');
    }

    public function testSubmitPreserveAuth()
    {
        $client = new TestClient(array('PHP_AUTH_USER' => 'foo', 'PHP_AUTH_PW' => 'bar'));
        $client->setNextResponse(new Response('<html><form action="/foo"><input type="submit" /></form></html>'));
        $crawler = $client->request('GET', 'http://www.example.com/foo/foobar');

        $server = $client->getRequest()->getServer();
        $this->assertArrayHasKey('PHP_AUTH_USER', $server);
        $this->assertEquals('foo', $server['PHP_AUTH_USER']);
        $this->assertArrayHasKey('PHP_AUTH_PW', $server);
        $this->assertEquals('bar', $server['PHP_AUTH_PW']);

        $client->submit($crawler->filter('input')->form());

        $this->assertEquals('http://www.example.com/foo', $client->getRequest()->getUri(), '->submit() submit forms');

        $server = $client->getRequest()->getServer();
        $this->assertArrayHasKey('PHP_AUTH_USER', $server);
        $this->assertEquals('foo', $server['PHP_AUTH_USER']);
        $this->assertArrayHasKey('PHP_AUTH_PW', $server);
        $this->assertEquals('bar', $server['PHP_AUTH_PW']);
    }

    public function testFollowRedirect()
    {
        $client = new TestClient();
        $client->followRedirects(false);
        $client->request('GET', 'http://www.example.com/foo/foobar');

        try {
            $client->followRedirect();
            $this->fail('->followRedirect() throws a \LogicException if the request was not redirected');
        } catch (\Exception $e) {
            $this->assertInstanceof('LogicException', $e, '->followRedirect() throws a \LogicException if the request was not redirected');
        }

        $client->setNextResponse(new Response('', 302, array('Location' => 'http://www.example.com/redirected')));
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $client->followRedirect();

        $this->assertEquals('http://www.example.com/redirected', $client->getRequest()->getUri(), '->followRedirect() follows a redirect if any');

        $client = new TestClient();
        $client->setNextResponse(new Response('', 302, array('Location' => 'http://www.example.com/redirected')));
        $client->request('GET', 'http://www.example.com/foo/foobar');

        $this->assertEquals('http://www.example.com/redirected', $client->getRequest()->getUri(), '->followRedirect() automatically follows redirects if followRedirects is true');

        $client = new TestClient();
        $client->setNextResponse(new Response('', 201, array('Location' => 'http://www.example.com/redirected')));
        $client->request('GET', 'http://www.example.com/foo/foobar');

        $this->assertEquals('http://www.example.com/foo/foobar', $client->getRequest()->getUri(), '->followRedirect() does not follow redirect if HTTP Code is not 30x');

        $client = new TestClient();
        $client->setNextResponse(new Response('', 201, array('Location' => 'http://www.example.com/redirected')));
        $client->followRedirects(false);
        $client->request('GET', 'http://www.example.com/foo/foobar');

        try {
            $client->followRedirect();
            $this->fail('->followRedirect() throws a \LogicException if the request did not respond with 30x HTTP Code');
        } catch (\Exception $e) {
            $this->assertInstanceof('LogicException', $e, '->followRedirect() throws a \LogicException if the request did not respond with 30x HTTP Code');
        }
    }

    public function testFollowRelativeRedirect()
    {
        $client = new TestClient();
        $client->setNextResponse(new Response('', 302, array('Location' => '/redirected')));
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $this->assertEquals('http://www.example.com/redirected', $client->getRequest()->getUri(), '->followRedirect() follows a redirect if any');

        $client = new TestClient();
        $client->setNextResponse(new Response('', 302, array('Location' => '/redirected:1234')));
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $this->assertEquals('http://www.example.com/redirected:1234', $client->getRequest()->getUri(), '->followRedirect() follows relative urls');
    }

    public function testFollowRedirectWithMaxRedirects()
    {
        $client = new TestClient();
        $client->setMaxRedirects(1);
        $client->setNextResponse(new Response('', 302, array('Location' => 'http://www.example.com/redirected')));
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $this->assertEquals('http://www.example.com/redirected', $client->getRequest()->getUri(), '->followRedirect() follows a redirect if any');

        $client->setNextResponse(new Response('', 302, array('Location' => 'http://www.example.com/redirected2')));
        try {
            $client->followRedirect();
            $this->fail('->followRedirect() throws a \LogicException if the request was redirected and limit of redirections was reached');
        } catch (\Exception $e) {
            $this->assertInstanceof('LogicException', $e, '->followRedirect() throws a \LogicException if the request was redirected and limit of redirections was reached');
        }

        $client->setNextResponse(new Response('', 302, array('Location' => 'http://www.example.com/redirected')));
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $this->assertEquals('http://www.example.com/redirected', $client->getRequest()->getUri(), '->followRedirect() follows a redirect if any');

        $client->setNextResponse(new Response('', 302, array('Location' => '/redirected')));
        $client->request('GET', 'http://www.example.com/foo/foobar');

        $this->assertEquals('http://www.example.com/redirected', $client->getRequest()->getUri(), '->followRedirect() follows relative URLs');

        $client = new TestClient();
        $client->setNextResponse(new Response('', 302, array('Location' => '//www.example.org/')));
        $client->request('GET', 'https://www.example.com/');

        $this->assertEquals('https://www.example.org/', $client->getRequest()->getUri(), '->followRedirect() follows protocol-relative URLs');

        $client = new TestClient();
        $client->setNextResponse(new Response('', 302, array('Location' => 'http://www.example.com/redirected')));
        $client->request('POST', 'http://www.example.com/foo/foobar', array('name' => 'bar'));

        $this->assertEquals('get', $client->getRequest()->getMethod(), '->followRedirect() uses a get for 302');
        $this->assertEquals(array(), $client->getRequest()->getParameters(), '->followRedirect() does not submit parameters when changing the method');
    }

    public function testFollowRedirectWithCookies()
    {
        $client = new TestClient();
        $client->followRedirects(false);
        $client->setNextResponse(new Response('', 302, array(
            'Location'   => 'http://www.example.com/redirected',
            'Set-Cookie' => 'foo=bar',
        )));
        $client->request('GET', 'http://www.example.com/');
        $this->assertEquals(array(), $client->getRequest()->getCookies());
        $client->followRedirect();
        $this->assertEquals(array('foo' => 'bar'), $client->getRequest()->getCookies());
    }

    public function testFollowRedirectWithHeaders()
    {
        $headers = array(
            'HTTP_HOST'       => 'www.example.com',
            'HTTP_USER_AGENT' => 'Symfony2 BrowserKit',
            'CONTENT_TYPE'    => 'application/vnd.custom+xml',
            'HTTPS'           => false,
        );

        $client = new TestClient();
        $client->followRedirects(false);
        $client->setNextResponse(new Response('', 302, array(
            'Location'    => 'http://www.example.com/redirected',
        )));
        $client->request('GET', 'http://www.example.com/', array(), array(), array(
            'CONTENT_TYPE' => 'application/vnd.custom+xml',
        ));

        $this->assertEquals($headers, $client->getRequest()->getServer());

        $client->followRedirect();

        $headers['HTTP_REFERER'] = 'http://www.example.com/';

        $this->assertEquals($headers, $client->getRequest()->getServer());
    }

    public function testFollowRedirectWithPort()
    {
        $headers = array(
            'HTTP_HOST'       => 'www.example.com:8080',
            'HTTP_USER_AGENT' => 'Symfony2 BrowserKit',
            'HTTPS'           => false,
            'HTTP_REFERER'    => 'http://www.example.com:8080/',
        );

        $client = new TestClient();
        $client->setNextResponse(new Response('', 302, array(
            'Location'    => 'http://www.example.com:8080/redirected',
        )));
        $client->request('GET', 'http://www.example.com:8080/');

        $this->assertEquals($headers, $client->getRequest()->getServer());
    }

    public function testBack()
    {
        $client = new TestClient();

        $parameters = array('foo' => 'bar');
        $files = array('myfile.foo' => 'baz');
        $server = array('X_TEST_FOO' => 'bazbar');
        $content = 'foobarbaz';

        $client->request('GET', 'http://www.example.com/foo/foobar', $parameters, $files, $server, $content);
        $client->request('GET', 'http://www.example.com/foo');
        $client->back();

        $this->assertEquals('http://www.example.com/foo/foobar', $client->getRequest()->getUri(), '->back() goes back in the history');
        $this->assertArrayHasKey('foo', $client->getRequest()->getParameters(), '->back() keeps parameters');
        $this->assertArrayHasKey('myfile.foo', $client->getRequest()->getFiles(), '->back() keeps files');
        $this->assertArrayHasKey('X_TEST_FOO', $client->getRequest()->getServer(), '->back() keeps $_SERVER');
        $this->assertEquals($content, $client->getRequest()->getContent(), '->back() keeps content');
    }

    public function testForward()
    {
        $client = new TestClient();

        $parameters = array('foo' => 'bar');
        $files = array('myfile.foo' => 'baz');
        $server = array('X_TEST_FOO' => 'bazbar');
        $content = 'foobarbaz';

        $client->request('GET', 'http://www.example.com/foo/foobar');
        $client->request('GET', 'http://www.example.com/foo', $parameters, $files, $server, $content);
        $client->back();
        $client->forward();

        $this->assertEquals('http://www.example.com/foo', $client->getRequest()->getUri(), '->forward() goes forward in the history');
        $this->assertArrayHasKey('foo', $client->getRequest()->getParameters(), '->forward() keeps parameters');
        $this->assertArrayHasKey('myfile.foo', $client->getRequest()->getFiles(), '->forward() keeps files');
        $this->assertArrayHasKey('X_TEST_FOO', $client->getRequest()->getServer(), '->forward() keeps $_SERVER');
        $this->assertEquals($content, $client->getRequest()->getContent(), '->forward() keeps content');
    }

    public function testReload()
    {
        $client = new TestClient();

        $parameters = array('foo' => 'bar');
        $files = array('myfile.foo' => 'baz');
        $server = array('X_TEST_FOO' => 'bazbar');
        $content = 'foobarbaz';

        $client->request('GET', 'http://www.example.com/foo/foobar', $parameters, $files, $server, $content);
        $client->reload();

        $this->assertEquals('http://www.example.com/foo/foobar', $client->getRequest()->getUri(), '->reload() reloads the current page');
        $this->assertArrayHasKey('foo', $client->getRequest()->getParameters(), '->reload() keeps parameters');
        $this->assertArrayHasKey('myfile.foo', $client->getRequest()->getFiles(), '->reload() keeps files');
        $this->assertArrayHasKey('X_TEST_FOO', $client->getRequest()->getServer(), '->reload() keeps $_SERVER');
        $this->assertEquals($content, $client->getRequest()->getContent(), '->reload() keeps content');
    }

    public function testRestart()
    {
        $client = new TestClient();
        $client->request('GET', 'http://www.example.com/foo/foobar');
        $client->restart();

        $this->assertTrue($client->getHistory()->isEmpty(), '->restart() clears the history');
        $this->assertEquals(array(), $client->getCookieJar()->all(), '->restart() clears the cookies');
    }

    public function testInsulatedRequests()
    {
        $client = new TestClient();
        $client->insulate();
        $client->setNextScript("new Symfony\Component\BrowserKit\Response('foobar')");
        $client->request('GET', 'http://www.example.com/foo/foobar');

        $this->assertEquals('foobar', $client->getResponse()->getContent(), '->insulate() process the request in a forked process');

        $client->setNextScript("new Symfony\Component\BrowserKit\Response('foobar)");

        try {
            $client->request('GET', 'http://www.example.com/foo/foobar');
            $this->fail('->request() throws a \RuntimeException if the script has an error');
        } catch (\Exception $e) {
            $this->assertInstanceof('RuntimeException', $e, '->request() throws a \RuntimeException if the script has an error');
        }
    }

    public function testGetServerParameter()
    {
        $client = new TestClient();
        $this->assertEquals('localhost', $client->getServerParameter('HTTP_HOST'));
        $this->assertEquals('Symfony2 BrowserKit', $client->getServerParameter('HTTP_USER_AGENT'));
        $this->assertEquals('testvalue', $client->getServerParameter('testkey', 'testvalue'));
    }

    public function testSetServerParameter()
    {
        $client = new TestClient();

        $this->assertEquals('localhost', $client->getServerParameter('HTTP_HOST'));
        $this->assertEquals('Symfony2 BrowserKit', $client->getServerParameter('HTTP_USER_AGENT'));

        $client->setServerParameter('HTTP_HOST', 'testhost');
        $this->assertEquals('testhost', $client->getServerParameter('HTTP_HOST'));

        $client->setServerParameter('HTTP_USER_AGENT', 'testua');
        $this->assertEquals('testua', $client->getServerParameter('HTTP_USER_AGENT'));
    }

    public function testSetServerParameterInRequest()
    {
        $client = new TestClient();

        $this->assertEquals('localhost', $client->getServerParameter('HTTP_HOST'));
        $this->assertEquals('Symfony2 BrowserKit', $client->getServerParameter('HTTP_USER_AGENT'));

        $client->request('GET', 'https://www.example.com/https/www.example.com', array(), array(), array(
            'HTTP_HOST'       => 'testhost',
            'HTTP_USER_AGENT' => 'testua',
            'HTTPS'           => false,
            'NEW_SERVER_KEY'  => 'new-server-key-value',
        ));

        $this->assertEquals('localhost', $client->getServerParameter('HTTP_HOST'));
        $this->assertEquals('Symfony2 BrowserKit', $client->getServerParameter('HTTP_USER_AGENT'));

        $this->assertEquals('http://testhost/https/www.example.com', $client->getRequest()->getUri());

        $server = $client->getRequest()->getServer();

        $this->assertArrayHasKey('HTTP_USER_AGENT', $server);
        $this->assertEquals('testua', $server['HTTP_USER_AGENT']);

        $this->assertArrayHasKey('HTTP_HOST', $server);
        $this->assertEquals('testhost', $server['HTTP_HOST']);

        $this->assertArrayHasKey('NEW_SERVER_KEY', $server);
        $this->assertEquals('new-server-key-value', $server['NEW_SERVER_KEY']);

        $this->assertArrayHasKey('HTTPS', $server);
        $this->assertFalse($server['HTTPS']);
    }
}
