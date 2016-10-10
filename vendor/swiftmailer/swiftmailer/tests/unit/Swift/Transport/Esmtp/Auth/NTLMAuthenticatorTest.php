<?php

class Swift_Transport_Esmtp_Auth_NTLMAuthenticatorTest extends \SwiftMailerTestCase
{
    private $_message1 = '4e544c4d535350000100000007020000';
    private $_message2 = '4e544c4d53535000020000000c000c003000000035828980514246973ea892c10000000000000000460046003c00000054004500530054004e00540002000c0054004500530054004e00540001000c004d0045004d0042004500520003001e006d0065006d006200650072002e0074006500730074002e0063006f006d0000000000';
    private $_message3 = '4e544c4d5353500003000000180018006000000076007600780000000c000c0040000000080008004c0000000c000c0054000000000000009a0000000102000054004500530054004e00540074006500730074004d0045004d00420045005200bf2e015119f6bdb3f6fdb768aa12d478f5ce3d2401c8f6e9caa4da8f25d5e840974ed8976d3ada46010100000000000030fa7e3c677bc301f5ce3d2401c8f6e90000000002000c0054004500530054004e00540001000c004d0045004d0042004500520003001e006d0065006d006200650072002e0074006500730074002e0063006f006d000000000000000000';

    public function setUp()
    {
        if (!function_exists('mcrypt_module_open') || !function_exists('openssl_random_pseudo_bytes') || !function_exists('bcmul') || !function_exists('iconv')) {
            $this->markTestSkipped(
                'One of the required functions is not available.'
             );
        }
    }

    public function testKeywordIsNtlm()
    {
        $login = $this->_getAuthenticator();
        $this->assertEquals('NTLM', $login->getAuthKeyword());
    }

    public function testMessage1Generator()
    {
        $login = $this->_getAuthenticator();
        $message1 = $this->_invokePrivateMethod('createMessage1', $login);

        $this->assertEquals($this->_message1, bin2hex($message1),
            '%s: We send the smallest ntlm message which should never fail.'
        );
    }

    public function testLMv1Generator()
    {
        $password = 'test1234';
        $challenge = 'b019d38bad875c9d';
        $lmv1 = '1879f60127f8a877022132ec221bcbf3ca016a9f76095606';

        $login = $this->_getAuthenticator();
        $lmv1Result = $this->_invokePrivateMethod('createLMPassword', $login, array($password, $this->hex2bin($challenge)));

        $this->assertEquals($lmv1, bin2hex($lmv1Result),
            '%s: The keys should be the same cause we use the same values to generate them.'
        );
    }

    public function testLMv2Generator()
    {
        $username = 'user';
        $password = 'SecREt01';
        $domain = 'DOMAIN';
        $challenge = '0123456789abcdef';
        $lmv2 = 'd6e6152ea25d03b7c6ba6629c2d6aaf0ffffff0011223344';

        $login = $this->_getAuthenticator();
        $lmv2Result = $this->_invokePrivateMethod('createLMv2Password', $login, array($password, $username, $domain, $this->hex2bin($challenge), $this->hex2bin('ffffff0011223344')));

        $this->assertEquals($lmv2, bin2hex($lmv2Result),
            '%s: The keys should be the same cause we use the same values to generate them.'
        );
    }

    public function testMessage3v1Generator()
    {
        $username = 'test';
        $domain = 'TESTNT';
        $workstation = 'MEMBER';
        $lmResponse = '1879f60127f8a877022132ec221bcbf3ca016a9f76095606';
        $ntlmResponse = 'e6285df3287c5d194f84df1a94817c7282d09754b6f9e02a';
        $message3T = '4e544c4d5353500003000000180018006000000018001800780000000c000c0040000000080008004c0000000c000c0054000000000000009a0000000102000054004500530054004e00540074006500730074004d0045004d004200450052001879f60127f8a877022132ec221bcbf3ca016a9f76095606e6285df3287c5d194f84df1a94817c7282d09754b6f9e02a';

        $login = $this->_getAuthenticator();
        $message3 = $this->_invokePrivateMethod('createMessage3', $login, array($domain, $username, $workstation, $this->hex2bin($lmResponse), $this->hex2bin($ntlmResponse)));

        $this->assertEquals($message3T, bin2hex($message3),
            '%s: We send the same information as the example is created with so this should be the same'
        );
    }

    public function testMessage3v2Generator()
    {
        $username = 'test';
        $domain = 'TESTNT';
        $workstation = 'MEMBER';
        $lmResponse = 'bf2e015119f6bdb3f6fdb768aa12d478f5ce3d2401c8f6e9';
        $ntlmResponse = 'caa4da8f25d5e840974ed8976d3ada46010100000000000030fa7e3c677bc301f5ce3d2401c8f6e90000000002000c0054004500530054004e00540001000c004d0045004d0042004500520003001e006d0065006d006200650072002e0074006500730074002e0063006f006d000000000000000000';

        $login = $this->_getAuthenticator();
        $message3 = $this->_invokePrivateMethod('createMessage3', $login, array($domain, $username, $workstation, $this->hex2bin($lmResponse), $this->hex2bin($ntlmResponse)));

        $this->assertEquals($this->_message3, bin2hex($message3),
            '%s: We send the same information as the example is created with so this should be the same'
        );
    }

