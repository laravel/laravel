<?php
// $Id: url_test.php 1780 2008-04-21 18:57:46Z edwardzyang $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../url.php');

class TestOfUrl extends UnitTestCase {
    
    function testDefaultUrl() {
        $url = new SimpleUrl('');
        $this->assertEqual($url->getScheme(), '');
        $this->assertEqual($url->getHost(), '');
        $this->assertEqual($url->getScheme('http'), 'http');
        $this->assertEqual($url->getHost('localhost'), 'localhost');
        $this->assertEqual($url->getPath(), '');
    }
    
    function testBasicParsing() {
        $url = new SimpleUrl('https://www.lastcraft.com/test/');
        $this->assertEqual($url->getScheme(), 'https');
        $this->assertEqual($url->getHost(), 'www.lastcraft.com');
        $this->assertEqual($url->getPath(), '/test/');
    }
    
    function testRelativeUrls() {
        $url = new SimpleUrl('../somewhere.php');
        $this->assertEqual($url->getScheme(), false);
        $this->assertEqual($url->getHost(), false);
        $this->assertEqual($url->getPath(), '../somewhere.php');
    }
    
    function testParseBareParameter() {
        $url = new SimpleUrl('?a');
        $this->assertEqual($url->getPath(), '');
        $this->assertEqual($url->getEncodedRequest(), '?a');
        $url->addRequestParameter('x', 'X');
        $this->assertEqual($url->getEncodedRequest(), '?a=&x=X');
    }
    
    function testParseEmptyParameter() {
        $url = new SimpleUrl('?a=');
        $this->assertEqual($url->getPath(), '');
        $this->assertEqual($url->getEncodedRequest(), '?a=');
        $url->addRequestParameter('x', 'X');
        $this->assertEqual($url->getEncodedRequest(), '?a=&x=X');
    }
    
    function testParseParameterPair() {
        $url = new SimpleUrl('?a=A');
        $this->assertEqual($url->getPath(), '');
        $this->assertEqual($url->getEncodedRequest(), '?a=A');
        $url->addRequestParameter('x', 'X');
        $this->assertEqual($url->getEncodedRequest(), '?a=A&x=X');
    }
    
    function testParseMultipleParameters() {
        $url = new SimpleUrl('?a=A&b=B');
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=B');
        $url->addRequestParameter('x', 'X');
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=B&x=X');
    }
    
    function testParsingParameterMixture() {
        $url = new SimpleUrl('?a=A&b=&c');
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=&c');
        $url->addRequestParameter('x', 'X');
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=&c=&x=X');
    }
    
    function testAddParametersFromScratch() {
        $url = new SimpleUrl('');
        $url->addRequestParameter('a', 'A');
        $this->assertEqual($url->getEncodedRequest(), '?a=A');
        $url->addRequestParameter('b', 'B');
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=B');
        $url->addRequestParameter('a', 'aaa');
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=B&a=aaa');
    }
    
    function testClearingParameters() {
        $url = new SimpleUrl('');
        $url->addRequestParameter('a', 'A');
        $url->clearRequest();
        $this->assertIdentical($url->getEncodedRequest(), '');
    }
    
    function testEncodingParameters() {
        $url = new SimpleUrl('');
        $url->addRequestParameter('a', '?!"\'#~@[]{}:;<>,./|£$%^&*()_+-=');
        $this->assertIdentical(
                $request = $url->getEncodedRequest(),
                '?a=%3F%21%22%27%23%7E%40%5B%5D%7B%7D%3A%3B%3C%3E%2C.%2F%7C%A3%24%25%5E%26%2A%28%29_%2B-%3D');
    }
    
    function testDecodingParameters() {            
        $url = new SimpleUrl('?a=%3F%21%22%27%23%7E%40%5B%5D%7B%7D%3A%3B%3C%3E%2C.%2F%7C%A3%24%25%5E%26%2A%28%29_%2B-%3D');
        $this->assertEqual(
                $url->getEncodedRequest(),
                '?a=' . urlencode('?!"\'#~@[]{}:;<>,./|£$%^&*()_+-='));
    }
    
    function testUrlInQueryDoesNotConfuseParsing() {
        $url = new SimpleUrl('wibble/login.php?url=http://www.google.com/moo/');
        $this->assertFalse($url->getScheme());
        $this->assertFalse($url->getHost());
        $this->assertEqual($url->getPath(), 'wibble/login.php');
        $this->assertEqual($url->getEncodedRequest(), '?url=http://www.google.com/moo/');
    }
    
