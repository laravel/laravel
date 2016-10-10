<?php

class Swift_Transport_Esmtp_Auth_LoginAuthenticatorTest extends \SwiftMailerTestCase
{
    private $_agent;

    public function setUp()
    {
        $this->_agent = $this->getMockery('Swift_Transport_SmtpAgent')->shouldIgnoreMissing();
    }

    public function testKeywordIsLogin()
    {
        $login = $this->_getAuthenticator();
        $this->assertEquals('LOGIN', $login->getAuthKeyword());
    }

    public function testSuccessfulAuthentication()
    {
        $login = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with("AUTH LOGIN\r\n", array(334));
        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with(base64_encode('jack')."\r\n", array(334));
        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with(base64_encode('pass')."\r\n", array(235));

        $this->assertTrue($login->authenticate($this->_agent, 'jack', 'pass'),
            '%s: The buffer accepted all commands authentication should succeed'
            );
    }

    public function testAuthenticationFailureSendRsetAndReturnFalse()
    {
        $login = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with("AUTH LOGIN\r\n", array(334));
        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with(base64_encode('jack')."\r\n", array(334));
        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with(base64_encode('pass')."\r\n", array(235))
             ->andThrow(new Swift_TransportException(''));
        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with("RSET\r\n", array(250));

        $this->assertFalse($login->authenticate($this->_agent, 'jack', 'pass'),
            '%s: Authentication fails, so RSET should be sent'
            );
    }

    // -- Private helpers

    private function _getAuthenticator()
    {
        return new Swift_Transport_Esmtp_Auth_LoginAuthenticator();
    }
}
