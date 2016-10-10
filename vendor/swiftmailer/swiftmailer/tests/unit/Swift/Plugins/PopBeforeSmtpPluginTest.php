<?php

class Swift_Plugins_PopBeforeSmtpPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testPluginConnectsToPop3HostBeforeTransportStarts()
    {
        $connection = $this->_createConnection();
        $connection->expects($this->once())
                   ->method('connect');

        $plugin = $this->_createPlugin('pop.host.tld', 110);
        $plugin->setConnection($connection);

        $transport = $this->_createTransport();
        $evt = $this->_createTransportChangeEvent($transport);

        $plugin->beforeTransportStarted($evt);
    }

    public function testPluginDisconnectsFromPop3HostBeforeTransportStarts()
    {
        $connection = $this->_createConnection();
        $connection->expects($this->once())
                   ->method('disconnect');

        $plugin = $this->_createPlugin('pop.host.tld', 110);
        $plugin->setConnection($connection);

        $transport = $this->_createTransport();
        $evt = $this->_createTransportChangeEvent($transport);

        $plugin->beforeTransportStarted($evt);
    }

    public function testPluginDoesNotConnectToSmtpIfBoundToDifferentTransport()
    {
        $connection = $this->_createConnection();
        $connection->expects($this->never())
                   ->method('disconnect');
        $connection->expects($this->never())
                   ->method('connect');

        $smtp = $this->_createTransport();

        $plugin = $this->_createPlugin('pop.host.tld', 110);
        $plugin->setConnection($connection);
        $plugin->bindSmtp($smtp);

        $transport = $this->_createTransport();
        $evt = $this->_createTransportChangeEvent($transport);

        $plugin->beforeTransportStarted($evt);
    }

    public function testPluginCanBindToSpecificTransport()
    {
        $connection = $this->_createConnection();
        $connection->expects($this->once())
                   ->method('connect');

        $smtp = $this->_createTransport();

        $plugin = $this->_createPlugin('pop.host.tld', 110);
        $plugin->setConnection($connection);
        $plugin->bindSmtp($smtp);

        $evt = $this->_createTransportChangeEvent($smtp);

        $plugin->beforeTransportStarted($evt);
    }

    // -- Creation Methods

    private function _createTransport()
    {
        return $this->getMock('Swift_Transport');
    }

    private function _createTransportChangeEvent($transport)
    {
        $evt = $this->getMockBuilder('Swift_Events_TransportChangeEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($transport));
        $evt->expects($this->any())
            ->method('getTransport')
            ->will($this->returnValue($transport));

        return $evt;
    }

    public function _createConnection()
    {
        return $this->getMock('Swift_Plugins_Pop_Pop3Connection');
    }

    public function _createPlugin($host, $port, $crypto = null)
    {
        return new Swift_Plugins_PopBeforeSmtpPlugin($host, $port, $crypto);
    }
}
