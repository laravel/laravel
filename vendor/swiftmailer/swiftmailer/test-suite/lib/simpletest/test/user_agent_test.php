<?php
// $Id: user_agent_test.php 1788 2008-04-27 11:01:59Z pp11 $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../user_agent.php');
require_once(dirname(__FILE__) . '/../authentication.php');
require_once(dirname(__FILE__) . '/../http.php');
require_once(dirname(__FILE__) . '/../encoding.php');
Mock::generate('SimpleHttpRequest');
Mock::generate('SimpleHttpResponse');
Mock::generate('SimpleHttpHeaders');
Mock::generatePartial('SimpleUserAgent', 'MockRequestUserAgent', array('createHttpRequest'));

class TestOfFetchingUrlParameters extends UnitTestCase {
    
    function setUp() {
        $this->headers = new MockSimpleHttpHeaders();
        $this->response = new MockSimpleHttpResponse();
        $this->response->setReturnValue('isError', false);
        $this->response->returns('getHeaders', new MockSimpleHttpHeaders());
        $this->request = new MockSimpleHttpRequest();
        $this->request->returns('fetch', $this->response);
    }
    
    function testGetRequestWithoutIncidentGivesNoErrors() {
        $url = new SimpleUrl('http://test:secret@this.com/page.html');
        $url->addRequestParameters(array('a' => 'A', 'b' => 'B'));
        
        $agent = new MockRequestUserAgent();
        $agent->returns('createHttpRequest', $this->request);
        $agent->__construct();
        
        $response = $agent->fetchResponse(
                new SimpleUrl('http://test:secret@this.com/page.html'),
                new SimpleGetEncoding(array('a' => 'A', 'b' => 'B')));
        $this->assertFalse($response->isError());
    }
}

class TestOfAdditionalHeaders extends UnitTestCase {
    
    function testAdditionalHeaderAddedToRequest() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnReference('getHeaders', new MockSimpleHttpHeaders());
        
        $request = new MockSimpleHttpRequest();
        $request->setReturnReference('fetch', $response);
        $request->expectOnce(
                'addHeaderLine',
                array('User-Agent: SimpleTest'));
        
        $agent = new MockRequestUserAgent();
        $agent->setReturnReference('createHttpRequest', $request);
        $agent->__construct();
        $agent->addHeader('User-Agent: SimpleTest');
        $response = $agent->fetchResponse(new SimpleUrl('http://this.host/'), new SimpleGetEncoding());
    }
}

class TestOfBrowserCookies extends UnitTestCase {

