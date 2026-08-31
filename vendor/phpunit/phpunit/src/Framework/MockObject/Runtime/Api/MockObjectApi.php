<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject;

use function assert;
use PHPUnit\Event\Code\NoTestCaseObjectOnCallStackException;
use PHPUnit\Event\Code\TestMethodBuilder;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker as InvocationMockerBuilder;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This trait is not covered by the backward compatibility promise for PHPUnit
 */
trait MockObjectApi
{
    public function __phpunit_hasMatchers(): bool
    {
        return $this->__phpunit_getInvocationHandler()->hasMatchers();
    }

    public function __phpunit_verify(bool $unsetInvocationMocker = true): void
    {
        $this->__phpunit_getInvocationHandler()->verify();

        if ($unsetInvocationMocker) {
            $this->__phpunit_unsetInvocationMocker();
        }
    }

    abstract public function __phpunit_state(): TestDoubleState;

    abstract public function __phpunit_getInvocationHandler(): InvocationHandler;

    abstract public function __phpunit_unsetInvocationMocker(): void;

    public function expects(InvocationOrder $matcher): InvocationMockerBuilder
    {
        assert($this instanceof StubInternal);

        if (!$this->__phpunit_wasGeneratedAsMockObject()) {
            $message = 'Expectations configured on test doubles that are created as test stubs are no longer verified since PHPUnit 10. Test doubles that are created as test stubs will no longer have the expects() method in PHPUnit 12. Update your test code to use createMock() instead of createStub(), for example.';

            try {
                $test = TestMethodBuilder::fromCallStack();

                if (!$this->__phpunit_state()->wasDeprecationAlreadyEmittedFor($test->id())) {
                    EventFacade::emitter()->testTriggeredPhpunitDeprecation(
                        $test,
                        $message,
                    );

                    $this->__phpunit_state()->deprecationWasEmittedFor($test->id());
                }
                // @codeCoverageIgnoreStart
            } catch (NoTestCaseObjectOnCallStackException) {
                EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation($message);
                // @codeCoverageIgnoreEnd
            }
        }

        return $this->__phpunit_getInvocationHandler()->expects($matcher);
    }
}
