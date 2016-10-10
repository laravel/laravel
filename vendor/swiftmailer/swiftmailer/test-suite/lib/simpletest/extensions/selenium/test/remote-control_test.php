<?php
// $Id: remote-control_test.php 1642 2008-01-22 20:49:15Z pp11 $
require_once(dirname(__FILE__) . '/../../../autorun.php');
require_once(dirname(__FILE__) . '/../remote-control.php');

class TestOfSimpleSeleniumRemoteControl extends UnitTestCase {
	function testSesssionIdShouldBePreserved() {
		$remote_control = new SimpleSeleniumRemoteControl("tester", "http://simpletest.org/");
		$this->assertEqual($remote_control->sessionIdParser('OK,123456789123456789'), '123456789123456789');
	}
	
	function testIsUpReturnsFalseWhenDirectedToLocalhostDown() {
		$remote_control = new SimpleSeleniumRemoteControl("tester", "http://simpletest.org/", "localhost-down");;
		$this->assertFalse($remote_control->isUp());
	}

	function testIsUpReturnsTrueWhenDirectedToLocalhostOnPort80() {
		$remote_control = new SimpleSeleniumRemoteControl("tester", "http://simpletest.org/", "localhost", "80");
		$this->assertTrue($remote_control->isUp());
	}
}

class TestOfSimpleSeleniumRemoteControlWhenItIsUp extends UnitTestCase {
	function skip() {
        $remote_control = new SimpleSeleniumRemoteControl("*custom opera -nosession", "http://simpletest.org/");
		$this->skipUnless($remote_control->isUp(), 'Remote control tests desperatly need a working Selenium Server.');
	}

    function testOfCommandCreation() {
        $remote_control = new SimpleSeleniumRemoteControl("tester", "http://simpletest.org/");
        $this->assertEqual($remote_control->buildUrlCmd("test"), 'http://localhost:4444/selenium-server/driver/?cmd=test');
        $this->assertEqual($remote_control->buildUrlCmd("test", array("next")), 'http://localhost:4444/selenium-server/driver/?cmd=test&1=next');
        $this->assertEqual($remote_control->buildUrlCmd("test", array("ŽtŽ")), 'http://localhost:4444/selenium-server/driver/?cmd=test&1=%C3%A9t%C3%A9');
        $this->assertEqual($remote_control->buildUrlCmd("test", array("next", "then")), 'http://localhost:4444/selenium-server/driver/?cmd=test&1=next&2=then');
    }
}