    private function createStandardResponse() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue("isError", false);
        $response->setReturnValue("getContent", "stuff");
        $response->setReturnReference("getHeaders", new MockSimpleHttpHeaders());
        return $response;
    }
    
    private function createCookieSite($header_lines) {
        $headers = new SimpleHttpHeaders($header_lines);
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue("isError", false);
        $response->setReturnReference("getHeaders", $headers);
        $response->setReturnValue("getContent", "stuff");
        $request = new MockSimpleHttpRequest();
        $request->setReturnReference("fetch", $response);
        return $request;
    }
    
    private function createMockedRequestUserAgent(&$request) {
        $agent = new MockRequestUserAgent();
        $agent->setReturnReference('createHttpRequest', $request);
        $agent->__construct();
        return $agent;
    }
    
    function testCookieJarIsSentToRequest() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A');
        
        $request = new MockSimpleHttpRequest();
        $request->returns('fetch', $this->createStandardResponse());
        $request->expectOnce('readCookiesFromJar', array($jar, '*'));
        
        $agent = $this->createMockedRequestUserAgent($request);
        $agent->setCookie('a', 'A');
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
    }
      
    function testNoCookieJarIsSentToRequestWhenCookiesAreDisabled() {
        $request = new MockSimpleHttpRequest();
        $request->returns('fetch', $this->createStandardResponse());
        $request->expectNever('readCookiesFromJar');
        
        $agent = $this->createMockedRequestUserAgent($request);
        $agent->setCookie('a', 'A');
        $agent->ignoreCookies();
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
    }
  
    function testReadingNewCookie() {
        $request = $this->createCookieSite('Set-cookie: a=AAAA');
        $agent = $this->createMockedRequestUserAgent($request);
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
        $this->assertEqual($agent->getCookieValue("this.com", "this/path/", "a"), "AAAA");
    }
  
    function testIgnoringNewCookieWhenCookiesDisabled() {
        $request = $this->createCookieSite('Set-cookie: a=AAAA');
        $agent = $this->createMockedRequestUserAgent($request);
        $agent->ignoreCookies();
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
        $this->assertIdentical($agent->getCookieValue("this.com", "this/path/", "a"), false);
    }
   
    function testOverwriteCookieThatAlreadyExists() {
        $request = $this->createCookieSite('Set-cookie: a=AAAA');
        $agent = $this->createMockedRequestUserAgent($request);
        $agent->setCookie('a', 'A');
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
        $this->assertEqual($agent->getCookieValue("this.com", "this/path/", "a"), "AAAA");
    }
   
    function testClearCookieBySettingExpiry() {
        $request = $this->createCookieSite('Set-cookie: a=b');
        $agent = $this->createMockedRequestUserAgent($request);
        
        $agent->setCookie("a", "A", "this/path/", "Wed, 25-Dec-02 04:24:21 GMT");
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
        $this->assertIdentical(
                $agent->getCookieValue("this.com", "this/path/", "a"),
                "b");
        $agent->restart("Wed, 25-Dec-02 04:24:20 GMT");
        $this->assertIdentical(
                $agent->getCookieValue("this.com", "this/path/", "a"),
                false);
    }
    
    function testAgeingAndClearing() {
        $request = $this->createCookieSite('Set-cookie: a=A; expires=Wed, 25-Dec-02 04:24:21 GMT; path=/this/path');
        $agent = $this->createMockedRequestUserAgent($request);
        
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
        $agent->restart("Wed, 25-Dec-02 04:24:20 GMT");
        $this->assertIdentical(
                $agent->getCookieValue("this.com", "this/path/", "a"),
                "A");
        $agent->ageCookies(2);
        $agent->restart("Wed, 25-Dec-02 04:24:20 GMT");
        $this->assertIdentical(
                $agent->getCookieValue("this.com", "this/path/", "a"),
                false);
    }
    
    function testReadingIncomingAndSettingNewCookies() {
        $request = $this->createCookieSite('Set-cookie: a=AAA');
        $agent = $this->createMockedRequestUserAgent($request);
        
        $this->assertNull($agent->getBaseCookieValue("a", false));
        $agent->fetchResponse(
                new SimpleUrl('http://this.com/this/path/page.html'),
                new SimpleGetEncoding());
        $agent->setCookie("b", "BBB", "this.com", "this/path/");
        $this->assertEqual(
                $agent->getBaseCookieValue("a", new SimpleUrl('http://this.com/this/path/page.html')),
                "AAA");
        $this->assertEqual(
                $agent->getBaseCookieValue("b", new SimpleUrl('http://this.com/this/path/page.html')),
                "BBB");
    }
}

class TestOfHttpRedirects extends UnitTestCase {
    
    function createRedirect($content, $redirect) {
        $headers = new MockSimpleHttpHeaders();
        $headers->setReturnValue('isRedirect', (boolean)$redirect);
        $headers->setReturnValue('getLocation', $redirect);
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent', $content);
        $response->setReturnReference('getHeaders', $headers);
        $request = new MockSimpleHttpRequest();
        $request->setReturnReference('fetch', $response);
        return $request;
    }
    
    function testDisabledRedirects() {
        $agent = new MockRequestUserAgent();
        $agent->returns(
                'createHttpRequest',
                $this->createRedirect('stuff', 'there.html'));
        $agent->expectOnce('createHttpRequest');
        $agent->__construct();
        $agent->setMaximumRedirects(0);
        $response = $agent->fetchResponse(new SimpleUrl('here.html'), new SimpleGetEncoding());
        $this->assertEqual($response->getContent(), 'stuff');
    }
    
