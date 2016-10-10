<?php

require_once 'Swift/Tests/IdenticalBinaryExpectation.php';

/**
 * A base test case with some custom expectations.
 * @package Swift
 * @subpackage Tests
 * @author Chris Corbyn
 */
class Swift_Tests_SwiftUnitTestCase extends UnitTestCase
{
    /** An instance of the Yay_Mockery class */
    private $_mockery;

    /**
     * Decorates SimpleTest's implementation to auto-validate mock objects.
     */
    public function after($method)
    {
        try {
            $this->_mockery()->assertIsSatisfied();
        } catch (Yay_NotSatisfiedException $e) {
            $this->fail($e->getMessage());
        }
        $this->_mockery = null;

        return parent::after($method);
    }

    /**
     * Assert two binary strings are an exact match.
     * @param string $a
     * @param string $b
     * @param string $s formatted message
     */
    public function assertIdenticalBinary($a, $b, $s = '%s')
    {
        return $this->assert(new Swift_Tests_IdenticalBinaryExpectation($a), $b, $s);
    }

    // -- Protected methods

    /**
     * Returns a singleton-per-test method for Yay_Mockery.
     * @return Yay_Mockery
     */
    protected function _mockery()
    {
        if (!isset($this->_mockery)) {
            $this->_mockery = new Yay_Mockery();
        }

        return $this->_mockery;
    }

    /**
     * Create a mock object.
     * @param  string   $class
     * @return Yay_Mock
     */
    protected function _mock($class)
    {
        return $this->_mockery()->mock($class);
    }

    /**
     * Add mock expectations.
     * @param Yay_Expectations $expectations
     */
    protected function _checking($expectations)
    {
        return $this->_mockery()->checking($expectations);
    }

    /**
     * Create a mock object which does nothing.
     * @param  string   $class
     * @return Yay_Mock
     */
    protected function _stub($class)
    {
        $stub = $this->_mockery()->mock($class);
        $this->_mockery()->checking(Yay_Expectations::create()
            -> ignoring($stub)
            );

        return $stub;
    }

    protected function _states($machineName)
    {
        return $this->_mockery()->states($machineName);
    }

    protected function _sequence($sequenceName)
    {
        return $this->_mockery()->sequence($sequenceName);
    }
}
