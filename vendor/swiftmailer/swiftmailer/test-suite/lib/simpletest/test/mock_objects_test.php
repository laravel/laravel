<?php
// $Id: mock_objects_test.php 1801 2008-09-01 23:22:06Z tswicegood $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../expectation.php');
require_once(dirname(__FILE__) . '/../mock_objects.php');

class TestOfAnythingExpectation extends UnitTestCase {
    function testSimpleInteger() {
        $expectation = new AnythingExpectation();
        $this->assertTrue($expectation->test(33));
        $this->assertTrue($expectation->test(false));
        $this->assertTrue($expectation->test(null));
    }
}

class TestOfParametersExpectation extends UnitTestCase {

    function testEmptyMatch() {
        $expectation = new ParametersExpectation(array());
        $this->assertTrue($expectation->test(array()));
        $this->assertFalse($expectation->test(array(33)));
    }

    function testSingleMatch() {
        $expectation = new ParametersExpectation(array(0));
        $this->assertFalse($expectation->test(array(1)));
        $this->assertTrue($expectation->test(array(0)));
    }

    function testAnyMatch() {
        $expectation = new ParametersExpectation(false);
        $this->assertTrue($expectation->test(array()));
        $this->assertTrue($expectation->test(array(1, 2)));
    }

    function testMissingParameter() {
        $expectation = new ParametersExpectation(array(0));
        $this->assertFalse($expectation->test(array()));
    }

    function testNullParameter() {
        $expectation = new ParametersExpectation(array(null));
        $this->assertTrue($expectation->test(array(null)));
        $this->assertFalse($expectation->test(array()));
    }

    function testAnythingExpectations() {
        $expectation = new ParametersExpectation(array(new AnythingExpectation()));
        $this->assertFalse($expectation->test(array()));
        $this->assertIdentical($expectation->test(array(null)), true);
        $this->assertIdentical($expectation->test(array(13)), true);
    }

    function testOtherExpectations() {
        $expectation = new ParametersExpectation(
                array(new PatternExpectation('/hello/i')));
        $this->assertFalse($expectation->test(array('Goodbye')));
        $this->assertTrue($expectation->test(array('hello')));
        $this->assertTrue($expectation->test(array('Hello')));
    }

    function testIdentityOnly() {
        $expectation = new ParametersExpectation(array("0"));
        $this->assertFalse($expectation->test(array(0)));
        $this->assertTrue($expectation->test(array("0")));
    }

    function testLongList() {
        $expectation = new ParametersExpectation(
                array("0", 0, new AnythingExpectation(), false));
        $this->assertTrue($expectation->test(array("0", 0, 37, false)));
        $this->assertFalse($expectation->test(array("0", 0, 37, true)));
        $this->assertFalse($expectation->test(array("0", 0, 37)));
    }
}

class TestOfSimpleSignatureMap extends UnitTestCase {

    function testEmpty() {
        $map = new SimpleSignatureMap();
        $this->assertFalse($map->isMatch("any", array()));
        $this->assertNull($map->findFirstAction("any", array()));
    }
    
    function testDifferentCallSignaturesCanHaveDifferentReferences() {
        $map = new SimpleSignatureMap();
        $fred = 'Fred';
        $jim = 'jim';
        $map->add(array(0), $fred);
        $map->add(array('0'), $jim);
        $this->assertSame($fred, $map->findFirstAction(array(0)));
        $this->assertSame($jim, $map->findFirstAction(array('0')));
    }

    function testWildcard() {
        $fred = 'Fred';
        $map = new SimpleSignatureMap();
        $map->add(array(new AnythingExpectation(), 1, 3), $fred);
        $this->assertTrue($map->isMatch(array(2, 1, 3)));
        $this->assertSame($map->findFirstAction(array(2, 1, 3)), $fred);
    }

    function testAllWildcard() {
        $fred = 'Fred';
        $map = new SimpleSignatureMap();
        $this->assertFalse($map->isMatch(array(2, 1, 3)));
        $map->add('', $fred);
        $this->assertTrue($map->isMatch(array(2, 1, 3)));
        $this->assertSame($map->findFirstAction(array(2, 1, 3)), $fred);
    }

