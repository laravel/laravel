<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

class VerificationDirector
{
    /**
     * @var VerificationExpectation
     */
    private $expectation;

    /**
     * @var ReceivedMethodCalls
     */
    private $receivedMethodCalls;

    public function __construct(ReceivedMethodCalls $receivedMethodCalls, VerificationExpectation $expectation)
    {
        $this->receivedMethodCalls = $receivedMethodCalls;
        $this->expectation = $expectation;
    }

    /**
     * @return self
     */
    public function atLeast()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('atLeast', []);
    }

    /**
     * @return self
     */
    public function atMost()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('atMost', []);
    }

    /**
     * @param int $minimum
     * @param int $maximum
     *
     * @return self
     */
    public function between($minimum, $maximum)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('between', [$minimum, $maximum]);
    }

    /**
     * @return self
     */
    public function once()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('once', []);
    }

    /**
     * @param int $limit
     *
     * @return self
     */
    public function times($limit = null)
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('times', [$limit]);
    }

    /**
     * @return self
     */
    public function twice()
    {
        return $this->cloneWithoutCountValidatorsApplyAndVerify('twice', []);
    }

    public function verify()
    {
        $this->receivedMethodCalls->verify($this->expectation);
    }

    /**
     * @template TArgs
     *
     * @param TArgs $args
     *
     * @return self
     */
    public function with(...$args)
    {
        return $this->cloneApplyAndVerify('with', $args);
    }

    /**
     * @return self
     */
    public function withAnyArgs()
    {
        return $this->cloneApplyAndVerify('withAnyArgs', []);
    }

    /**
     * @template TArgs
     *
     * @param TArgs $args
     *
     * @return self
     */
    public function withArgs($args)
    {
        return $this->cloneApplyAndVerify('withArgs', [$args]);
    }

    /**
     * @return self
     */
    public function withNoArgs()
    {
        return $this->cloneApplyAndVerify('withNoArgs', []);
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return self
     */
    protected function cloneApplyAndVerify($method, $args)
    {
        $verificationExpectation = clone $this->expectation;

        $verificationExpectation->{$method}(...$args);

        $verificationDirector = new self($this->receivedMethodCalls, $verificationExpectation);

        $verificationDirector->verify();

        return $verificationDirector;
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return self
     */
    protected function cloneWithoutCountValidatorsApplyAndVerify($method, $args)
    {
        $verificationExpectation = clone $this->expectation;

        $verificationExpectation->clearCountValidators();

        $verificationExpectation->{$method}(...$args);

        $verificationDirector = new self($this->receivedMethodCalls, $verificationExpectation);

        $verificationDirector->verify();

        return $verificationDirector;
    }
}
