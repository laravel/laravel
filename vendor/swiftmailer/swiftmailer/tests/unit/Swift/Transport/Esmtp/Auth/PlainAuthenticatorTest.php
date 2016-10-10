<?php

class Swift_Transport_Esmtp_Auth_PlainAuthenticatorTest extends \SwiftMailerTestCase
{
    private $_agent;

    public function setUp()
    {
        $this->_agent = $this->getMockery('Swift_Transport_SmtpAgent')->shouldIgnoreMissing();
    }

    public function testKeywordIsPlain()
    {
        /* -- RFC 4616, 1.
        The name associated with this mechanism is "PLAIN".
        */

        $login = $this->_getAuthenticator();
        $this->assertEquals('PLAIN', $login->getAuthKeyword());
    }

    public function testSuccessfulAuthentication()
    {
        /* -- RFC 4616, 2.
        The client presents the authorization identity (identity to act as),
        followed by a NUL (U+0000) character, followed by the authentication
        identity (identity whose password will be used), followed by a NUL
        (U+0000) character, followed by the clear-text password.
        */

        $plain = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with('AUTH PLAIN '.base64_encode(
                        'jack'.chr(0).'jack'.chr(0).'pass'
                    )."\r\n", array(235));

        $this->assertTrue($plain->authenticate($this->_agent, 'jack', 'pass'),
            '%s: The buffer accepted all commands authentication should succeed'
            );
    }

    public function testAuthenticationFailureSendRsetAndReturnFalse()
    {
        $plain = $this->_getAuthenticator();

        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with('AUTH PLAIN '.base64_encode(
                        'jack'.chr(0).'jack'.chr(0).'pass'
                    )."\r\n", array(235))
             ->andThrow(new Swift_TransportException(''));
        $this->_agent->shouldReceive('executeCommand')
             ->once()
             ->with("RSET\r\n", array(250));

        $this->assertFalse($plain->authenticate($this->_agent, 'jack', 'pass'),
            '%s: Authentication fails, so RSET should be sent'
            );
    }

    // -- Private helpers

    private function _getAuthenticator()
    {
        return new Swift_Transport_Esmtp_Auth_PlainAuthenticator();
    }
}