    function testOrdering() {
        $map = new SimpleSignatureMap();
        $map->add(array(1, 2), new SimpleByValue("1, 2"));
        $map->add(array(1, 3), new SimpleByValue("1, 3"));
        $map->add(array(1), new SimpleByValue("1"));
        $map->add(array(1, 4), new SimpleByValue("1, 4"));
        $map->add(array(new AnythingExpectation()), new SimpleByValue("Any"));
        $map->add(array(2), new SimpleByValue("2"));
        $map->add("", new SimpleByValue("Default"));
        $map->add(array(), new SimpleByValue("None"));
        $this->assertEqual($map->findFirstAction(array(1, 2)), new SimpleByValue("1, 2"));
        $this->assertEqual($map->findFirstAction(array(1, 3)), new SimpleByValue("1, 3"));
        $this->assertEqual($map->findFirstAction(array(1, 4)), new SimpleByValue("1, 4"));
        $this->assertEqual($map->findFirstAction(array(1)), new SimpleByValue("1"));
        $this->assertEqual($map->findFirstAction(array(2)), new SimpleByValue("Any"));
        $this->assertEqual($map->findFirstAction(array(3)), new SimpleByValue("Any"));
        $this->assertEqual($map->findFirstAction(array()), new SimpleByValue("Default"));
    }
}

class TestOfCallSchedule extends UnitTestCase {
    function testCanBeSetToAlwaysReturnTheSameReference() {
        $a = 5;
        $schedule = new SimpleCallSchedule();
        $schedule->register('aMethod', false, new SimpleByReference($a));
        $this->assertReference($schedule->respond(0, 'aMethod', array()), $a);
        $this->assertReference($schedule->respond(1, 'aMethod', array()), $a);
    }

    function testSpecificSignaturesOverrideTheAlwaysCase() {
        $any = 'any';
        $one = 'two';
        $schedule = new SimpleCallSchedule();
        $schedule->register('aMethod', array(1), new SimpleByReference($one));
        $schedule->register('aMethod', false, new SimpleByReference($any));
        $this->assertReference($schedule->respond(0, 'aMethod', array(2)), $any);
        $this->assertReference($schedule->respond(0, 'aMethod', array(1)), $one);
    }
    
    function testReturnsCanBeSetOverTime() {
        $one = 'one';
        $two = 'two';
        $schedule = new SimpleCallSchedule();
        $schedule->registerAt(0, 'aMethod', false, new SimpleByReference($one));
        $schedule->registerAt(1, 'aMethod', false, new SimpleByReference($two));
        $this->assertReference($schedule->respond(0, 'aMethod', array()), $one);
        $this->assertReference($schedule->respond(1, 'aMethod', array()), $two);
    }
    
    function testReturnsOverTimecanBeAlteredByTheArguments() {
        $one = '1';
        $two = '2';
        $two_a = '2a';
        $schedule = new SimpleCallSchedule();
        $schedule->registerAt(0, 'aMethod', false, new SimpleByReference($one));
        $schedule->registerAt(1, 'aMethod', array('a'), new SimpleByReference($two_a));
        $schedule->registerAt(1, 'aMethod', false, new SimpleByReference($two));
        $this->assertReference($schedule->respond(0, 'aMethod', array()), $one);
        $this->assertReference($schedule->respond(1, 'aMethod', array()), $two);
        $this->assertReference($schedule->respond(1, 'aMethod', array('a')), $two_a);
    }
    
    function testCanReturnByValue() {
        $a = 5;
        $schedule = new SimpleCallSchedule();
        $schedule->register('aMethod', false, new SimpleByValue($a));
        $this->assertCopy($schedule->respond(0, 'aMethod', array()), $a);
    }
    
    function testCanThrowException() {
        if (version_compare(phpversion(), '5', '>=')) {
            $schedule = new SimpleCallSchedule();
            $schedule->register('aMethod', false, new SimpleThrower(new Exception('Ouch')));
            $this->expectException(new Exception('Ouch'));
            $schedule->respond(0, 'aMethod', array());
        }
    }
    
    function testCanEmitError() {
        $schedule = new SimpleCallSchedule();
        $schedule->register('aMethod', false, new SimpleErrorThrower('Ouch', E_USER_WARNING));
        $this->expectError('Ouch');
        $schedule->respond(0, 'aMethod', array());
    }
}

