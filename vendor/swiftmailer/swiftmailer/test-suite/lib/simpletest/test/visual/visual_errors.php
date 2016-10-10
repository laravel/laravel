<?php
require_once('../../shell_tester.php');
require_once('../../mock_objects.php');
require_once('../../xml.php');
require_once('../../autorun.php');

class VisualTestOfErrors extends UnitTestCase {

    function testErrorDisplay() {
        $this->dump('Four exceptions...');
        trigger_error('Default');
        trigger_error('Error', E_USER_ERROR);
        trigger_error('Warning', E_USER_WARNING);
        trigger_error('Notice', E_USER_NOTICE);
    }

    function testErrorTrap() {
        $this->dump('Pass...');
        $this->expectError();
        trigger_error('Error');
    }
    
    function testUnusedErrorExpectationsCauseFailures() {
        $this->dump('Two failures...');
        $this->expectError('Some error');
        $this->expectError();
    }

    function testErrorTextIsSentImmediately() {
        $this->dump('One failure...');
        $this->expectError('Error');
        trigger_error('Error almost');
        $this->dump('This should lie between the two errors');
        trigger_error('Error after');
    }
}

class VisualTestOfExceptions extends UnitTestCase {
    function skip() {
        $this->skipUnless(version_compare(phpversion(), '5') >= 0);
    }

    function testExceptionTrap() {
        $this->dump('One exception...');
        $this->ouch();
        $this->fail('Should not be here');
    }

    function testExceptionExpectationShowsErrorWhenNoException() {
        $this->dump('One failure...');
        $this->expectException('SomeException');
        $this->expectException('LaterException');
    }

    function testExceptionExpectationShowsPassWhenException() {
        $this->dump('Pass...');
        $this->expectException();
        $this->ouch();
    }

    function ouch() {
        eval('throw new Exception("Ouch!");');
    }
}
?>