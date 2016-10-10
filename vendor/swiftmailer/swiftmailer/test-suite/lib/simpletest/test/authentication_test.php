<?php
// $Id: authentication_test.php 1748 2008-04-14 01:50:41Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../authentication.php');
require_once(dirname(__FILE__) . '/../http.php');
Mock::generate('SimpleHttpRequest');

class TestOfRealm extends UnitTestCase {
    
    function testWithinSameUrl() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/hello.html'));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/hello.html')));
    }
    
    function testInsideWithLongerUrl() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/'));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/hello.html')));
    }
    
    function testBelowRootIsOutside() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/'));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/more/hello.html')));
    }
    
    function testOldNetscapeDefinitionIsOutside() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/'));
        $this->assertFalse($realm->isWithin(
                new SimpleUrl('http://www.here.com/pathmore/hello.html')));
    }
    
    function testInsideWithMissingTrailingSlash() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/'));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path')));
    }
    
    function testDifferentPageNameStillInside() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/hello.html'));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/goodbye.html')));
    }
    
    function testNewUrlInSameDirectoryDoesNotChangeRealm() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/hello.html'));
        $realm->stretch(new SimpleUrl('http://www.here.com/path/goodbye.html'));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/index.html')));
        $this->assertFalse($realm->isWithin(
                new SimpleUrl('http://www.here.com/index.html')));
    }
    
    function testNewUrlMakesRealmTheCommonPath() {
        $realm = new SimpleRealm(
                'Basic',
                new SimpleUrl('http://www.here.com/path/here/hello.html'));
        $realm->stretch(new SimpleUrl('http://www.here.com/path/there/goodbye.html'));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/here/index.html')));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/there/index.html')));
        $this->assertTrue($realm->isWithin(
                new SimpleUrl('http://www.here.com/path/index.html')));
        $this->assertFalse($realm->isWithin(
                new SimpleUrl('http://www.here.com/index.html')));
        $this->assertFalse($realm->isWithin(
                new SimpleUrl('http://www.here.com/paths/index.html')));
        $this->assertFalse($realm->isWithin(
                new SimpleUrl('http://www.here.com/pathindex.html')));
    }
}

class TestOfAuthenticator extends UnitTestCase {
    
    function testNoRealms() {
        $request = new MockSimpleHttpRequest();
        $request->expectNever('addHeaderLine');
        $authenticator = new SimpleAuthenticator();
        $authenticator->addHeaders($request, new SimpleUrl('http://here.com/'));
    }
    
    function &createSingleRealm() {
        $authenticator = new SimpleAuthenticator();
        $authenticator->addRealm(
                new SimpleUrl('http://www.here.com/path/hello.html'),
                'Basic',
                'Sanctuary');
        $authenticator->setIdentityForRealm('www.here.com', 'Sanctuary', 'test', 'secret');
        return $authenticator;
    }
    
    function testOutsideRealm() {
        $request = new MockSimpleHttpRequest();
        $request->expectNever('addHeaderLine');
        $authenticator = &$this->createSingleRealm();
        $authenticator->addHeaders(
                $request,
                new SimpleUrl('http://www.here.com/hello.html'));
    }
    
    function testWithinRealm() {
        $request = new MockSimpleHttpRequest();
        $request->expectOnce('addHeaderLine');
        $authenticator = &$this->createSingleRealm();
        $authenticator->addHeaders(
                $request,
                new SimpleUrl('http://www.here.com/path/more/hello.html'));
    }
    
    function testRestartingClearsRealm() {
        $request = new MockSimpleHttpRequest();
        $request->expectNever('addHeaderLine');
        $authenticator = &$this->createSingleRealm();
        $authenticator->restartSession();
        $authenticator->addHeaders(
                $request,
                new SimpleUrl('http://www.here.com/hello.html'));
    }
    
    function testDifferentHostIsOutsideRealm() {
        $request = new MockSimpleHttpRequest();
        $request->expectNever('addHeaderLine');
        $authenticator = &$this->createSingleRealm();
        $authenticator->addHeaders(
                $request,
                new SimpleUrl('http://here.com/path/hello.html'));
    }
}
?>