<?php
// $Id: reflection_php4_test.php 1505 2007-04-30 23:39:59Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');

class AnyOldThing {
    function aMethod() {
    }
}

class AnyOldChildThing extends AnyOldThing { }

class TestOfReflection extends UnitTestCase {

    function testClassExistence() {
        $reflection = new SimpleReflection('AnyOldThing');
        $this->assertTrue($reflection->classOrInterfaceExists());
        $this->assertTrue($reflection->classOrInterfaceExistsSansAutoload());
    }

    function testClassNonExistence() {
        $reflection = new SimpleReflection('UnknownThing');
        $this->assertFalse($reflection->classOrInterfaceExists());
        $this->assertFalse($reflection->classOrInterfaceExistsSansAutoload());
    }

    function testDetectionOfInterfacesAlwaysFalse() {
        $reflection = new SimpleReflection('AnyOldThing');
        $this->assertFalse($reflection->isAbstract());
        $this->assertFalse($reflection->isInterface());
    }

    function testFindingParentClass() {
        $reflection = new SimpleReflection('AnyOldChildThing');
        $this->assertEqual(strtolower($reflection->getParent()), 'anyoldthing');
    }

    function testMethodsListFromClass() {
        $reflection = new SimpleReflection('AnyOldThing');
        $methods = $reflection->getMethods();
        $this->assertEqualIgnoringCase($methods[0], 'aMethod');
    }

    function testNoInterfacesForPHP4() {
        $reflection = new SimpleReflection('AnyOldThing');
        $this->assertEqual(
                $reflection->getInterfaces(),
                array());
    }

    function testMostGeneralPossibleSignature() {
        $reflection = new SimpleReflection('AnyOldThing');
        $this->assertEqualIgnoringCase(
                $reflection->getSignature('aMethod'),
                'function &aMethod()');
    }

    function assertEqualIgnoringCase($a, $b) {
        return $this->assertEqual(strtolower($a), strtolower($b));
    }
}
?>