    function testSettingCordinates() {
        $url = new SimpleUrl('');
        $url->setCoordinates('32', '45');
        $this->assertIdentical($url->getX(), 32);
        $this->assertIdentical($url->getY(), 45);
        $this->assertEqual($url->getEncodedRequest(), '');
    }
    
    function testParseCordinates() {
        $url = new SimpleUrl('?32,45');
        $this->assertIdentical($url->getX(), 32);
        $this->assertIdentical($url->getY(), 45);
    }
    
    function testClearingCordinates() {
        $url = new SimpleUrl('?32,45');
        $url->setCoordinates();
        $this->assertIdentical($url->getX(), false);
        $this->assertIdentical($url->getY(), false);
    }
    
    function testParsingParameterCordinateMixture() {
        $url = new SimpleUrl('?a=A&b=&c?32,45');
        $this->assertIdentical($url->getX(), 32);
        $this->assertIdentical($url->getY(), 45);
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=&c');
    }
    
    function testParsingParameterWithBadCordinates() {
        $url = new SimpleUrl('?a=A&b=&c?32');
        $this->assertIdentical($url->getX(), false);
        $this->assertIdentical($url->getY(), false);
        $this->assertEqual($url->getEncodedRequest(), '?a=A&b=&c?32');
    }
    
    function testPageSplitting() {
        $url = new SimpleUrl('./here/../there/somewhere.php');
        $this->assertEqual($url->getPath(), './here/../there/somewhere.php');
        $this->assertEqual($url->getPage(), 'somewhere.php');
        $this->assertEqual($url->getBasePath(), './here/../there/');
    }
    
    function testAbsolutePathPageSplitting() {
        $url = new SimpleUrl("http://host.com/here/there/somewhere.php");
        $this->assertEqual($url->getPath(), "/here/there/somewhere.php");
        $this->assertEqual($url->getPage(), "somewhere.php");
        $this->assertEqual($url->getBasePath(), "/here/there/");
    }
    
    function testSplittingUrlWithNoPageGivesEmptyPage() {
        $url = new SimpleUrl('/here/there/');
        $this->assertEqual($url->getPath(), '/here/there/');
        $this->assertEqual($url->getPage(), '');
        $this->assertEqual($url->getBasePath(), '/here/there/');
    }
    
    function testPathNormalisation() {
        $url = new SimpleUrl();
        $this->assertEqual(
                $url->normalisePath('https://host.com/I/am/here/../there/somewhere.php'),
                'https://host.com/I/am/there/somewhere.php');
    }

    // regression test for #1535407
    function testPathNormalisationWithSinglePeriod() {
        $url = new SimpleUrl();
        $this->assertEqual(
            $url->normalisePath('https://host.com/I/am/here/./../there/somewhere.php'),
            'https://host.com/I/am/there/somewhere.php');
    }
    
    // regression test for #1852413
    function testHostnameExtractedFromUContainingAtSign() {
        $url = new SimpleUrl("http://localhost/name@example.com");
        $this->assertEqual($url->getScheme(), "http");
        $this->assertEqual($url->getUsername(), "");
        $this->assertEqual($url->getPassword(), "");
        $this->assertEqual($url->getHost(), "localhost");
        $this->assertEqual($url->getPath(), "/name@example.com");
    }

    function testHostnameInLocalhost() {
        $url = new SimpleUrl("http://localhost/name/example.com");
        $this->assertEqual($url->getScheme(), "http");
        $this->assertEqual($url->getUsername(), "");
        $this->assertEqual($url->getPassword(), "");
        $this->assertEqual($url->getHost(), "localhost");
        $this->assertEqual($url->getPath(), "/name/example.com");
    }

    function testUsernameAndPasswordAreUrlDecoded() {
        $url = new SimpleUrl('http://' . urlencode('test@test') .
                ':' . urlencode('$!£@*&%') . '@www.lastcraft.com');
        $this->assertEqual($url->getUsername(), 'test@test');
        $this->assertEqual($url->getPassword(), '$!£@*&%');
    }
    