    public function testGetDomainAndUsername()
    {
        $username = "DOMAIN\user";

        $login = $this->_getAuthenticator();
        list($domain, $user) = $this->_invokePrivateMethod('getDomainAndUsername', $login, array($username));

        $this->assertEquals('DOMAIN', $domain,
            '%s: the fetched domain did not match'
        );
        $this->assertEquals('user', $user,
            '%s: the fetched user did not match'
        );
    }

    public function testGetDomainAndUsernameWithExtension()
    {
        $username = "domain.com\user";

        $login = $this->_getAuthenticator();
        list($domain, $user) = $this->_invokePrivateMethod('getDomainAndUsername', $login, array($username));

        $this->assertEquals('domain.com', $domain,
            '%s: the fetched domain did not match'
        );
        $this->assertEquals('user', $user,
            '%s: the fetched user did not match'
        );
    }

    public function testGetDomainAndUsernameWithAtSymbol()
    {
        $username = 'user@DOMAIN';

        $login = $this->_getAuthenticator();
        list($domain, $user) = $this->_invokePrivateMethod('getDomainAndUsername', $login, array($username));

        $this->assertEquals('DOMAIN', $domain,
            '%s: the fetched domain did not match'
        );
        $this->assertEquals('user', $user,
            '%s: the fetched user did not match'
        );
    }

    public function testGetDomainAndUsernameWithAtSymbolAndExtension()
    {
        $username = 'user@domain.com';

        $login = $this->_getAuthenticator();
        list($domain, $user) = $this->_invokePrivateMethod('getDomainAndUsername', $login, array($username));

        $this->assertEquals('domain.com', $domain,
            '%s: the fetched domain did not match'
        );
        $this->assertEquals('user', $user,
            '%s: the fetched user did not match'
        );
    }

    public function testSuccessfulAuthentication()
    {
        $domain = 'TESTNT';
        $username = 'test';
        $secret = 'test1234';

        $ntlm = $this->_getAuthenticator();
        $agent = $this->_getAgent();
        $agent->shouldReceive('executeCommand')
              ->once()
              ->with('AUTH NTLM '.base64_encode(
                        $this->_invokePrivateMethod('createMessage1', $ntlm)
                    )."\r\n", array(334))
              ->andReturn('334 '.base64_encode($this->hex2bin('4e544c4d53535000020000000c000c003000000035828980514246973ea892c10000000000000000460046003c00000054004500530054004e00540002000c0054004500530054004e00540001000c004d0045004d0042004500520003001e006d0065006d006200650072002e0074006500730074002e0063006f006d0000000000')));
        $agent->shouldReceive('executeCommand')
              ->once()
              ->with(base64_encode(
                        $this->_invokePrivateMethod('createMessage3', $ntlm, array($domain, $username, $this->hex2bin('4d0045004d00420045005200'), $this->hex2bin('bf2e015119f6bdb3f6fdb768aa12d478f5ce3d2401c8f6e9'), $this->hex2bin('caa4da8f25d5e840974ed8976d3ada46010100000000000030fa7e3c677bc301f5ce3d2401c8f6e90000000002000c0054004500530054004e00540001000c004d0045004d0042004500520003001e006d0065006d006200650072002e0074006500730074002e0063006f006d000000000000000000'))
                    ))."\r\n", array(235));

        $this->assertTrue($ntlm->authenticate($agent, $username.'@'.$domain, $secret, $this->hex2bin('30fa7e3c677bc301'), $this->hex2bin('f5ce3d2401c8f6e9')),
            '%s: The buffer accepted all commands authentication should succeed'
        );
    }

    public function testAuthenticationFailureSendRsetAndReturnFalse()
    {
        $domain = 'TESTNT';
        $username = 'test';
        $secret = 'test1234';

        $ntlm = $this->_getAuthenticator();
        $agent = $this->_getAgent();
        $agent->shouldReceive('executeCommand')
              ->once()
              ->with('AUTH NTLM '.base64_encode(
                        $this->_invokePrivateMethod('createMessage1', $ntlm)
                    )."\r\n", array(334))
              ->andThrow(new Swift_TransportException(''));
        $agent->shouldReceive('executeCommand')
              ->once()
              ->with("RSET\r\n", array(250));

        $this->assertFalse($ntlm->authenticate($agent, $username.'@'.$domain, $secret, $this->hex2bin('30fa7e3c677bc301'), $this->hex2bin('f5ce3d2401c8f6e9')),
            '%s: Authentication fails, so RSET should be sent'
        );
    }

    // -- Private helpers
    private function _getAuthenticator()
    {
        return new Swift_Transport_Esmtp_Auth_NTLMAuthenticator();
    }

    private function _getAgent()
    {
        return $this->getMockery('Swift_Transport_SmtpAgent')->shouldIgnoreMissing();
    }

    private function _invokePrivateMethod($method, $instance, array $args = array())
    {
        $methodC = new ReflectionMethod($instance, trim($method));
        $methodC->setAccessible(true);

        return $methodC->invokeArgs($instance, $args);
    }

    /**
     * Hex2bin replacement for < PHP 5.4.
     *
     * @param string $hex
     *
     * @return string Binary
     */
    protected function hex2bin($hex)
    {
        return function_exists('hex2bin') ? hex2bin($hex) : pack('H*', $hex);
    }
}
