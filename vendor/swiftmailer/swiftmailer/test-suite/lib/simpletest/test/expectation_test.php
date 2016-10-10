<?php
// $Id: expectation_test.php 1788 2008-04-27 11:01:59Z pp11 $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../expectation.php');

class TestOfEquality extends UnitTestCase {

    function testBoolean() {
        $is_true = new EqualExpectation(true);
        $this->assertTrue($is_true->test(true));
        $this->assertFalse($is_true->test(false));
    }

    function testStringMatch() {
        $hello = new EqualExpectation("Hello");
        $this->assertTrue($hello->test("Hello"));
        $this->assertFalse($hello->test("Goodbye"));
    }

    function testInteger() {
        $fifteen = new EqualExpectation(15);
        $this->assertTrue($fifteen->test(15));
        $this->assertFalse($fifteen->test(14));
    }

    function testFloat() {
        $pi = new EqualExpectation(3.14);
        $this->assertTrue($pi->test(3.14));
        $this->assertFalse($pi->test(3.15));
    }

    function testArray() {
        $colours = new EqualExpectation(array("r", "g", "b"));
        $this->assertTrue($colours->test(array("r", "g", "b")));
        $this->assertFalse($colours->test(array("g", "b", "r")));
    }

    function testHash() {
        $is_blue = new EqualExpectation(array("r" => 0, "g" => 0, "b" => 255));
        $this->assertTrue($is_blue->test(array("r" => 0, "g" => 0, "b" => 255)));
        $this->assertFalse($is_blue->test(array("r" => 0, "g" => 255, "b" => 0)));
    }

    function testHashWithOutOfOrderKeysShouldStillMatch() {
        $any_order = new EqualExpectation(array('a' => 1, 'b' => 2));
        $this->assertTrue($any_order->test(array('b' => 2, 'a' => 1)));
    }
}

class TestOfWithin extends UnitTestCase {

    function testWithinFloatingPointMargin() {
        $within = new WithinMarginExpectation(1.0, 0.2);
        $this->assertFalse($within->test(0.7));
        $this->assertTrue($within->test(0.8));
        $this->assertTrue($within->test(0.9));
        $this->assertTrue($within->test(1.1));
        $this->assertTrue($within->test(1.2));
        $this->assertFalse($within->test(1.3));
    }

    function testOutsideFloatingPointMargin() {
        $within = new OutsideMarginExpectation(1.0, 0.2);
        $this->assertTrue($within->test(0.7));
        $this->assertFalse($within->test(0.8));
        $this->assertFalse($within->test(1.2));
        $this->assertTrue($within->test(1.3));
    }
}

class TestOfInequality extends UnitTestCase {

    function testStringMismatch() {
        $not_hello = new NotEqualExpectation("Hello");
        $this->assertTrue($not_hello->test("Goodbye"));
        $this->assertFalse($not_hello->test("Hello"));
    }
}

class RecursiveNasty {
    private $me;

    function RecursiveNasty() {
        $this->me = $this;
    }
}

class TestOfIdentity extends UnitTestCase {

    function testType() {
        $string = new IdenticalExpectation("37");
        $this->assertTrue($string->test("37"));
        $this->assertFalse($string->test(37));
        $this->assertFalse($string->test("38"));
    }

    function _testNastyPhp5Bug() {
        $this->assertFalse(new RecursiveNasty() != new RecursiveNasty());
    }

    function _testReallyHorribleRecursiveStructure() {
        $hopeful = new IdenticalExpectation(new RecursiveNasty());
        $this->assertTrue($hopeful->test(new RecursiveNasty()));
    }
}

class DummyReferencedObject{}

class TestOfReference extends UnitTestCase {

    function testReference() {
        $foo = "foo";
        $ref = &$foo;
        $not_ref = $foo;
        $bar = "bar";

        $expect = new ReferenceExpectation($foo);
        $this->assertTrue($expect->test($ref));
        $this->assertFalse($expect->test($not_ref));
        $this->assertFalse($expect->test($bar));
    }
}

class TestOfNonIdentity extends UnitTestCase {

    function testType() {
        $string = new NotIdenticalExpectation("37");
        $this->assertTrue($string->test("38"));
        $this->assertTrue($string->test(37));
        $this->assertFalse($string->test("37"));
    }
}

class TestOfPatterns extends UnitTestCase {

    function testWanted() {
        $pattern = new PatternExpectation('/hello/i');
        $this->assertTrue($pattern->test("Hello world"));
        $this->assertFalse($pattern->test("Goodbye world"));
    }

    function testUnwanted() {
        $pattern = new NoPatternExpectation('/hello/i');
        $this->assertFalse($pattern->test("Hello world"));
        $this->assertTrue($pattern->test("Goodbye world"));
    }
}

class ExpectedMethodTarget {
    function hasThisMethod() {}
}

class TestOfMethodExistence extends UnitTestCase {

    function testHasMethod() {
        $instance = new ExpectedMethodTarget();
        $expectation = new MethodExistsExpectation('hasThisMethod');
        $this->assertTrue($expectation->test($instance));
        $expectation = new MethodExistsExpectation('doesNotHaveThisMethod');
        $this->assertFalse($expectation->test($instance));
    }
}

class TestOfIsA extends UnitTestCase {

    function testString() {
        $expectation = new IsAExpectation('string');
        $this->assertTrue($expectation->test('Hello'));
        $this->assertFalse($expectation->test(5));
    }

    function testBoolean() {
        $expectation = new IsAExpectation('boolean');
        $this->assertTrue($expectation->test(true));
        $this->assertFalse($expectation->test(1));
    }

    function testBool() {
        $expectation = new IsAExpectation('bool');
        $this->assertTrue($expectation->test(true));
        $this->assertFalse($expectation->test(1));
    }

    function testDouble() {
        $expectation = new IsAExpectation('double');
        $this->assertTrue($expectation->test(5.0));
        $this->assertFalse($expectation->test(5));
    }

    function testFloat() {
        $expectation = new IsAExpectation('float');
        $this->assertTrue($expectation->test(5.0));
        $this->assertFalse($expectation->test(5));
    }

    function testReal() {
        $expectation = new IsAExpectation('real');
        $this->assertTrue($expectation->test(5.0));
        $this->assertFalse($expectation->test(5));
    }

    function testInteger() {
        $expectation = new IsAExpectation('integer');
        $this->assertTrue($expectation->test(5));
        $this->assertFalse($expectation->test(5.0));
    }

    function testInt() {
        $expectation = new IsAExpectation('int');
        $this->assertTrue($expectation->test(5));
        $this->assertFalse($expectation->test(5.0));
    }
}

class TestOfNotA extends UnitTestCase {

    function testString() {
        $expectation = new NotAExpectation('string');
        $this->assertFalse($expectation->test('Hello'));
        $this->assertTrue($expectation->test(5));
    }
}
?>