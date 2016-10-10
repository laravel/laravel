<?php
// $Id: http_test.php 1782 2008-04-25 17:09:06Z pp11 $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../encoding.php');
require_once(dirname(__FILE__) . '/../http.php');
require_once(dirname(__FILE__) . '/../socket.php');
require_once(dirname(__FILE__) . '/../cookies.php');
Mock::generate('SimpleSocket');
Mock::generate('SimpleCookieJar');
Mock::generate('SimpleRoute');
Mock::generatePartial(
		'SimpleRoute',
		'PartialSimpleRoute',
        array('createSocket'));
Mock::generatePartial(
        'SimpleProxyRoute',
        'PartialSimpleProxyRoute',
        array('createSocket'));

class TestOfDirectRoute extends UnitTestCase {
    
    function testDefaultGetRequest() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("GET /here.html HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: a.valid.host\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        $route = new PartialSimpleRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(new SimpleUrl('http://a.valid.host/here.html'));
        $this->assertSame($route->createConnection('GET', 15), $socket);
    }
    
    function testDefaultPostRequest() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("POST /here.html HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: a.valid.host\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        
        $route = new PartialSimpleRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(new SimpleUrl('http://a.valid.host/here.html'));
        
        $route->createConnection('POST', 15);
    }
    
    function testGetWithPort() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("GET /here.html HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: a.valid.host:81\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        
        $route = new PartialSimpleRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(new SimpleUrl('http://a.valid.host:81/here.html'));
        
        $route->createConnection('GET', 15);
    }
    
    function testGetWithParameters() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("GET /here.html?a=1&b=2 HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: a.valid.host\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        
        $route = new PartialSimpleRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(new SimpleUrl('http://a.valid.host/here.html?a=1&b=2'));
        
        $route->createConnection('GET', 15);
    }
}

class TestOfProxyRoute extends UnitTestCase {
    
    function testDefaultGet() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("GET http://a.valid.host/here.html HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: my-proxy:8080\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        
        $route = new PartialSimpleProxyRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(
                new SimpleUrl('http://a.valid.host/here.html'),
                new SimpleUrl('http://my-proxy'));
        $route->createConnection('GET', 15);
    }
    
    function testDefaultPost() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("POST http://a.valid.host/here.html HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: my-proxy:8080\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        
        $route = new PartialSimpleProxyRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(
                new SimpleUrl('http://a.valid.host/here.html'),
                new SimpleUrl('http://my-proxy'));
        $route->createConnection('POST', 15);
    }
    
    function testGetWithPort() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("GET http://a.valid.host:81/here.html HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: my-proxy:8081\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        
        $route = new PartialSimpleProxyRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(
                new SimpleUrl('http://a.valid.host:81/here.html'),
                new SimpleUrl('http://my-proxy:8081'));
        $route->createConnection('GET', 15);
    }
    
    function testGetWithParameters() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("GET http://a.valid.host/here.html?a=1&b=2 HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: my-proxy:8080\r\n"));
        $socket->expectAt(2, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 3);
        
        $route = new PartialSimpleProxyRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(
                new SimpleUrl('http://a.valid.host/here.html?a=1&b=2'),
                new SimpleUrl('http://my-proxy'));
        $route->createConnection('GET', 15);
    }
    
    function testGetWithAuthentication() {
        $encoded = base64_encode('Me:Secret');

        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("GET http://a.valid.host/here.html HTTP/1.0\r\n"));
        $socket->expectAt(1, 'write', array("Host: my-proxy:8080\r\n"));
        $socket->expectAt(2, 'write', array("Proxy-Authorization: Basic $encoded\r\n"));
        $socket->expectAt(3, 'write', array("Connection: close\r\n"));
        $socket->expectCallCount('write', 4);
        
        $route = new PartialSimpleProxyRoute();
        $route->setReturnReference('createSocket', $socket);
        $route->__construct(
                new SimpleUrl('http://a.valid.host/here.html'),
                new SimpleUrl('http://my-proxy'),
                'Me',
                'Secret');
        $route->createConnection('GET', 15);
    }
}

class TestOfHttpRequest extends UnitTestCase {
    
    function testReadingBadConnection() {
        $socket = new MockSimpleSocket();
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        $request = new SimpleHttpRequest($route, new SimpleGetEncoding());
        $reponse = $request->fetch(15);
        $this->assertTrue($reponse->isError());
    }
    
    function testReadingGoodConnection() {
        $socket = new MockSimpleSocket();
        $socket->expectOnce('write', array("\r\n"));
        
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        $route->expect('createConnection', array('GET', 15));
        
        $request = new SimpleHttpRequest($route, new SimpleGetEncoding());
        $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
    }
    