class Dummy {
    function Dummy() {
    }

    function aMethod() {
        return true;
    }

    function &aReferenceMethod() {
        return true;
    }

    function anotherMethod() {
        return true;
    }
}
Mock::generate('Dummy');
Mock::generate('Dummy', 'AnotherMockDummy');
Mock::generate('Dummy', 'MockDummyWithExtraMethods', array('extraMethod'));

class TestOfMockGeneration extends UnitTestCase {

    function testCloning() {
        $mock = new MockDummy();
        $this->assertTrue(method_exists($mock, "aMethod"));
        $this->assertNull($mock->aMethod());
    }

    function testCloningWithExtraMethod() {
        $mock = new MockDummyWithExtraMethods();
        $this->assertTrue(method_exists($mock, "extraMethod"));
    }

    function testCloningWithChosenClassName() {
        $mock = new AnotherMockDummy();
        $this->assertTrue(method_exists($mock, "aMethod"));
    }
}

class TestOfMockReturns extends UnitTestCase {

    function testDefaultReturn() {
        $mock = new MockDummy();
        $mock->setReturnValue("aMethod", "aaa");
        $this->assertIdentical($mock->aMethod(), "aaa");
        $this->assertIdentical($mock->aMethod(), "aaa");
    }

    function testParameteredReturn() {
        $mock = new MockDummy();
        $mock->setReturnValue('aMethod', 'aaa', array(1, 2, 3));
        $this->assertNull($mock->aMethod());
        $this->assertIdentical($mock->aMethod(1, 2, 3), 'aaa');
    }

    function testSetReturnGivesObjectReference() {
        $mock = new MockDummy();
        $object = new Dummy();
        $mock->returns('aMethod', $object, array(1, 2, 3));
        $this->assertSame($mock->aMethod(1, 2, 3), $object);
    }

    function testSetReturnReferenceGivesOriginalReference() {
        $mock = new MockDummy();
        $object = 1;
        $mock->setReturnReference('aReferenceMethod', $object, array(1, 2, 3));
        $this->assertReference($mock->aReferenceMethod(1, 2, 3), $object);
    }

    function testPatternMatchReturn() {
        $mock = new MockDummy();
        $mock->setReturnValue(
                "aMethod",
                "aaa",
                array(new PatternExpectation('/hello/i')));
        $this->assertIdentical($mock->aMethod('Hello'), "aaa");
        $this->assertNull($mock->aMethod('Goodbye'));
    }

    function testMultipleMethods() {
        $mock = new MockDummy();
        $mock->setReturnValue("aMethod", 100, array(1));
        $mock->setReturnValue("aMethod", 200, array(2));
        $mock->setReturnValue("anotherMethod", 10, array(1));
        $mock->setReturnValue("anotherMethod", 20, array(2));
        $this->assertIdentical($mock->aMethod(1), 100);
        $this->assertIdentical($mock->anotherMethod(1), 10);
        $this->assertIdentical($mock->aMethod(2), 200);
        $this->assertIdentical($mock->anotherMethod(2), 20);
    }

    function testReturnSequence() {
        $mock = new MockDummy();
        $mock->setReturnValueAt(0, "aMethod", "aaa");
        $mock->setReturnValueAt(1, "aMethod", "bbb");
        $mock->setReturnValueAt(3, "aMethod", "ddd");
        $this->assertIdentical($mock->aMethod(), "aaa");
        $this->assertIdentical($mock->aMethod(), "bbb");
        $this->assertNull($mock->aMethod());
        $this->assertIdentical($mock->aMethod(), "ddd");
    }

    function testSetReturnReferenceAtGivesOriginal() {
        $mock = new MockDummy();
        $object = 100;
        $mock->setReturnReferenceAt(1, "aReferenceMethod", $object);
        $this->assertNull($mock->aReferenceMethod());
        $this->assertReference($mock->aReferenceMethod(), $object);
        $this->assertNull($mock->aReferenceMethod());
    }

    function testReturnsAtGivesOriginalObjectHandle() {
        $mock = new MockDummy();
        $object = new Dummy();
        $mock->returnsAt(1, "aMethod", $object);
        $this->assertNull($mock->aMethod());
        $this->assertSame($mock->aMethod(), $object);
        $this->assertNull($mock->aMethod());
    }