    function testBlitz() {
        $this->assertUrl(
                "https://username:password@www.somewhere.com:243/this/that/here.php?a=1&b=2#anchor",
                array("https", "username", "password", "www.somewhere.com", 243, "/this/that/here.php", "com", "?a=1&b=2", "anchor"),
                array("a" => "1", "b" => "2"));
        $this->assertUrl(
                "username:password@www.somewhere.com/this/that/here.php?a=1",
                array(false, "username", "password", "www.somewhere.com", false, "/this/that/here.php", "com", "?a=1", false),
                array("a" => "1"));
        $this->assertUrl(
                "username:password@somewhere.com:243?1,2",
                array(false, "username", "password", "somewhere.com", 243, "/", "com", "", false),
                array(),
                array(1, 2));
        $this->assertUrl(
                "https://www.somewhere.com",
                array("https", false, false, "www.somewhere.com", false, "/", "com", "", false));
        $this->assertUrl(
                "username@www.somewhere.com:243#anchor",
                array(false, "username", false, "www.somewhere.com", 243, "/", "com", "", "anchor"));
        $this->assertUrl(
                "/this/that/here.php?a=1&b=2?3,4",
                array(false, false, false, false, false, "/this/that/here.php", false, "?a=1&b=2", false),
                array("a" => "1", "b" => "2"),
                array(3, 4));
        $this->assertUrl(
                "username@/here.php?a=1&b=2",
                array(false, "username", false, false, false, "/here.php", false, "?a=1&b=2", false),
                array("a" => "1", "b" => "2"));
    }
    
    function testAmbiguousHosts() {
        $this->assertUrl(
                "tigger",
                array(false, false, false, false, false, "tigger", false, "", false));
        $this->assertUrl(
                "/tigger",
                array(false, false, false, false, false, "/tigger", false, "", false));
        $this->assertUrl(
                "//tigger",
                array(false, false, false, "tigger", false, "/", false, "", false));
        $this->assertUrl(
                "//tigger/",
                array(false, false, false, "tigger", false, "/", false, "", false));
        $this->assertUrl(
                "tigger.com",
                array(false, false, false, "tigger.com", false, "/", "com", "", false));
        $this->assertUrl(
                "me.net/tigger",
                array(false, false, false, "me.net", false, "/tigger", "net", "", false));
    }
    
    function testAsString() {
        $this->assertPreserved('https://www.here.com');
        $this->assertPreserved('http://me:secret@www.here.com');
        $this->assertPreserved('http://here/there');
        $this->assertPreserved('http://here/there?a=A&b=B');
        $this->assertPreserved('http://here/there?a=1&a=2');
        $this->assertPreserved('http://here/there?a=1&a=2?9,8');
        $this->assertPreserved('http://host?a=1&a=2');
        $this->assertPreserved('http://host#stuff');
        $this->assertPreserved('http://me:secret@www.here.com/a/b/c/here.html?a=A?7,6');
        $this->assertPreserved('http://www.here.com/?a=A__b=B');
        $this->assertPreserved('http://www.example.com:8080/');
    }
    
    function assertUrl($raw, $parts, $params = false, $coords = false) {
        if (! is_array($params)) {
            $params = array();
        }
        $url = new SimpleUrl($raw);
        $this->assertIdentical($url->getScheme(), $parts[0], "[$raw] scheme -> %s");
        $this->assertIdentical($url->getUsername(), $parts[1], "[$raw] username -> %s");
        $this->assertIdentical($url->getPassword(), $parts[2], "[$raw] password -> %s");
        $this->assertIdentical($url->getHost(), $parts[3], "[$raw] host -> %s");
        $this->assertIdentical($url->getPort(), $parts[4], "[$raw] port -> %s");
        $this->assertIdentical($url->getPath(), $parts[5], "[$raw] path -> %s");
        $this->assertIdentical($url->getTld(), $parts[6], "[$raw] tld -> %s");
        $this->assertIdentical($url->getEncodedRequest(), $parts[7], "[$raw] encoded -> %s");
        $this->assertIdentical($url->getFragment(), $parts[8], "[$raw] fragment -> %s");
        if ($coords) {
            $this->assertIdentical($url->getX(), $coords[0], "[$raw] x -> %s");
            $this->assertIdentical($url->getY(), $coords[1], "[$raw] y -> %s");
        }
    }
    
    function testUrlWithTwoSlashesInPath() {
        $url = new SimpleUrl('/article/categoryedit/insert//');
        $this->assertEqual($url->getPath(), '/article/categoryedit/insert//');
    }
    
    function assertPreserved($string) {
        $url = new SimpleUrl($string);
        $this->assertEqual($url->asString(), $string);
    }
}

class TestOfAbsoluteUrls extends UnitTestCase {
    
