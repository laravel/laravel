<?php

class PasswordGetInfoTest extends PHPUnit_Framework_TestCase {
    
    public static function provideInfo() {
        return array(
            array('foo', array('algo' => 0, 'algoName' => 'unknown', 'options' => array())),
            array('$2y$', array('algo' => 0, 'algoName' => 'unknown', 'options' => array())),
            array('$2y$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', array('algo' => PASSWORD_BCRYPT, 'algoName' => 'bcrypt', 'options' => array('cost' => 7))),
            array('$2y$10$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', array('algo' => PASSWORD_BCRYPT, 'algoName' => 'bcrypt', 'options' => array('cost' => 10))),

        );
    }

    public function testFuncExists() {
        $this->assertTrue(function_exists('password_get_info'));
    }

    /**
     * @dataProvider provideInfo
     */
    public function testInfo($hash, $info) {
        $this->assertEquals($info, password_get_info($hash));
    }

}