    function testComplicatedReturnSequence() {
        $mock = new MockDummy();
        $object = new Dummy();
        $mock->returnsAt(1, "aMethod", "aaa", array("a"));
        $mock->returnsAt(1, "aMethod", "bbb");
        $mock->returnsAt(2, "aMethod", $object, array('*', 2));
        $mock->returnsAt(2, "aMethod", "value", array('*', 3));
        $mock->returns("aMethod", 3, array(3));
        $this->assertNull($mock->aMethod());
        $this->assertEqual($mock->aMethod("a"), "aaa");
        $this->assertSame($mock->aMethod(1, 2), $object);
        $this->assertEqual($mock->aMethod(3), 3);
        $this->assertNull($mock->aMethod());
    }

    function testMultipleMethodSequences() {
        $mock = new MockDummy();
        $mock->setReturnValueAt(0, "aMethod", "aaa");
        $mock->setReturnValueAt(1, "aMethod", "bbb");
        $mock->setReturnValueAt(0, "anotherMethod", "ccc");
        $mock->setReturnValueAt(1, "anotherMethod", "ddd");
        $this->assertIdentical($mock->aMethod(), "aaa");
        $this->assertIdentical($mock->anotherMethod(), "ccc");
        $this->assertIdentical($mock->aMethod(), "bbb");
        $this->assertIdentical($mock->anotherMethod(), "ddd");
    }

    function testSequenceFallback() {
        $mock = new MockDummy();
        $mock->setReturnValueAt(0, "aMethod", "aaa", array('a'));
        $mock->setReturnValueAt(1, "aMethod", "bbb", array('a'));
        $mock->setReturnValue("aMethod", "AAA");
        $this->assertIdentical($mock->aMethod('a'), "aaa");
        $this->assertIdentical($mock->aMethod('b'), "AAA");
    }

    function testMethodInterference() {
        $mock = new MockDummy();
        $mock->setReturnValueAt(0, "anotherMethod", "aaa");
        $mock->setReturnValue("aMethod", "AAA");
        $this->assertIdentical($mock->aMethod(), "AAA");
        $this->assertIdentical($mock->anotherMethod(), "aaa");
    }
}

class TestOfMockExpectationsThatPass extends UnitTestCase {

    function testAnyArgument() {
        $mock = new MockDummy();
        $mock->expect('aMethod', array('*'));
        $mock->aMethod(1);
        $mock->aMethod('hello');
    }

    function testAnyTwoArguments() {
        $mock = new MockDummy();
        $mock->expect('aMethod', array('*', '*'));
        $mock->aMethod(1, 2);
    }

    function testSpecificArgument() {
        $mock = new MockDummy();
        $mock->expect('aMethod', array(1));
        $mock->aMethod(1);
    }

    function testExpectation() {
        $mock = new MockDummy();
        $mock->expect('aMethod', array(new IsAExpectation('Dummy')));
        $mock->aMethod(new Dummy());
    }

    function testArgumentsInSequence() {
        $mock = new MockDummy();
        $mock->expectAt(0, 'aMethod', array(1, 2));
        $mock->expectAt(1, 'aMethod', array(3, 4));
        $mock->aMethod(1, 2);
        $mock->aMethod(3, 4);
    }

    function testAtLeastOnceSatisfiedByOneCall() {
        $mock = new MockDummy();
        $mock->expectAtLeastOnce('aMethod');
        $mock->aMethod();
    }

    function testAtLeastOnceSatisfiedByTwoCalls() {
        $mock = new MockDummy();
        $mock->expectAtLeastOnce('aMethod');
        $mock->aMethod();
        $mock->aMethod();
    }

    function testOnceSatisfiedByOneCall() {
        $mock = new MockDummy();
        $mock->expectOnce('aMethod');
        $mock->aMethod();
    }

    function testMinimumCallsSatisfiedByEnoughCalls() {
        $mock = new MockDummy();
        $mock->expectMinimumCallCount('aMethod', 1);
        $mock->aMethod();
    }

    function testMinimumCallsSatisfiedByTooManyCalls() {
        $mock = new MockDummy();
        $mock->expectMinimumCallCount('aMethod', 3);
        $mock->aMethod();
        $mock->aMethod();
        $mock->aMethod();
        $mock->aMethod();
    }