    function testSingleRedirect() {
        $agent = new MockRequestUserAgent();
        $agent->returnsAt(
                0,
                'createHttpRequest',
                $this->createRedirect('first', 'two.html'));
        $agent->returnsAt(
                1,
                'createHttpRequest',
                $this->createRedirect('second', 'three.html'));
        $agent->expectCallCount('createHttpRequest', 2);
        $agent->__construct();
        
        $agent->setMaximumRedirects(1);
        $response = $agent->fetchResponse(new SimpleUrl('one.html'), new SimpleGetEncoding());
        $this->assertEqual($response->getContent(), 'second');
    }
    
    function testDoubleRedirect() {
        $agent = new MockRequestUserAgent();
        $agent->returnsAt(
                0,
                'createHttpRequest',
                $this->createRedirect('first', 'two.html'));
        $agent->returnsAt(
                1,
                'createHttpRequest',
                $this->createRedirect('second', 'three.html'));
        $agent->returnsAt(
                2,
                'createHttpRequest',
                $this->createRedirect('third', 'four.html'));
        $agent->expectCallCount('createHttpRequest', 3);
        $agent->__construct();
        
        $agent->setMaximumRedirects(2);
        $response = $agent->fetchResponse(new SimpleUrl('one.html'), new SimpleGetEncoding());
        $this->assertEqual($response->getContent(), 'third');
    }
    
    function testSuccessAfterRedirect() {
        $agent = new MockRequestUserAgent();
        $agent->returnsAt(
                0,
                'createHttpRequest',
                $this->createRedirect('first', 'two.html'));
        $agent->returnsAt(
                1,
                'createHttpRequest',
                $this->createRedirect('second', false));
        $agent->returnsAt(
                2,
                'createHttpRequest',
                $this->createRedirect('third', 'four.html'));
        $agent->expectCallCount('createHttpRequest', 2);
        $agent->__construct();
        
        $agent->setMaximumRedirects(2);
        $response = $agent->fetchResponse(new SimpleUrl('one.html'), new SimpleGetEncoding());
        $this->assertEqual($response->getContent(), 'second');
    }
    
    function testRedirectChangesPostToGet() {
        $agent = new MockRequestUserAgent();
        $agent->returnsAt(
                0,
                'createHttpRequest',
                $this->createRedirect('first', 'two.html'));
        $agent->expectAt(0, 'createHttpRequest', array('*', new IsAExpectation('SimplePostEncoding')));
        $agent->returnsAt(
                1,
                'createHttpRequest',
                $this->createRedirect('second', 'three.html'));
        $agent->expectAt(1, 'createHttpRequest', array('*', new IsAExpectation('SimpleGetEncoding')));
        $agent->expectCallCount('createHttpRequest', 2);
        $agent->__construct();
        $agent->setMaximumRedirects(1);
        $response = $agent->fetchResponse(new SimpleUrl('one.html'), new SimplePostEncoding());
    }
}

class TestOfBadHosts extends UnitTestCase {
    
    private function createSimulatedBadHost() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('isError', true);
        $response->setReturnValue('getError', 'Bad socket');
        $response->setReturnValue('getContent', false);
        $request = new MockSimpleHttpRequest();
        $request->setReturnReference('fetch', $response);
        return $request;
    }
    
    function testUntestedHost() {
        $request = $this->createSimulatedBadHost();
        $agent = new MockRequestUserAgent();
        $agent->setReturnReference('createHttpRequest', $request);
        $agent->__construct();
        $response = $agent->fetchResponse(
                new SimpleUrl('http://this.host/this/path/page.html'),
                new SimpleGetEncoding());
        $this->assertTrue($response->isError());
    }
}

class TestOfAuthorisation extends UnitTestCase {
    
    function testAuthenticateHeaderAdded() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnReference('getHeaders', new MockSimpleHttpHeaders());
        
        $request = new MockSimpleHttpRequest();
        $request->returns('fetch', $response);
        $request->expectOnce(
                'addHeaderLine',
                array('Authorization: Basic ' . base64_encode('test:secret')));
        
        $agent = new MockRequestUserAgent();
        $agent->returns('createHttpRequest', $request);
        $agent->__construct();
        $response = $agent->fetchResponse(
                new SimpleUrl('http://test:secret@this.host'),
                new SimpleGetEncoding());
    }
}
?>