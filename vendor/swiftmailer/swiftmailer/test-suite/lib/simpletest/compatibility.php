<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @version    $Id: compatibility.php 1747 2008-04-13 18:26:47Z pp11 $
 */

/**
 *  Static methods for compatibility between different
 *  PHP versions.
 *  @package    SimpleTest
 */
class SimpleTestCompatibility {
    
    /**
     *    Creates a copy whether in PHP5 or PHP4.
     *    @param object $object     Thing to copy.
     *    @return object            A copy.
     *    @access public
     */
    static function copy($object) {
        if (version_compare(phpversion(), '5') >= 0) {
            eval('$copy = clone $object;');
            return $copy;
        }
        return $object;
    }
    
    /**
     *    Identity test. Drops back to equality + types for PHP5
     *    objects as the === operator counts as the
     *    stronger reference constraint.
     *    @param mixed $first    Test subject.
     *    @param mixed $second   Comparison object.
     *    @return boolean        True if identical.
     *    @access public
     */
    static function isIdentical($first, $second) {
        if (version_compare(phpversion(), '5') >= 0) {
            return SimpleTestCompatibility::isIdenticalType($first, $second);
        }
        if ($first != $second) {
            return false;
        }
        return ($first === $second);
    }
    
    /**
     *    Recursive type test.
     *    @param mixed $first    Test subject.
     *    @param mixed $second   Comparison object.
     *    @return boolean        True if same type.
     *    @access private
     */
    protected static function isIdenticalType($first, $second) {
        if (gettype($first) != gettype($second)) {
            return false;
        }
        if (is_object($first) && is_object($second)) {
            if (get_class($first) != get_class($second)) {
                return false;
            }
            return SimpleTestCompatibility::isArrayOfIdenticalTypes(
                    get_object_vars($first),
                    get_object_vars($second));
        }
        if (is_array($first) && is_array($second)) {
            return SimpleTestCompatibility::isArrayOfIdenticalTypes($first, $second);
        }
        if ($first !== $second) {
            return false;
        }
        return true;
    }
    
    /**
     *    Recursive type test for each element of an array.
     *    @param mixed $first    Test subject.
     *    @param mixed $second   Comparison object.
     *    @return boolean        True if identical.
     *    @access private
     */
    protected static function isArrayOfIdenticalTypes($first, $second) {
        if (array_keys($first) != array_keys($second)) {
            return false;
        }
        foreach (array_keys($first) as $key) {
            $is_identical = SimpleTestCompatibility::isIdenticalType(
                    $first[$key],
                    $second[$key]);
            if (! $is_identical) {
                return false;
            }
        }
        return true;
    }
    
    /**
     *    Test for two variables being aliases.
     *    @param mixed $first    Test subject.
     *    @param mixed $second   Comparison object.
     *    @return boolean        True if same.
     *    @access public
     */
    static function isReference(&$first, &$second) {
        if (version_compare(phpversion(), '5', '>=') && is_object($first)) {
            return ($first === $second);
        }
        if (is_object($first) && is_object($second)) {
            $id = uniqid("test");
            $first->$id = true;
            $is_ref = isset($second->$id);
            unset($first->$id);
            return $is_ref;
        }
        $temp = $first;
        $first = uniqid("test");
        $is_ref = ($first === $second);
        $first = $temp;
        return $is_ref;
    }
    
    /**
     *    Test to see if an object is a member of a
     *    class hiearchy.
     *    @param object $object    Object to test.
     *    @param string $class     Root name of hiearchy.
     *    @return boolean         True if class in hiearchy.
     *    @access public
     */
    static function isA($object, $class) {
        if (version_compare(phpversion(), '5') >= 0) {
            if (! class_exists($class, false)) {
                if (function_exists('interface_exists')) {
                    if (! interface_exists($class, false))  {
                        return false;
                    }
                }
            }
            eval("\$is_a = \$object instanceof $class;");
            return $is_a;
        }
        if (function_exists('is_a')) {
            return is_a($object, $class);
        }
        return ((strtolower($class) == get_class($object))
                or (is_subclass_of($object, $class)));
    }
    
    /**
     *    Sets a socket timeout for each chunk.
     *    @param resource $handle    Socket handle.
     *    @param integer $timeout    Limit in seconds.
     *    @access public
     */
    static function setTimeout($handle, $timeout) {
        if (function_exists('stream_set_timeout')) {
            stream_set_timeout($handle, $timeout, 0);
        } elseif (function_exists('socket_set_timeout')) {
            socket_set_timeout($handle, $timeout, 0);
        } elseif (function_exists('set_socket_timeout')) {
            set_socket_timeout($handle, $timeout, 0);
        }
    }
}
?>