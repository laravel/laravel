<?php
// $Id: collector_test.php 1769 2008-04-19 14:39:00Z pp11 $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../collector.php');
SimpleTest::ignore('MockTestSuite');
Mock::generate('TestSuite');

class PathEqualExpectation extends EqualExpectation {
	function __construct($value, $message = '%s') {
    	parent::__construct(str_replace("\\", '/', $value), $message);
	}

    function test($compare) {
        return parent::test(str_replace("\\", '/', $compare));
    }
}

class TestOfCollector extends UnitTestCase {
    function testCollectionIsAddedToGroup() {
        $suite = new MockTestSuite();
        $suite->expectMinimumCallCount('addFile', 2);
        $suite->expect(
                'addFile',
                array(new PatternExpectation('/collectable\\.(1|2)$/')));
        $collector = new SimpleCollector();
        $collector->collect($suite, dirname(__FILE__) . '/support/collector/');
    }
}

class TestOfPatternCollector extends UnitTestCase {

    function testAddingEverythingToGroup() {
        $suite = new MockTestSuite();
        $suite->expectCallCount('addFile', 2);
        $suite->expect(
                'addFile',
                array(new PatternExpectation('/collectable\\.(1|2)$/')));
        $collector = new SimplePatternCollector('/.*/');
        $collector->collect($suite, dirname(__FILE__) . '/support/collector/');
    }

    function testOnlyMatchedFilesAreAddedToGroup() {
        $suite = new MockTestSuite();
        $suite->expectOnce('addFile', array(new PathEqualExpectation(
        		dirname(__FILE__) . '/support/collector/collectable.1')));
        $collector = new SimplePatternCollector('/1$/');
        $collector->collect($suite, dirname(__FILE__) . '/support/collector/');
    }
}
?>