    function testWritingAdditionalHeaders() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("My: stuff\r\n"));
        $socket->expectAt(1, 'write', array("\r\n"));
        $socket->expectCallCount('write', 2);
        
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        
        $request = new SimpleHttpRequest($route, new SimpleGetEncoding());
        $request->addHeaderLine('My: stuff');
        $request->fetch(15);
    }
    
    function testCookieWriting() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("Cookie: a=A\r\n"));
        $socket->expectAt(1, 'write', array("\r\n"));
        $socket->expectCallCount('write', 2);
        
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A');
        
        $request = new SimpleHttpRequest($route, new SimpleGetEncoding());
        $request->readCookiesFromJar($jar, new SimpleUrl('/'));
        $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
    }
    
    function testMultipleCookieWriting() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("Cookie: a=A;b=B\r\n"));
        
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A');
        $jar->setCookie('b', 'B');
        
        $request = new SimpleHttpRequest($route, new SimpleGetEncoding());
        $request->readCookiesFromJar($jar, new SimpleUrl('/'));
        $request->fetch(15);
    }
}

class TestOfHttpPostRequest extends UnitTestCase {
    
    function testReadingBadConnectionCausesErrorBecauseOfDeadSocket() {
        $socket = new MockSimpleSocket();
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        $request = new SimpleHttpRequest($route, new SimplePostEncoding());
        $reponse = $request->fetch(15);
        $this->assertTrue($reponse->isError());
    }
    
    function testReadingGoodConnection() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("Content-Length: 0\r\n"));
        $socket->expectAt(1, 'write', array("Content-Type: application/x-www-form-urlencoded\r\n"));
        $socket->expectAt(2, 'write', array("\r\n"));
        $socket->expectAt(3, 'write', array(""));
        
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        $route->expect('createConnection', array('POST', 15));
        
        $request = new SimpleHttpRequest($route, new SimplePostEncoding());
        $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
    }
    
    function testContentHeadersCalculated() {
        $socket = new MockSimpleSocket();
        $socket->expectAt(0, 'write', array("Content-Length: 3\r\n"));
        $socket->expectAt(1, 'write', array("Content-Type: application/x-www-form-urlencoded\r\n"));
        $socket->expectAt(2, 'write', array("\r\n"));
        $socket->expectAt(3, 'write', array("a=A"));
        
        $route = new MockSimpleRoute();
        $route->setReturnReference('createConnection', $socket);
        $route->expect('createConnection', array('POST', 15));
        
        $request = new SimpleHttpRequest(
                $route,
                new SimplePostEncoding(array('a' => 'A')));
        $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
    }
}
    
class TestOfHttpHeaders extends UnitTestCase {
    
    function testParseBasicHeaders() {
        $headers = new SimpleHttpHeaders(
                "HTTP/1.1 200 OK\r\n" .
                "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n" .
                "Content-Type: text/plain\r\n" .
                "Server: Apache/1.3.24 (Win32) PHP/4.2.3\r\n" .
                "Connection: close");
        $this->assertIdentical($headers->getHttpVersion(), "1.1");
        $this->assertIdentical($headers->getResponseCode(), 200);
        $this->assertEqual($headers->getMimeType(), "text/plain");
    }
    
    function testNonStandardResponseHeader() {
        $headers = new SimpleHttpHeaders(
                "HTTP/1.1 302 (HTTP-Version SP Status-Code CRLF)\r\n" .
                "Connection: close");
        $this->assertIdentical($headers->getResponseCode(), 302);
    }
    
    function testCanParseMultipleCookies() {
        $jar = new MockSimpleCookieJar();
        $jar->expectAt(0, 'setCookie', array('a', 'aaa', 'host', '/here/', 'Wed, 25 Dec 2002 04:24:20 GMT'));
        $jar->expectAt(1, 'setCookie', array('b', 'bbb', 'host', '/', false));

        $headers = new SimpleHttpHeaders(
                "HTTP/1.1 200 OK\r\n" .
                "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n" .
                "Content-Type: text/plain\r\n" .
                "Server: Apache/1.3.24 (Win32) PHP/4.2.3\r\n" .
                "Set-Cookie: a=aaa; expires=Wed, 25-Dec-02 04:24:20 GMT; path=/here/\r\n" .
                "Set-Cookie: b=bbb\r\n" .
                "Connection: close");
        $headers->writeCookiesToJar($jar, new SimpleUrl('http://host'));
    }
    
    function testCanRecogniseRedirect() {
        $headers = new SimpleHttpHeaders("HTTP/1.1 301 OK\r\n" .
                "Content-Type: text/plain\r\n" .
                "Content-Length: 0\r\n" .
                "Location: http://www.somewhere-else.com/\r\n" .
                "Connection: close");
        $this->assertIdentical($headers->getResponseCode(), 301);
        $this->assertEqual($headers->getLocation(), "http://www.somewhere-else.com/");
        $this->assertTrue($headers->isRedirect());
    }
    
    function testCanParseChallenge() {
        $headers = new SimpleHttpHeaders("HTTP/1.1 401 Authorization required\r\n" .
                "Content-Type: text/plain\r\n" .
                "Connection: close\r\n" .
                "WWW-Authenticate: Basic realm=\"Somewhere\"");
        $this->assertEqual($headers->getAuthentication(), 'Basic');
        $this->assertEqual($headers->getRealm(), 'Somewhere');
        $this->assertTrue($headers->isChallenge());
    }
}

