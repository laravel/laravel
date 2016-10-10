<?php
// $Id: cookies_test.php 1506 2007-05-07 00:58:03Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../cookies.php');

class TestOfCookie extends UnitTestCase {
    
    function testCookieDefaults() {
        $cookie = new SimpleCookie("name");
        $this->assertFalse($cookie->getValue());
        $this->assertEqual($cookie->getPath(), "/");
        $this->assertIdentical($cookie->getHost(), false);
        $this->assertFalse($cookie->getExpiry());
        $this->assertFalse($cookie->isSecure());
    }
    
    function testCookieAccessors() {
        $cookie = new SimpleCookie(
                "name",
                "value",
                "/path",
                "Mon, 18 Nov 2002 15:50:29 GMT",
                true);
        $this->assertEqual($cookie->getName(), "name");
        $this->assertEqual($cookie->getValue(), "value");
        $this->assertEqual($cookie->getPath(), "/path/");
        $this->assertEqual($cookie->getExpiry(), "Mon, 18 Nov 2002 15:50:29 GMT");
        $this->assertTrue($cookie->isSecure());
    }
    
    function testFullHostname() {
        $cookie = new SimpleCookie("name");
        $this->assertTrue($cookie->setHost("host.name.here"));
        $this->assertEqual($cookie->getHost(), "host.name.here");
        $this->assertTrue($cookie->setHost("host.com"));
        $this->assertEqual($cookie->getHost(), "host.com");
    }
    
    function testHostTruncation() {
        $cookie = new SimpleCookie("name");
        $cookie->setHost("this.host.name.here");
        $this->assertEqual($cookie->getHost(), "host.name.here");
        $cookie->setHost("this.host.com");
        $this->assertEqual($cookie->getHost(), "host.com");
        $this->assertTrue($cookie->setHost("dashes.in-host.com"));
        $this->assertEqual($cookie->getHost(), "in-host.com");
    }
    
    function testBadHosts() {
        $cookie = new SimpleCookie("name");
        $this->assertFalse($cookie->setHost("gibberish"));
        $this->assertFalse($cookie->setHost("host.here"));
        $this->assertFalse($cookie->setHost("host..com"));
        $this->assertFalse($cookie->setHost("..."));
        $this->assertFalse($cookie->setHost("host.com."));
    }
    
    function testHostValidity() {
        $cookie = new SimpleCookie("name");
        $cookie->setHost("this.host.name.here");
        $this->assertTrue($cookie->isValidHost("host.name.here"));
        $this->assertTrue($cookie->isValidHost("that.host.name.here"));
        $this->assertFalse($cookie->isValidHost("bad.host"));
        $this->assertFalse($cookie->isValidHost("nearly.name.here"));
    }
    
    function testPathValidity() {
        $cookie = new SimpleCookie("name", "value", "/path");
        $this->assertFalse($cookie->isValidPath("/"));
        $this->assertTrue($cookie->isValidPath("/path/"));
        $this->assertTrue($cookie->isValidPath("/path/more"));
    }
    
    function testSessionExpiring() {
        $cookie = new SimpleCookie("name", "value", "/path");
        $this->assertTrue($cookie->isExpired(0));
    }
    
    function testTimestampExpiry() {
        $cookie = new SimpleCookie("name", "value", "/path", 456);
        $this->assertFalse($cookie->isExpired(0));
        $this->assertTrue($cookie->isExpired(457));
        $this->assertFalse($cookie->isExpired(455));
    }
    
    function testDateExpiry() {
        $cookie = new SimpleCookie(
                "name",
                "value",
                "/path",
                "Mon, 18 Nov 2002 15:50:29 GMT");
        $this->assertTrue($cookie->isExpired("Mon, 18 Nov 2002 15:50:30 GMT"));
        $this->assertFalse($cookie->isExpired("Mon, 18 Nov 2002 15:50:28 GMT"));
    }
    
    function testAging() {
        $cookie = new SimpleCookie("name", "value", "/path", 200);
        $cookie->agePrematurely(199);
        $this->assertFalse($cookie->isExpired(0));
        $cookie->agePrematurely(2);
        $this->assertTrue($cookie->isExpired(0));
    }
}

class TestOfCookieJar extends UnitTestCase {
    
    function testAddCookie() {
        $jar = new SimpleCookieJar();
        $jar->setCookie("a", "A");
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array('a=A'));
    }
    
    function testHostFilter() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A', 'my-host.com');
        $jar->setCookie('b', 'B', 'another-host.com');
        $jar->setCookie('c', 'C');
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('my-host.com')),
                array('a=A', 'c=C'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('another-host.com')),
                array('b=B', 'c=C'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('www.another-host.com')),
                array('b=B', 'c=C'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('new-host.org')),
                array('c=C'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('/')),
                array('a=A', 'b=B', 'c=C'));
    }
    
    function testPathFilter() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A', false, '/path/');
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array());
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/elsewhere')), array());
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path/')), array('a=A'));
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path')), array('a=A'));
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/pa')), array());
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path/here')), array('a=A'));
    }
    
    function testPathFilterDeeply() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A', false, '/path/more_path/');
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path/')), array());
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path')), array());
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/pa')), array());
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path/more_path/')), array('a=A'));
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path/more_path/and_more')), array('a=A'));
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/path/not_here/')), array());
    }
    
    function testMultipleCookieWithDifferentPathsButSameName() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'abc', false, '/');
        $jar->setCookie('a', '123', false, '/path/here/');
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('/')),
                array('a=abc'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('my-host.com/')),
                array('a=abc'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('my-host.com/path/')),
                array('a=abc'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('my-host.com/path/here')),
                array('a=abc', 'a=123'));
        $this->assertEqual(
                $jar->selectAsPairs(new SimpleUrl('my-host.com/path/here/there')),
                array('a=abc', 'a=123'));
    }
    
    function testOverwrite() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'abc', false, '/');
        $jar->setCookie('a', 'cde', false, '/');
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array('a=cde'));
    }
    
    function testClearSessionCookies() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A', false, '/');
        $jar->restartSession();
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array());
    }
    
    function testExpiryFilterByDate() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A', false, '/', 'Wed, 25-Dec-02 04:24:20 GMT');
        $jar->restartSession("Wed, 25-Dec-02 04:24:19 GMT");
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array('a=A'));
        $jar->restartSession("Wed, 25-Dec-02 04:24:21 GMT");
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array());
    }
    
    function testExpiryFilterByAgeing() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'A', false, '/', 'Wed, 25-Dec-02 04:24:20 GMT');
        $jar->restartSession("Wed, 25-Dec-02 04:24:19 GMT");
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array('a=A'));
        $jar->agePrematurely(2);
        $jar->restartSession("Wed, 25-Dec-02 04:24:19 GMT");
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array());
    }
    
    function testCookieClearing() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'abc', false, '/');
        $jar->setCookie('a', '', false, '/');
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array('a='));
    }
    
    function testCookieClearByLoweringDate() {
        $jar = new SimpleCookieJar();
        $jar->setCookie('a', 'abc', false, '/', 'Wed, 25-Dec-02 04:24:21 GMT');
        $jar->setCookie('a', 'def', false, '/', 'Wed, 25-Dec-02 04:24:19 GMT');
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array('a=def'));
        $jar->restartSession('Wed, 25-Dec-02 04:24:20 GMT');
        $this->assertEqual($jar->selectAsPairs(new SimpleUrl('/')), array());
    }
}
?>