    function testMaximumCallsSatisfiedByEnoughCalls() {
        $mock = new MockDummy();
        $mock->expectMaximumCallCount('aMethod', 1);
        $mock->aMethod();
    }

    function testMaximumCallsSatisfiedByNoCalls() {
        $mock = new MockDummy();
        $mock->expectMaximumCallCount('aMethod', 1);
    }
}

class MockWithInjectedTestCase extends SimpleMock {
    protected function getCurrentTestCase() {
        return SimpleTest::getContext()->getTest()->getMockedTest();
    }
}
SimpleTest::setMockBaseClass('MockWithInjectedTestCase');
Mock::generate('Dummy', 'MockDummyWithInjectedTestCase');
SimpleTest::setMockBaseClass('SimpleMock');
Mock::generate('SimpleTestCase');

class LikeExpectation extends IdenticalExpectation {
    function __construct($expectation) {
        $expectation->message = '';
        parent::__construct($expectation);
    }

    function test($compare) {
        $compare->message = '';
        return parent::test($compare);
    }

    function testMessage($compare) {
        $compare->message = '';
        return parent::testMessage($compare);
    }
}

class TestOfMockExpectations extends UnitTestCase {
    private $test;

    function setUp() {
        $this->test = new MockSimpleTestCase();
    }

    function getMockedTest() {
        return $this->test;
    }

    function testSettingExpectationOnNonMethodThrowsError() {
        $mock = new MockDummyWithInjectedTestCase();
        $this->expectError();
        $mock->expectMaximumCallCount('aMissingMethod', 2);
    }

    function testMaxCallsDetectsOverrun() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new MaximumCallCountExpectation('aMethod', 2)),
                3));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectMaximumCallCount('aMethod', 2);
        $mock->aMethod();
        $mock->aMethod();
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testTallyOnMaxCallsSendsPassOnUnderrun() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new MaximumCallCountExpectation('aMethod', 2)),
                2));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectMaximumCallCount("aMethod", 2);
        $mock->aMethod();
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testExpectNeverDetectsOverrun() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new MaximumCallCountExpectation('aMethod', 0)),
                1));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectNever('aMethod');
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testTallyOnExpectNeverStillSendsPassOnUnderrun() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new MaximumCallCountExpectation('aMethod', 0)),
                0));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectNever('aMethod');
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testMinCalls() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new MinimumCallCountExpectation('aMethod', 2)),
                2));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectMinimumCallCount('aMethod', 2);
        $mock->aMethod();
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testFailedNever() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new MaximumCallCountExpectation('aMethod', 0)),
                1));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectNever('aMethod');
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testUnderOnce() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new CallCountExpectation('aMethod', 1)),
                0));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectOnce('aMethod');
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testOverOnce() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new CallCountExpectation('aMethod', 1)),
                2));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectOnce('aMethod');
        $mock->aMethod();
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testUnderAtLeastOnce() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new MinimumCallCountExpectation('aMethod', 1)),
                0));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectAtLeastOnce("aMethod");
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testZeroArguments() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new ParametersExpectation(array())),
                array(),
                '*'));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expect("aMethod", array());
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testExpectedArguments() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new ParametersExpectation(array(1, 2, 3))),
                array(1, 2, 3),
                '*'));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expect('aMethod', array(1, 2, 3));
        $mock->aMethod(1, 2, 3);
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testFailedArguments() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new ParametersExpectation(array('this'))),
                array('that'),
                '*'));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expect('aMethod', array('this'));
        $mock->aMethod('that');
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testWildcardsAreTranslatedToAnythingExpectations() {
        $this->test->expectOnce('assert', array(
                new LikeExpectation(new ParametersExpectation(array(
                            new AnythingExpectation(), 123, new AnythingExpectation()))),
                array(100, 123, 101),
                '*'));
        $mock = new MockDummyWithInjectedTestCase($this);
        $mock->expect("aMethod", array('*', 123, '*'));
        $mock->aMethod(100, 123, 101);
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testSpecificPassingSequence() {
        $this->test->expectAt(0, 'assert', array(
                new LikeExpectation(new ParametersExpectation(array(1, 2, 3))),
                array(1, 2, 3),
                '*'));
        $this->test->expectAt(1, 'assert', array(
                new LikeExpectation(new ParametersExpectation(array('Hello'))),
                array('Hello'),
                '*'));
        $mock = new MockDummyWithInjectedTestCase();
        $mock->expectAt(1, 'aMethod', array(1, 2, 3));
        $mock->expectAt(2, 'aMethod', array('Hello'));
        $mock->aMethod();
        $mock->aMethod(1, 2, 3);
        $mock->aMethod('Hello');
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
    }

    function testNonArrayForExpectedParametersGivesError() {
        $mock = new MockDummyWithInjectedTestCase();
        $this->expectError(new PatternExpectation('/\$args.*not an array/i'));
        $mock->expect("aMethod", "foo");
        $mock->aMethod();
        $mock->mock->atTestEnd('testSomething', $this->test);
   }
}

