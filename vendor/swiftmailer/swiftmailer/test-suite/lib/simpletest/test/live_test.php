<?php
// $Id: live_test.php 1748 2008-04-14 01:50:41Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../socket.php');
require_once(dirname(__FILE__) . '/../http.php');
require_once(dirname(__FILE__) . '/../compatibility.php');

if (SimpleTest::getDefaultProxy()) {
    SimpleTest::ignore('LiveHttpTestCase');
}

class LiveHttpTestCase extends UnitTestCase {

    function testBadSocket() {
        $socket = new SimpleSocket('bad_url', 111, 5);
        $this->assertTrue($socket->isError());
        $this->assertPattern(
                '/Cannot open \\[bad_url:111\\] with \\[/',
                $socket->getError());
        $this->assertFalse($socket->isOpen());
        $this->assertFalse($socket->write('A message'));
    }
    
    function testSocketClosure() {
        $socket = new SimpleSocket('www.lastcraft.com', 80, 15, 8);
        $this->assertTrue($socket->isOpen());
        $this->assertTrue($socket->write("GET /test/network_confirm.php HTTP/1.0\r\n"));
        $socket->write("Host: www.lastcraft.com\r\n");
        $socket->write("Connection: close\r\n\r\n");
        $this->assertEqual($socket->read(), "HTTP/1.1");
        $socket->close();
        $this->assertIdentical($socket->read(), false);
    }
    
    function testRecordOfSentCharacters() {
        $socket = new SimpleSocket('www.lastcraft.com', 80, 15);
        $this->assertTrue($socket->write("GET /test/network_confirm.php HTTP/1.0\r\n"));
        $socket->write("Host: www.lastcraft.com\r\n");
        $socket->write("Connection: close\r\n\r\n");
        $socket->close();
        $this->assertEqual($socket->getSent(),
                "GET /test/network_confirm.php HTTP/1.0\r\n" .
                "Host: www.lastcraft.com\r\n" .
                "Connection: close\r\n\r\n");
    }
}
?>