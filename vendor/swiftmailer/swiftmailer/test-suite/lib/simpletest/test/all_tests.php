<?php
require_once(dirname(__FILE__) . '/../autorun.php');

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('All tests for SimpleTest ' . SimpleTest::getVersion());
        $this->addFile(dirname(__FILE__) . '/unit_tests.php');
        $this->addFile(dirname(__FILE__) . '/shell_test.php');
        $this->addFile(dirname(__FILE__) . '/live_test.php');
        $this->addFile(dirname(__FILE__) . '/acceptance_test.php');
    }
}
?>