class TestOfMockComparisons extends UnitTestCase {

    function testEqualComparisonOfMocksDoesNotCrash() {
        $expectation = new EqualExpectation(new MockDummy());
        $this->assertTrue($expectation->test(new MockDummy(), true));
    }

    function testIdenticalComparisonOfMocksDoesNotCrash() {
        $expectation = new IdenticalExpectation(new MockDummy());
        $this->assertTrue($expectation->test(new MockDummy()));
    }
}

class ClassWithSpecialMethods {
    function __get($name) { }
    function __set($name, $value) { }
    function __isset($name) { }
    function __unset($name) { }
    function __call($method, $arguments) { }
    function __toString() { }
}
Mock::generate('ClassWithSpecialMethods');

class TestOfSpecialMethodsAfterPHP51 extends UnitTestCase {
    
    function skip() {
        $this->skipIf(version_compare(phpversion(), '5.1', '<'), '__isset and __unset overloading not tested unless PHP 5.1+');
    }
    
    function testCanEmulateIsset() {
        $mock = new MockClassWithSpecialMethods();
        $mock->setReturnValue('__isset', true);
        $this->assertIdentical(isset($mock->a), true);
    }

    function testCanExpectUnset() {
        $mock = new MockClassWithSpecialMethods();
        $mock->expectOnce('__unset', array('a'));
        unset($mock->a);
    }
    
}

class TestOfSpecialMethods extends UnitTestCase {
    function skip() {
        $this->skipIf(version_compare(phpversion(), '5', '<'), 'Overloading not tested unless PHP 5+');
    }

    function testCanMockTheThingAtAll() {
        $mock = new MockClassWithSpecialMethods();
    }

    function testReturnFromSpecialAccessor() {
        $mock = new MockClassWithSpecialMethods();
        $mock->setReturnValue('__get', '1st Return', array('first'));
        $mock->setReturnValue('__get', '2nd Return', array('second'));
        $this->assertEqual($mock->first, '1st Return');
        $this->assertEqual($mock->second, '2nd Return');
    }

    function testcanExpectTheSettingOfValue() {
        $mock = new MockClassWithSpecialMethods();
        $mock->expectOnce('__set', array('a', 'A'));
        $mock->a = 'A';
    }

    function testCanSimulateAnOverloadmethod() {
        $mock = new MockClassWithSpecialMethods();
        $mock->expectOnce('__call', array('amOverloaded', array('A')));
        $mock->setReturnValue('__call', 'aaa');
        $this->assertIdentical($mock->amOverloaded('A'), 'aaa');
    }

    function testToStringMagic() {
        $mock = new MockClassWithSpecialMethods();
        $mock->expectOnce('__toString');
        $mock->setReturnValue('__toString', 'AAA');
        ob_start();
        print $mock;
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertEqual($output, 'AAA');
    }
}

class WithStaticMethod {
    static function aStaticMethod() { }
}
Mock::generate('WithStaticMethod');

class TestOfMockingClassesWithStaticMethods extends UnitTestCase {
    
    function testStaticMethodIsMockedAsStatic() {
        $mock = new WithStaticMethod();
        $reflection = new ReflectionClass($mock);
        $method = $reflection->getMethod('aStaticMethod');
        $this->assertTrue($method->isStatic());
    }
}

class MockTestException extends Exception { }

