<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: detached.php 1784 2008-04-26 13:07:14Z pp11 $
 */

/**#@+
 *  include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/xml.php');
require_once(dirname(__FILE__) . '/shell_tester.php');
/**#@-*/

/**
 *    Runs an XML formated test in a separate process.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class DetachedTestCase {
    private $command;
    private $dry_command;
    private $size;

    /**
     *    Sets the location of the remote test.
     *    @param string $command       Test script.
     *    @param string $dry_command   Script for dry run.
     *    @access public
     */
    function __construct($command, $dry_command = false) {
        $this->command = $command;
        $this->dry_command = $dry_command ? $dry_command : $command;
        $this->size = false;
    }

    /**
     *    Accessor for the test name for subclasses.
     *    @return string       Name of the test.
     *    @access public
     */
    function getLabel() {
        return $this->command;
    }

    /**
     *    Runs the top level test for this class. Currently
     *    reads the data as a single chunk. I'll fix this
     *    once I have added iteration to the browser.
     *    @param SimpleReporter $reporter    Target of test results.
     *    @returns boolean                   True if no failures.
     *    @access public
     */
    function run(&$reporter) {
        $shell = &new SimpleShell();
        $shell->execute($this->command);
        $parser = &$this->createParser($reporter);
        if (! $parser->parse($shell->getOutput())) {
            trigger_error('Cannot parse incoming XML from [' . $this->command . ']');
            return false;
        }
        return true;
    }

    /**
     *    Accessor for the number of subtests.
     *    @return integer       Number of test cases.
     *    @access public
     */
    function getSize() {
        if ($this->size === false) {
            $shell = &new SimpleShell();
            $shell->execute($this->dry_command);
            $reporter = &new SimpleReporter();
            $parser = &$this->createParser($reporter);
            if (! $parser->parse($shell->getOutput())) {
                trigger_error('Cannot parse incoming XML from [' . $this->dry_command . ']');
                return false;
            }
            $this->size = $reporter->getTestCaseCount();
        }
        return $this->size;
    }

    /**
     *    Creates the XML parser.
     *    @param SimpleReporter $reporter    Target of test results.
     *    @return SimpleTestXmlListener      XML reader.
     *    @access protected
     */
    protected function &createParser(&$reporter) {
        return new SimpleTestXmlParser($reporter);
    }
}
?>