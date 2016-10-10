<?php
/**
 *	@package	SimpleTest
 *	@subpackage	Extensions
 *	@version	$Id: junit_xml_reporter.php 1802 2008-09-08 10:43:58Z maetl_ $
 *  @author Patrice Neff - mailinglists@patrice.ch (original code)
 */

require_once dirname(__FILE__).'/../reporter.php';

/**
 * Reporter which outputs test results in a format compatible
 * with JUnit / Maven XML output. Can be used for integrating
 * test suite with continuous integration servers such as
 * Atlassian Bamboo.
 */
class JUnitXMLReporter extends SimpleReporter {
    function JUnitXMLReporter() {
        $this->SimpleReporter();

        $this->doc = new DOMDocument();
        $this->doc->loadXML('<testsuite/>');
        $this->root = $this->doc->documentElement;
    }

    function paintHeader($test_name) {
        $this->testsStart = microtime(true);
        
        $this->root->setAttribute('name', $test_name);
        $this->root->setAttribute('timestamp', date('c'));
        $this->root->setAttribute('hostname', 'localhost');
        
        echo "<?xml version=\"1.0\"?>\n";
        echo "<!-- starting test suite $test_name\n";
    }

    /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        echo "-->\n";
        
        $duration = microtime(true) - $this->testsStart;

        $this->root->setAttribute('tests', $this->getPassCount() + $this->getFailCount() + $this->getExceptionCount());
        $this->root->setAttribute('failures', $this->getFailCount());
        $this->root->setAttribute('errors', $this->getExceptionCount());
        $this->root->setAttribute('time', $duration);
        
        $this->doc->formatOutput = true;
        $xml = $this->doc->saveXML();
        // Cut out XML declaration
        echo preg_replace('/<\?[^>]*\?>/', "", $xml);
        echo "\n";
    }
    
    function paintCaseStart($case) {
        echo "- case start $case\n";
        $this->currentCaseName = $case;
    }

    function paintCaseEnd($case) {
        // No output here
    }
    
    function paintMethodStart($test) {
        echo "  - test start: $test\n";
        
        $this->methodStart = microtime(true);
        $this->currCase = $this->doc->createElement('testcase');
    }
    
    function paintMethodEnd($test) {
        $duration = microtime(true) - $this->methodStart;
        
        $this->currCase->setAttribute('name', $test);
        $this->currCase->setAttribute('classname', $this->currentCaseName);
        $this->currCase->setAttribute('time', $duration);
        $this->root->appendChild($this->currCase);
    }
    
    function paintFail($message) {
        parent::paintFail($message);

        error_log("Failure: " . $message);
        $this->terminateAbnormally($message);
    }
    
    function paintException($exception) {
        parent::paintException($exception);
        
        error_log("Exception: " . $exception);
        $this->terminateAbnormally($exception);
    }
    
    function terminateAbnormally($message) {
        if (!$this->currCase) {
            error_log("!! currCase was not set.");
            return;
        }

        $ch = $this->doc->createElement('failure');
        $breadcrumb = $this->getTestList();
        $ch->setAttribute('message', $breadcrumb[count($breadcrumb)-1]);
        $ch->setAttribute('type', $breadcrumb[count($breadcrumb)-1]);
        
        $message = implode(' -> ', $breadcrumb) . "\n\n\n" . $message;
        $content = $this->doc->createTextNode($message);
        $ch->appendChild($content);
        
        $this->currCase->appendChild($ch);
    }
}
?>

