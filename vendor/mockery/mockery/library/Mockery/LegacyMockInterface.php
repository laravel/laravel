<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Closure;
use Throwable;

interface LegacyMockInterface
{
    /**
     * In the event shouldReceive() accepting an array of methods/returns
     * this method will switch them from normal expectations to default
     * expectations
     *
     * @return self
     */
    public function byDefault();

    /**
     * Set mock to defer unexpected methods to its parent if possible
     *
     * @return self
     */
    public function makePartial();

    /**
     * Fetch the next available allocation order number
     *
     * @return int
     */
    public function mockery_allocateOrder();

    /**
     * Find an expectation matching the given method and arguments
     *
     * @template TMixed
     *
     * @param string        $method
     * @param array<TMixed> $args
     *
     * @return null|Expectation
     */
    public function mockery_findExpectation($method, array $args);

    /**
     * Return the container for this mock
     *
     * @return Container
     */
    public function mockery_getContainer();

    /**
     * Get current ordered number
     *
     * @return int
     */
    public function mockery_getCurrentOrder();

    /**
     * Gets the count of expectations for this mock
     *
     * @return int
     */
    public function mockery_getExpectationCount();

    /**
     * Return the expectations director for the given method
     *
     * @param string $method
     *
     * @return null|ExpectationDirector
     */
    public function mockery_getExpectationsFor($method);

    /**
     * Fetch array of ordered groups
     *
     * @return array<string,int>
     */
    public function mockery_getGroups();

    /**
     * @return string[]
     */
    public function mockery_getMockableMethods();

    /**
     * @return array
     */
    public function mockery_getMockableProperties();

    /**
     * Return the name for this mock
     *
     * @return string
     */
    public function mockery_getName();

    /**
     * Alternative setup method to constructor
     *
     * @param object $partialObject
     *
     * @return void
     */
    public function mockery_init(?Container $container = null, $partialObject = null);

    /**
     * @return bool
     */
    public function mockery_isAnonymous();

    /**
     * Set current ordered number
     *
     * @param int $order
     *
     * @return int
     */
    public function mockery_setCurrentOrder($order);

    /**
     * Return the expectations director for the given method
     *
     * @param string $method
     *
     * @return null|ExpectationDirector
     */
    public function mockery_setExpectationsFor($method, ExpectationDirector $director);

    /**
     * Set ordering for a group
     *
     * @param string $group
     * @param int    $order
     *
     * @return void
     */
    public function mockery_setGroup($group, $order);

    /**
     * Tear down tasks for this mock
     *
     * @return void
     */
    public function mockery_teardown();

    /**
     * Validate the current mock's ordering
     *
     * @param string $method
     * @param int    $order
     *
     * @throws Exception
     *
     * @return void
     */
    public function mockery_validateOrder($method, $order);

    /**
     * Iterate across all expectation directors and validate each
     *
     * @throws Throwable
     *
     * @return void
     */
    public function mockery_verify();

    /**
     * Allows additional methods to be mocked that do not explicitly exist on mocked class
     *
     * @param  string $method the method name to be mocked
     * @return self
     */
    public function shouldAllowMockingMethod($method);

    /**
     * @return self
     */
    public function shouldAllowMockingProtectedMethods();

    /**
     * Set mock to defer unexpected methods to its parent if possible
     *
     * @deprecated since 1.4.0. Please use makePartial() instead.
     *
     * @return self
     */
    public function shouldDeferMissing();

    /**
     * @return self
     */
    public function shouldHaveBeenCalled();

    /**
     * @template TMixed
     * @param string                     $method
     * @param null|array<TMixed>|Closure $args
     *
     * @return self
     */
    public function shouldHaveReceived($method, $args = null);

    /**
     * Set mock to ignore unexpected methods and return Undefined class
     *
     * @template TReturnValue
     *
     * @param null|TReturnValue $returnValue the default return value for calls to missing functions on this mock
     *
     * @return self
     */
    public function shouldIgnoreMissing($returnValue = null);

    /**
     * @template TMixed
     * @param null|array<TMixed> $args (optional)
     *
     * @return self
     */
    public function shouldNotHaveBeenCalled(?array $args = null);

    /**
     * @template TMixed
     * @param string                     $method
     * @param null|array<TMixed>|Closure $args
     *
     * @return self
     */
    public function shouldNotHaveReceived($method, $args = null);

    /**
     * Shortcut method for setting an expectation that a method should not be called.
     *
     * @param string ...$methodNames one or many methods that are expected not to be called in this mock
     *
     * @return Expectation|ExpectationInterface|HigherOrderMessage
     */
    public function shouldNotReceive(...$methodNames);

    /**
     * Set expected method calls
     *
     * @param string ...$methodNames one or many methods that are expected to be called in this mock
     *
     * @return Expectation|ExpectationInterface|HigherOrderMessage
     */
    public function shouldReceive(...$methodNames);
}
