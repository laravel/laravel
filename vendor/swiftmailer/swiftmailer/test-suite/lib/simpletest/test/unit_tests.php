<?php
// $Id: unit_tests.php 1748 2008-04-14 01:50:41Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../unit_tester.php');
require_once(dirname(__FILE__) . '/../shell_tester.php');
require_once(dirname(__FILE__) . '/../mock_objects.php');
require_once(dirname(__FILE__) . '/../web_tester.php');
require_once(dirname(__FILE__) . '/../extensions/pear_test_case.php');

class UnitTests extends TestSuite {
    function UnitTests() {
        $this->TestSuite('Unit tests');
        $path = dirname(__FILE__);
        $this->addFile($path . '/errors_test.php');
        $this->addFile($path . '/exceptions_test.php');
        $this->addFile($path . '/autorun_test.php');
        $this->addFile($path . '/compatibility_test.php');
        $this->addFile($path . '/simpletest_test.php');
        $this->addFile($path . '/dumper_test.php');
        $this->addFile($path . '/expectation_test.php');
        $this->addFile($path . '/unit_tester_test.php');
        $this->addFile($path . '/reflection_php5_test.php');
        $this->addFile($path . '/mock_objects_test.php');
        $this->addFile($path . '/interfaces_test.php');
        $this->addFile($path . '/collector_test.php');
        $this->addFile($path . '/adapter_test.php');
        $this->addFile($path . '/socket_test.php');
        $this->addFile($path . '/encoding_test.php');
        $this->addFile($path . '/url_test.php');
        $this->addFile($path . '/cookies_test.php');
        $this->addFile($path . '/http_test.php');
        $this->addFile($path . '/authentication_test.php');
        $this->addFile($path . '/user_agent_test.php');
        $this->addFile($path . '/parser_test.php');
        $this->addFile($path . '/tag_test.php');
        $this->addFile($path . '/form_test.php');
        $this->addFile($path . '/page_test.php');
        $this->addFile($path . '/frames_test.php');
        $this->addFile($path . '/browser_test.php');
        $this->addFile($path . '/web_tester_test.php');
        $this->addFile($path . '/shell_tester_test.php');
        $this->addFile($path . '/xml_test.php');
        $this->addFile($path . '/../extensions/testdox/test.php');
    }
}
?>