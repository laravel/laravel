<?php

class Swift_Transport_Esmtp_AuthHandlerTest extends \SwiftMailerTestCase
{
    private $_agent;

    public function setUp()
    {
        $this->_agent = $this->getMockery('Swift_Transport_SmtpAgent')->shouldIgnoreMissing();
    }

    public function testKeywordIsAuth()
    {
        $auth = $this->_createHandler(array());
        $this->assertEquals('AUTH', $auth->getHandledKeyword());
    }

    public function testUsernameCanBeSetAndFetched()
    {
        $auth = $this->_createHandler(array());
        $auth->setUsername('jack');
        $this->assertEquals('jack', $auth->getUsername());
    }

    public function testPasswordCanBeSetAndFetched()
    {
        $auth = $this->_createHandler(array());
        $auth->setPassword('pass');
        $this->assertEquals('pass', $auth->getPassword());
    }

    public function testAuthModeCanBeSetAndFetched()
    {
        $auth = $this->_createHandler(array());
        $auth->setAuthMode('PLAIN');
        $this->assertEquals('PLAIN', $auth->getAuthMode());
    }

    public function testMixinMethods()
    {
        $auth = $this->_createHandler(array());
        $mixins = $auth->exposeMixinMethods();
        $this->assertTrue(in_array('getUsername', $mixins),
            '%s: getUsername() should be accessible via mixin'
            );
        $this->assertTrue(in_array('setUsername', $mixins),
            '%s: setUsername() should be accessible via mixin'
            );
        $this->assertTrue(in_array('getPassword', $mixins),
            '%s: getPassword() should be accessible via mixin'
            );
        $this->assertTrue(in_array('setPassword', $mixins),
            '%s: setPassword() should be accessible via mixin'
            );
        $this->assertTrue(in_array('setAuthMode', $mixins),
            '%s: setAuthMode() should be accessible via mixin'
            );
        $this->assertTrue(in_array('getAuthMode', $mixins),
            '%s: getAuthMode() should be accessible via mixin'
            );
    }

    public function testAuthenticatorsAreCalledAccordingToParamsAfterEhlo()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');

        $a1->shouldReceive('authenticate')
           ->never()
           ->with($this->_agent, 'jack', 'pass');
        $a2->shouldReceive('authenticate')
           ->once()
           ->with($this->_agent, 'jack', 'pass')
           ->andReturn(true);

        $auth = $this->_createHandler(array($a1, $a2));
        $auth->setUsername('jack');
        $auth->setPassword('pass');

        $auth->setKeywordParams(array('CRAM-MD5', 'LOGIN'));
        $auth->afterEhlo($this->_agent);
    }

    public function testAuthenticatorsAreNotUsedIfNoUsernameSet()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');

        $a1->shouldReceive('authenticate')
           ->never()
           ->with($this->_agent, 'jack', 'pass');
        $a2->shouldReceive('authenticate')
           ->never()
           ->with($this->_agent, 'jack', 'pass')
           ->andReturn(true);

        $auth = $this->_createHandler(array($a1, $a2));

        $auth->setKeywordParams(array('CRAM-MD5', 'LOGIN'));
        $auth->afterEhlo($this->_agent);
    }

    public function testSeveralAuthenticatorsAreTriedIfNeeded()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');

        $a1->shouldReceive('authenticate')
           ->once()
           ->with($this->_agent, 'jack', 'pass')
           ->andReturn(false);
        $a2->shouldReceive('authenticate')
           ->once()
           ->with($this->_agent, 'jack', 'pass')
           ->andReturn(true);

        $auth = $this->_createHandler(array($a1, $a2));
        $auth->setUsername('jack');
        $auth->setPassword('pass');

        $auth->setKeywordParams(array('PLAIN', 'LOGIN'));
        $auth->afterEhlo($this->_agent);
    }

    public function testFirstAuthenticatorToPassBreaksChain()
    {
        $a1 = $this->_createMockAuthenticator('PLAIN');
        $a2 = $this->_createMockAuthenticator('LOGIN');
        $a3 = $this->_createMockAuthenticator('CRAM-MD5');

        $a1->shouldReceive('authenticate')
           ->once()
           ->with($this->_agent, 'jack', 'pass')
           ->andReturn(false);
        $a2->shouldReceive('authenticate')
           ->once()
           ->with($this->_agent, 'jack', 'pass')
           ->andReturn(true);
        $a3->shouldReceive('authenticate')
           ->never()
           ->with($this->_agent, 'jack', 'pass');

        $auth = $this->_createHandler(array($a1, $a2));
        $auth->setUsername('jack');
        $auth->setPassword('pass');

        $auth->setKeywordParams(array('PLAIN', 'LOGIN', 'CRAM-MD5'));
        $auth->afterEhlo($this->_agent);
    }

    // -- Private helpers

    private function _createHandler($authenticators)
    {
        return new Swift_Transport_Esmtp_AuthHandler($authenticators);
    }

    private function _createMockAuthenticator($type)
    {
        $authenticator = $this->getMockery('Swift_Transport_Esmtp_Authenticator')->shouldIgnoreMissing();
        $authenticator->shouldReceive('getAuthKeyword')
                      ->zeroOrMoreTimes()
                      ->andReturn($type);

        return $authenticator;
    }
}