	function testDirectoriesAfterFilename() {
		$string = '../../index.php/foo/bar';
		$url = new SimpleUrl($string);
		$this->assertEqual($url->asString(), $string);
		
		$absolute = $url->makeAbsolute('http://www.domain.com/some/path/');
		$this->assertEqual($absolute->asString(), 'http://www.domain.com/index.php/foo/bar');
	}

    function testMakingAbsolute() {
        $url = new SimpleUrl('../there/somewhere.php');
        $this->assertEqual($url->getPath(), '../there/somewhere.php');
        $absolute = $url->makeAbsolute('https://host.com:1234/I/am/here/');
        $this->assertEqual($absolute->getScheme(), 'https');
        $this->assertEqual($absolute->getHost(), 'host.com');
        $this->assertEqual($absolute->getPort(), 1234);
        $this->assertEqual($absolute->getPath(), '/I/am/there/somewhere.php');
    }
    
    function testMakingAnEmptyUrlAbsolute() {
        $url = new SimpleUrl('');
        $this->assertEqual($url->getPath(), '');
        $absolute = $url->makeAbsolute('http://host.com/I/am/here/page.html');
        $this->assertEqual($absolute->getScheme(), 'http');
        $this->assertEqual($absolute->getHost(), 'host.com');
        $this->assertEqual($absolute->getPath(), '/I/am/here/page.html');
    }
    
    function testMakingAnEmptyUrlAbsoluteWithMissingPageName() {
        $url = new SimpleUrl('');
        $this->assertEqual($url->getPath(), '');
        $absolute = $url->makeAbsolute('http://host.com/I/am/here/');
        $this->assertEqual($absolute->getScheme(), 'http');
        $this->assertEqual($absolute->getHost(), 'host.com');
        $this->assertEqual($absolute->getPath(), '/I/am/here/');
    }
    
    function testMakingAShortQueryUrlAbsolute() {
        $url = new SimpleUrl('?a#b');
        $this->assertEqual($url->getPath(), '');
        $absolute = $url->makeAbsolute('http://host.com/I/am/here/');
        $this->assertEqual($absolute->getScheme(), 'http');
        $this->assertEqual($absolute->getHost(), 'host.com');
        $this->assertEqual($absolute->getPath(), '/I/am/here/');
        $this->assertEqual($absolute->getEncodedRequest(), '?a');
        $this->assertEqual($absolute->getFragment(), 'b');
    }
    
    function testMakingADirectoryUrlAbsolute() {
        $url = new SimpleUrl('hello/');
        $this->assertEqual($url->getPath(), 'hello/');
        $this->assertEqual($url->getBasePath(), 'hello/');
        $this->assertEqual($url->getPage(), '');
        $absolute = $url->makeAbsolute('http://host.com/I/am/here/page.html');
        $this->assertEqual($absolute->getPath(), '/I/am/here/hello/');
    }
    
    function testMakingARootUrlAbsolute() {
        $url = new SimpleUrl('/');
        $this->assertEqual($url->getPath(), '/');
        $absolute = $url->makeAbsolute('http://host.com/I/am/here/page.html');
        $this->assertEqual($absolute->getPath(), '/');
    }
    
    function testMakingARootPageUrlAbsolute() {
        $url = new SimpleUrl('/here.html');
        $absolute = $url->makeAbsolute('http://host.com/I/am/here/page.html');
        $this->assertEqual($absolute->getPath(), '/here.html');
    }
    
    function testCarryAuthenticationFromRootPage() {
        $url = new SimpleUrl('here.html');
        $absolute = $url->makeAbsolute('http://test:secret@host.com/');
        $this->assertEqual($absolute->getPath(), '/here.html');
        $this->assertEqual($absolute->getUsername(), 'test');
        $this->assertEqual($absolute->getPassword(), 'secret');
    }
    
    function testMakingCoordinateUrlAbsolute() {
        $url = new SimpleUrl('?1,2');
        $this->assertEqual($url->getPath(), '');
        $absolute = $url->makeAbsolute('http://host.com/I/am/here/');
        $this->assertEqual($absolute->getScheme(), 'http');
        $this->assertEqual($absolute->getHost(), 'host.com');
        $this->assertEqual($absolute->getPath(), '/I/am/here/');
        $this->assertEqual($absolute->getX(), 1);
        $this->assertEqual($absolute->getY(), 2);
    }
    
