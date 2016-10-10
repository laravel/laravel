<?php

class PasswordVerifyTest extends PHPUnit_Framework_TestCase {
    
    public function testFuncExists() {
        $this->assertTrue(function_exists('password_verify'));
    }

    public function testFailedType() {
        $this->assertFalse(password_verify(123, 123));
    }

    public function testSaltOnly() {
        $this->assertFalse(password_verify('foo', '$2a$07$usesomesillystringforsalt$'));
    }

    public function testInvalidPassword() {
        $this->assertFalse(password_verify('rasmusler', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi'));
    }

    public function testValidPassword() {
        $this->assertTrue(password_verify('rasmuslerdorf', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi'));
    }

    public function testInValidHash() {
        $this->assertFalse(password_verify('rasmuslerdorf', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hj'));
    }

}