class TestOfHttpResponse extends UnitTestCase {
    
    function testBadRequest() {
        $socket = new MockSimpleSocket();
        $socket->setReturnValue('getSent', '');

        $response = new SimpleHttpResponse($socket, new SimpleUrl('here'), new SimpleGetEncoding());
        $this->assertTrue($response->isError());
        $this->assertPattern('/Nothing fetched/', $response->getError());
        $this->assertIdentical($response->getContent(), false);
        $this->assertIdentical($response->getSent(), '');
    }
    
    function testBadSocketDuringResponse() {
        $socket = new MockSimpleSocket();
        $socket->setReturnValueAt(0, "read", "HTTP/1.1 200 OK\r\n");
        $socket->setReturnValueAt(1, "read", "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n");
        $socket->setReturnValue("read", "");
        $socket->setReturnValue('getSent', 'HTTP/1.1 ...');

        $response = new SimpleHttpResponse($socket, new SimpleUrl('here'), new SimpleGetEncoding());
        $this->assertTrue($response->isError());
        $this->assertEqual($response->getContent(), '');
        $this->assertEqual($response->getSent(), 'HTTP/1.1 ...');
    }
    
    function testIncompleteHeader() {
        $socket = new MockSimpleSocket();
        $socket->setReturnValueAt(0, "read", "HTTP/1.1 200 OK\r\n");
        $socket->setReturnValueAt(1, "read", "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n");
        $socket->setReturnValueAt(2, "read", "Content-Type: text/plain\r\n");
        $socket->setReturnValue("read", "");
        
        $response = new SimpleHttpResponse($socket, new SimpleUrl('here'), new SimpleGetEncoding());
        $this->assertTrue($response->isError());
        $this->assertEqual($response->getContent(), "");
    }
    
    function testParseOfResponseHeadersWhenChunked() {
        $socket = new MockSimpleSocket();
        $socket->setReturnValueAt(0, "read", "HTTP/1.1 200 OK\r\nDate: Mon, 18 Nov 2002 15:50:29 GMT\r\n");
        $socket->setReturnValueAt(1, "read", "Content-Type: text/plain\r\n");
        $socket->setReturnValueAt(2, "read", "Server: Apache/1.3.24 (Win32) PHP/4.2.3\r\nConne");
        $socket->setReturnValueAt(3, "read", "ction: close\r\n\r\nthis is a test file\n");
        $socket->setReturnValueAt(4, "read", "with two lines in it\n");
        $socket->setReturnValue("read", "");
        
        $response = new SimpleHttpResponse($socket, new SimpleUrl('here'), new SimpleGetEncoding());
        $this->assertFalse($response->isError());
        $this->assertEqual(
                $response->getContent(),
                "this is a test file\nwith two lines in it\n");
        $headers = $response->getHeaders();
        $this->assertIdentical($headers->getHttpVersion(), "1.1");
        $this->assertIdentical($headers->getResponseCode(), 200);
        $this->assertEqual($headers->getMimeType(), "text/plain");
        $this->assertFalse($headers->isRedirect());
        $this->assertFalse($headers->getLocation());
    }
    
    function testRedirect() {
        $socket = new MockSimpleSocket();
        $socket->setReturnValueAt(0, "read", "HTTP/1.1 301 OK\r\n");
        $socket->setReturnValueAt(1, "read", "Content-Type: text/plain\r\n");
        $socket->setReturnValueAt(2, "read", "Location: http://www.somewhere-else.com/\r\n");
        $socket->setReturnValueAt(3, "read", "Connection: close\r\n");
        $socket->setReturnValueAt(4, "read", "\r\n");
        $socket->setReturnValue("read", "");
        
        $response = new SimpleHttpResponse($socket, new SimpleUrl('here'), new SimpleGetEncoding());
        $headers = $response->getHeaders();
        $this->assertTrue($headers->isRedirect());
        $this->assertEqual($headers->getLocation(), "http://www.somewhere-else.com/");
    }
    
    function testRedirectWithPort() {
        $socket = new MockSimpleSocket();
        $socket->setReturnValueAt(0, "read", "HTTP/1.1 301 OK\r\n");
        $socket->setReturnValueAt(1, "read", "Content-Type: text/plain\r\n");
        $socket->setReturnValueAt(2, "read", "Location: http://www.somewhere-else.com:80/\r\n");
        $socket->setReturnValueAt(3, "read", "Connection: close\r\n");
        $socket->setReturnValueAt(4, "read", "\r\n");
        $socket->setReturnValue("read", "");
        
        $response = new SimpleHttpResponse($socket, new SimpleUrl('here'), new SimpleGetEncoding());
        $headers = $response->getHeaders();
        $this->assertTrue($headers->isRedirect());
        $this->assertEqual($headers->getLocation(), "http://www.somewhere-else.com:80/");
    }
}
?>