    function testMakingAbsoluteAppendedPath() {
        $url = new SimpleUrl('./there/somewhere.php');
        $absolute = $url->makeAbsolute('https://host.com/here/');
        $this->assertEqual($absolute->getPath(), '/here/there/somewhere.php');
    }
    
    function testMakingAbsoluteBadlyFormedAppendedPath() {
        $url = new SimpleUrl('there/somewhere.php');
        $absolute = $url->makeAbsolute('https://host.com/here/');
        $this->assertEqual($absolute->getPath(), '/here/there/somewhere.php');
    }
    
    function testMakingAbsoluteHasNoEffectWhenAlreadyAbsolute() {
        $url = new SimpleUrl('https://test:secret@www.lastcraft.com:321/stuff/?a=1#f');
        $absolute = $url->makeAbsolute('http://host.com/here/');
        $this->assertEqual($absolute->getScheme(), 'https');
        $this->assertEqual($absolute->getUsername(), 'test');
        $this->assertEqual($absolute->getPassword(), 'secret');
        $this->assertEqual($absolute->getHost(), 'www.lastcraft.com');
        $this->assertEqual($absolute->getPort(), 321);
        $this->assertEqual($absolute->getPath(), '/stuff/');
        $this->assertEqual($absolute->getEncodedRequest(), '?a=1');
        $this->assertEqual($absolute->getFragment(), 'f');
    }
    
    function testMakingAbsoluteCarriesAuthenticationWhenAlreadyAbsolute() {
        $url = new SimpleUrl('https://www.lastcraft.com');
        $absolute = $url->makeAbsolute('http://test:secret@host.com/here/');
        $this->assertEqual($absolute->getHost(), 'www.lastcraft.com');
        $this->assertEqual($absolute->getUsername(), 'test');
        $this->assertEqual($absolute->getPassword(), 'secret');
    }
    
    function testMakingHostOnlyAbsoluteDoesNotCarryAnyOtherInformation() {
        $url = new SimpleUrl('http://www.lastcraft.com');
        $absolute = $url->makeAbsolute('https://host.com:81/here/');
        $this->assertEqual($absolute->getScheme(), 'http');
        $this->assertEqual($absolute->getHost(), 'www.lastcraft.com');
        $this->assertIdentical($absolute->getPort(), false);
        $this->assertEqual($absolute->getPath(), '/');
    }
}

class TestOfFrameUrl extends UnitTestCase {
    
    function testTargetAttachment() {
        $url = new SimpleUrl('http://www.site.com/home.html');
        $this->assertIdentical($url->getTarget(), false);
        $url->setTarget('A frame');
        $this->assertIdentical($url->getTarget(), 'A frame');
    }
}

/**
 * @note Based off of http://www.mozilla.org/quality/networking/testing/filetests.html
 */
class TestOfFileUrl extends UnitTestCase {
    
    function testMinimalUrl() {
        $url = new SimpleUrl('file:///');
        $this->assertEqual($url->getScheme(), 'file');
        $this->assertIdentical($url->getHost(), false);
        $this->assertEqual($url->getPath(), '/');
    }
    
    function testUnixUrl() {
        $url = new SimpleUrl('file:///fileInRoot');
        $this->assertEqual($url->getScheme(), 'file');
        $this->assertIdentical($url->getHost(), false);
        $this->assertEqual($url->getPath(), '/fileInRoot');
    }
    
    function testDOSVolumeUrl() {
        $url = new SimpleUrl('file:///C:/config.sys');
        $this->assertEqual($url->getScheme(), 'file');
        $this->assertIdentical($url->getHost(), false);
        $this->assertEqual($url->getPath(), '/C:/config.sys');
    }
    
    function testDOSVolumePromotion() {
        $url = new SimpleUrl('file://C:/config.sys');
        $this->assertEqual($url->getScheme(), 'file');
        $this->assertIdentical($url->getHost(), false);
        $this->assertEqual($url->getPath(), '/C:/config.sys');
    }
    
    function testDOSBackslashes() {
        $url = new SimpleUrl('file:///C:\config.sys');
        $this->assertEqual($url->getScheme(), 'file');
        $this->assertIdentical($url->getHost(), false);
        $this->assertEqual($url->getPath(), '/C:/config.sys');
    }
    
    function testDOSDirnameAfterFile() {
        $url = new SimpleUrl('file://C:\config.sys');
        $this->assertEqual($url->getScheme(), 'file');
        $this->assertIdentical($url->getHost(), false);
        $this->assertEqual($url->getPath(), '/C:/config.sys');
    }
    
}

?>