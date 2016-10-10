<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: reflection_php4.php 1672 2008-03-02 04:47:34Z edwardzyang $
 */

/**
 *  Version specific reflection API.
 *  @package SimpleTest
 *  @subpackage UnitTester
 *  @ignore duplicate with reflection_php5.php
 */
class SimpleReflection {
    var $_interface;

    /**
     *    Stashes the class/interface.
     *    @param string $interface    Class or interface
     *                                to inspect.
     */
    function SimpleReflection($interface) {
        $this->_interface = $interface;
    }

    /**
     *    Checks that a class has been declared.
     *    @return boolean        True if defined.
     *    @access public
     */
    function classExists() {
        return class_exists($this->_interface);
    }

    /**
     *    Needed to kill the autoload feature in PHP5
     *    for classes created dynamically.
     *    @return boolean        True if defined.
     *    @access public
     */
    function classExistsSansAutoload() {
        return class_exists($this->_interface);
    }

    /**
     *    Checks that a class or interface has been
     *    declared.
     *    @return boolean        True if defined.
     *    @access public
     */
    function classOrInterfaceExists() {
        return class_exists($this->_interface);
    }

    /**
     *    Needed to kill the autoload feature in PHP5
     *    for classes created dynamically.
     *    @return boolean        True if defined.
     *    @access public
     */
    function classOrInterfaceExistsSansAutoload() {
        return class_exists($this->_interface);
    }

    /**
     *    Gets the list of methods on a class or
     *    interface.
     *    @returns array          List of method names.
     *    @access public
     */
    function getMethods() {
        return get_class_methods($this->_interface);
    }

    /**
     *    Gets the list of interfaces from a class. If the
     *    class name is actually an interface then just that
     *    interface is returned.
     *    @returns array          List of interfaces.
     *    @access public
     */
    function getInterfaces() {
        return array();
    }

    /**
     *    Finds the parent class name.
     *    @returns string      Parent class name.
     *    @access public
     */
    function getParent() {
        return strtolower(get_parent_class($this->_interface));
    }

    /**
     *    Determines if the class is abstract, which for PHP 4
     *    will never be the case.
     *    @returns boolean      True if abstract.
     *    @access public
     */
    function isAbstract() {
        return false;
    }

    /**
     *    Determines if the the entity is an interface, which for PHP 4
     *    will never be the case.
     *    @returns boolean      True if interface.
     *    @access public
     */
    function isInterface() {
        return false;
    }
        
    /**
     *    Scans for final methods, but as it's PHP 4 there
     *    aren't any.
     *    @returns boolean   True if the class has a final method.
     *    @access public
     */
    function hasFinal() {
        return false;
    }

    /**
     *    Gets the source code matching the declaration
     *    of a method.
     *    @param string $method       Method name.
     *    @access public
     */
    function getSignature($method) {
        return "function &$method()";
    }
}
?>