class TestOfThrowingExceptionsFromMocks extends UnitTestCase {

    function testCanThrowOnMethodCall() {
        $mock = new MockDummy();
        $mock->throwOn('aMethod');
        $this->expectException();
        $mock->aMethod();
    }

    function testCanThrowSpecificExceptionOnMethodCall() {
        $mock = new MockDummy();
        $mock->throwOn('aMethod', new MockTestException());
        $this->expectException();
        $mock->aMethod();
    }
    
    function testThrowsOnlyWhenCallSignatureMatches() {
        $mock = new MockDummy();
        $mock->throwOn('aMethod', new MockTestException(), array(3));
        $mock->aMethod(1);
        $mock->aMethod(2);
        $this->expectException();
        $mock->aMethod(3);
    }
    
    function testCanThrowOnParticularInvocation() {
        $mock = new MockDummy();
        $mock->throwAt(2, 'aMethod', new MockTestException());
        $mock->aMethod();
        $mock->aMethod();
        $this->expectException();
        $mock->aMethod();
    }
}

class TestOfThrowingErrorsFromMocks extends UnitTestCase {
    
    function testCanGenerateErrorFromMethodCall() {
        $mock = new MockDummy();
        $mock->errorOn('aMethod', 'Ouch!');
        $this->expectError('Ouch!');
        $mock->aMethod();
    }
    
    function testGeneratesErrorOnlyWhenCallSignatureMatches() {
        $mock = new MockDummy();
        $mock->errorOn('aMethod', 'Ouch!', array(3));
        $mock->aMethod(1);
        $mock->aMethod(2);
        $this->expectError();
        $mock->aMethod(3);
    }
    
    function testCanGenerateErrorOnParticularInvocation() {
        $mock = new MockDummy();
        $mock->errorAt(2, 'aMethod', 'Ouch!');
        $mock->aMethod();
        $mock->aMethod();
        $this->expectError();
        $mock->aMethod();
    }
}

Mock::generatePartial('Dummy', 'TestDummy', array('anotherMethod', 'aReferenceMethod'));

class TestOfPartialMocks extends UnitTestCase {

    function testMethodReplacementWithNoBehaviourReturnsNull() {
        $mock = new TestDummy();
        $this->assertEqual($mock->aMethod(99), 99);
        $this->assertNull($mock->anotherMethod());
    }

    function testSettingReturns() {
        $mock = new TestDummy();
        $mock->setReturnValue('anotherMethod', 33, array(3));
        $mock->setReturnValue('anotherMethod', 22);
        $mock->setReturnValueAt(2, 'anotherMethod', 44, array(3));
        $this->assertEqual($mock->anotherMethod(), 22);
        $this->assertEqual($mock->anotherMethod(3), 33);
        $this->assertEqual($mock->anotherMethod(3), 44);
    }

    function testSetReturnReferenceGivesOriginal() {
        $mock = new TestDummy();
        $object = 99;
        $mock->setReturnReferenceAt(0, 'aReferenceMethod', $object, array(3));
        $this->assertReference($mock->aReferenceMethod(3), $object);
    }

    function testReturnsAtGivesOriginalObjectHandle() {
        $mock = new TestDummy();
        $object = new Dummy();
        $mock->returnsAt(0, 'anotherMethod', $object, array(3));
        $this->assertSame($mock->anotherMethod(3), $object);
    }

    function testExpectations() {
        $mock = new TestDummy();
        $mock->expectCallCount('anotherMethod', 2);
        $mock->expect('anotherMethod', array(77));
        $mock->expectAt(1, 'anotherMethod', array(66));
        $mock->anotherMethod(77);
        $mock->anotherMethod(66);
    }

    function testSettingExpectationOnMissingMethodThrowsError() {
        $mock = new TestDummy();
        $this->expectError();
        $mock->expectCallCount('aMissingMethod', 2);
    }
}

class ConstructorSuperClass {
    function ConstructorSuperClass() { }
}

class ConstructorSubClass extends ConstructorSuperClass { }

class TestOfPHP4StyleSuperClassConstruct extends UnitTestCase {
    function testBasicConstruct() {
        Mock::generate('ConstructorSubClass');
        $mock = new MockConstructorSubClass();
        $this->assertIsA($mock, 'ConstructorSubClass');
        $this->assertTrue(method_exists($mock, 'ConstructorSuperClass'));
    }
}

