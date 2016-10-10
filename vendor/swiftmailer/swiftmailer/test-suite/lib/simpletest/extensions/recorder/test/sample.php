<?php
// $Id: sample_test.php 1500 2007-04-29 14:33:31Z pp11 $
require_once dirname(__FILE__) . '/../../../autorun.php';

class SampleTestForRecorder extends UnitTestCase {
    function testTrueIsTrue() {
        $this->assertTrue(true);
    }

    function testFalseIsTrue() {
        $this->assertFalse(true);
    }
}
?>