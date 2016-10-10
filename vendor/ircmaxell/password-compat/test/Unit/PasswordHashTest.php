<?php

class PasswordHashTest extends PHPUnit_Framework_TestCase {
    
    public function testFuncExists() {
        $this->assertTrue(function_exists('password_hash'));
    }

    public function testStringLength() {
        $this->assertEquals(60, strlen(password_hash('foo', PASSWORD_BCRYPT)));
    }

    public function testHash() {
        $hash = password_hash('foo', PASSWORD_BCRYPT);
        $this->assertEquals($hash, crypt('foo', $hash));
    }

    public function testKnownSalt() {
        $hash = password_hash("rasmuslerdorf", PASSWORD_BCRYPT, array("cost" => 7, "salt" => "usesomesillystringforsalt"));
        $this->assertEquals('$2y$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', $hash);
    }

    public function testRawSalt() {
        $hash = password_hash("test", PASSWORD_BCRYPT, array("salt" => "123456789012345678901" . chr(0)));
        $this->assertEquals('$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y', $hash);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidAlgo() {
        password_hash('foo', array());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidAlgo2() {
        password_hash('foo', 2);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidPassword() {
        password_hash(array(), 1);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidSalt() {
        password_hash('foo', PASSWORD_BCRYPT, array('salt' => array()));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidBcryptCostLow() {
        password_hash('foo', PASSWORD_BCRYPT, array('cost' => 3));
    }
        
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidBcryptCostHigh() {
        password_hash('foo', PASSWORD_BCRYPT, array('cost' => 32));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidBcryptCostInvalid() {
        password_hash('foo', PASSWORD_BCRYPT, array('cost' => 'foo'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidBcryptSaltShort() {
        password_hash('foo', PASSWORD_BCRYPT, array('salt' => 'abc'));
    }

}