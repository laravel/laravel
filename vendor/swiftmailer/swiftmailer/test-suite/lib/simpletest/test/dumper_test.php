<?php
// $Id: dumper_test.php 1505 2007-04-30 23:39:59Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');

class DumperDummy {
}

class TestOfTextFormatting extends UnitTestCase {
    
    function testClipping() {
        $dumper = new SimpleDumper();
        $this->assertEqual(
                $dumper->clipString("Hello", 6),
                "Hello",
                "Hello, 6->%s");
        $this->assertEqual(
                $dumper->clipString("Hello", 5),
                "Hello",
                "Hello, 5->%s");
        $this->assertEqual(
                $dumper->clipString("Hello world", 3),
                "Hel...",
                "Hello world, 3->%s");
        $this->assertEqual(
                $dumper->clipString("Hello world", 6, 3),
                "Hello ...",
                "Hello world, 6, 3->%s");
        $this->assertEqual(
                $dumper->clipString("Hello world", 3, 6),
                "...o w...",
                "Hello world, 3, 6->%s");
        $this->assertEqual(
                $dumper->clipString("Hello world", 4, 11),
                "...orld",
                "Hello world, 4, 11->%s");
        $this->assertEqual(
                $dumper->clipString("Hello world", 4, 12),
                "...orld",
                "Hello world, 4, 12->%s");
    }
    
    function testDescribeNull() {
        $dumper = new SimpleDumper();
        $this->assertPattern('/null/i', $dumper->describeValue(null));
    }
    
    function testDescribeBoolean() {
        $dumper = new SimpleDumper();
        $this->assertPattern('/boolean/i', $dumper->describeValue(true));
        $this->assertPattern('/true/i', $dumper->describeValue(true));
        $this->assertPattern('/false/i', $dumper->describeValue(false));
    }
    
    function testDescribeString() {
        $dumper = new SimpleDumper();
        $this->assertPattern('/string/i', $dumper->describeValue('Hello'));
        $this->assertPattern('/Hello/', $dumper->describeValue('Hello'));
    }
    
    function testDescribeInteger() {
        $dumper = new SimpleDumper();
        $this->assertPattern('/integer/i', $dumper->describeValue(35));
        $this->assertPattern('/35/', $dumper->describeValue(35));
    }
    
    function testDescribeFloat() {
        $dumper = new SimpleDumper();
        $this->assertPattern('/float/i', $dumper->describeValue(0.99));
        $this->assertPattern('/0\.99/', $dumper->describeValue(0.99));
    }
    
    function testDescribeArray() {
        $dumper = new SimpleDumper();
        $this->assertPattern('/array/i', $dumper->describeValue(array(1, 4)));
        $this->assertPattern('/2/i', $dumper->describeValue(array(1, 4)));
    }
    
    function testDescribeObject() {
        $dumper = new SimpleDumper();
        $this->assertPattern(
                '/object/i',
                $dumper->describeValue(new DumperDummy()));
        $this->assertPattern(
                '/DumperDummy/i',
                $dumper->describeValue(new DumperDummy()));
    }
}
?>