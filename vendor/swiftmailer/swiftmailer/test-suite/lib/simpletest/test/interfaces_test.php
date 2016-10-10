<?php
// $Id: interfaces_test.php 1781 2008-04-22 10:43:01Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');
if (function_exists('spl_classes')) {
    include(dirname(__FILE__) . '/support/spl_examples.php');
}
if (version_compare(PHP_VERSION, '5.1', '>=')) {
    include(dirname(__FILE__) . '/interfaces_test_php5_1.php');
}

interface DummyInterface {
    function aMethod();
    function anotherMethod($a);
    function &referenceMethod(&$a);
}

Mock::generate('DummyInterface');
Mock::generatePartial('DummyInterface', 'PartialDummyInterface', array());

class TestOfMockInterfaces extends UnitTestCase {

    function testCanMockAnInterface() {
        $mock = new MockDummyInterface();
        $this->assertIsA($mock, 'SimpleMock');
        $this->assertIsA($mock, 'MockDummyInterface');
        $this->assertTrue(method_exists($mock, 'aMethod'));
        $this->assertTrue(method_exists($mock, 'anotherMethod'));
        $this->assertNull($mock->aMethod());
    }

    function testMockedInterfaceExpectsParameters() {
        $mock = new MockDummyInterface();
        $this->expectError();
        $mock->anotherMethod();
    }

    function testCannotPartiallyMockAnInterface() {
        $this->assertFalse(class_exists('PartialDummyInterface'));
    }
}

class TestOfSpl extends UnitTestCase {
    
    function skip() {
        $this->skipUnless(function_exists('spl_classes'), 'No SPL module loaded');
    }

    function testCanMockAllSplClasses() {
        if (! function_exists('spl_classes')) {
            return;
        }
        foreach(spl_classes() as $class) {
            if ($class == 'SplHeap') {
                continue;
            }
            if (version_compare(PHP_VERSION, '5.1', '<') &&
                $class == 'CachingIterator' ||
                $class == 'CachingRecursiveIterator' ||
                $class == 'FilterIterator' ||
                $class == 'LimitIterator' ||
                $class == 'ParentIterator') {
                // These iterators require an iterator be passed to them during
                // construction in PHP 5.0; there is no way for SimpleTest
                // to supply such an iterator, however, so support for it is
                // disabled.
                continue;
            }
            $mock_class = "Mock$class";
            Mock::generate($class);
            $this->assertIsA(new $mock_class(), $mock_class);
        }
    }

    function testExtensionOfCommonSplClasses() {
        Mock::generate('IteratorImplementation');
        $this->assertIsA(
                new IteratorImplementation(),
                'IteratorImplementation');
        Mock::generate('IteratorAggregateImplementation');
        $this->assertIsA(
                new IteratorAggregateImplementation(),
                'IteratorAggregateImplementation');
   }
}

class WithHint {
    function hinted(DummyInterface $object) { }
}

class ImplementsDummy implements DummyInterface {
    function aMethod() { }
    function anotherMethod($a) { }
    function &referenceMethod(&$a) { }
    function extraMethod($a = false) { }
}
Mock::generate('ImplementsDummy');

class TestOfImplementations extends UnitTestCase {

    function testMockedInterfaceCanPassThroughTypeHint() {
        $mock = new MockDummyInterface();
        $hinter = new WithHint();
        $hinter->hinted($mock);
    }

    function testImplementedInterfacesAreCarried() {
        $mock = new MockImplementsDummy();
        $hinter = new WithHint();
        $hinter->hinted($mock);
    }
    
    function testNoSpuriousWarningsWhenSkippingDefaultedParameter() {
        $mock = new MockImplementsDummy();
        $mock->extraMethod();
    }
}

interface SampleInterfaceWithConstruct {
    function __construct($something);
}

class TestOfInterfaceMocksWithConstruct extends UnitTestCase {
    function TODO_testBasicConstructOfAnInterface() {   // Fails in PHP 5.3dev
        Mock::generate('SampleInterfaceWithConstruct');
    }
}

interface SampleInterfaceWithClone {
    function __clone();
}

class TestOfSampleInterfaceWithClone extends UnitTestCase {
    function testCanMockWithoutErrors() {
        Mock::generate('SampleInterfaceWithClone');
    }
}
?>