class TestOfPHP5StaticMethodMocking extends UnitTestCase {
    function testCanCreateAMockObjectWithStaticMethodsWithoutError() {
        eval('
            class SimpleObjectContainingStaticMethod {
                static function someStatic() { }
            }
        ');
        Mock::generate('SimpleObjectContainingStaticMethod');
    }
}

class TestOfPHP5AbstractMethodMocking extends UnitTestCase {
    function testCanCreateAMockObjectFromAnAbstractWithProperFunctionDeclarations() {
        eval('
            abstract class SimpleAbstractClassContainingAbstractMethods {
                abstract function anAbstract();
                abstract function anAbstractWithParameter($foo);
                abstract function anAbstractWithMultipleParameters($foo, $bar);
            }
        ');
        Mock::generate('SimpleAbstractClassContainingAbstractMethods');
        $this->assertTrue(
            method_exists(
                // Testing with class name alone does not work in PHP 5.0
                new MockSimpleAbstractClassContainingAbstractMethods,
                'anAbstract'
            )
        );
        $this->assertTrue(
            method_exists(
                new MockSimpleAbstractClassContainingAbstractMethods,
                'anAbstractWithParameter'
            )
        );
        $this->assertTrue(
            method_exists(
                new MockSimpleAbstractClassContainingAbstractMethods,
                'anAbstractWithMultipleParameters'
            )
        );
    }

    function testMethodsDefinedAsAbstractInParentShouldHaveFullSignature() {
        eval('
             abstract class SimpleParentAbstractClassContainingAbstractMethods {
                abstract function anAbstract();
                abstract function anAbstractWithParameter($foo);
                abstract function anAbstractWithMultipleParameters($foo, $bar);
            }

             class SimpleChildAbstractClassContainingAbstractMethods extends SimpleParentAbstractClassContainingAbstractMethods {
                function anAbstract(){}
                function anAbstractWithParameter($foo){}
                function anAbstractWithMultipleParameters($foo, $bar){}
            }

            class EvenDeeperEmptyChildClass extends SimpleChildAbstractClassContainingAbstractMethods {}
        ');
        Mock::generate('SimpleChildAbstractClassContainingAbstractMethods');
        $this->assertTrue(
            method_exists(
                new MockSimpleChildAbstractClassContainingAbstractMethods,
                'anAbstract'
            )
        );
        $this->assertTrue(
            method_exists(
                new MockSimpleChildAbstractClassContainingAbstractMethods,
                'anAbstractWithParameter'
            )
        );
        $this->assertTrue(
            method_exists(
                new MockSimpleChildAbstractClassContainingAbstractMethods,
                'anAbstractWithMultipleParameters'
            )
        );
        Mock::generate('EvenDeeperEmptyChildClass');
        $this->assertTrue(
            method_exists(
                new MockEvenDeeperEmptyChildClass,
                'anAbstract'
            )
        );
        $this->assertTrue(
            method_exists(
                new MockEvenDeeperEmptyChildClass,
                'anAbstractWithParameter'
            )
        );
        $this->assertTrue(
            method_exists(
                new MockEvenDeeperEmptyChildClass,
                'anAbstractWithMultipleParameters'
            )
        );
    }
}

class DummyWithProtected
{
    public function aMethodCallsProtected() { return $this->aProtectedMethod(); }
    protected function aProtectedMethod() { return true; }
}

Mock::generatePartial('DummyWithProtected', 'TestDummyWithProtected', array('aProtectedMethod'));
class TestOfProtectedMethodPartialMocks extends UnitTestCase
{
    function testProtectedMethodExists() {
        $this->assertTrue(
            method_exists(
                new TestDummyWithProtected,
                'aProtectedMethod'
            )
        );
    }

    function testProtectedMethodIsCalled() {
        $object = new DummyWithProtected();
        $this->assertTrue($object->aMethodCallsProtected(), 'ensure original was called');
    }

    function testMockedMethodIsCalled() {
        $object = new TestDummyWithProtected();
        $object->setReturnValue('aProtectedMethod', false);
        $this->assertFalse($object->aMethodCallsProtected());
    }
}

?>
