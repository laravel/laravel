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

use function strtolower;
use Exception;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class InvocationHandler
{
    /**
     * @var list<Matcher>
     */
    private array $matchers = [];

    /**
     * @var array<string,Matcher>
     */
    private array $matcherMap = [];

    /**
     * @var list<ConfigurableMethod>
     */
    private readonly array $configurableMethods;
    private readonly bool $returnValueGeneration;

    /**
     * @param list<ConfigurableMethod> $configurableMethods
     */
    public function __construct(array $configurableMethods, bool $returnValueGeneration)
    {
        $this->configurableMethods   = $configurableMethods;
        $this->returnValueGeneration = $returnValueGeneration;
    }

    public function hasMatchers(): bool
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher->hasMatchers()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Looks up the match builder with identification $id and returns it.
     */
    public function lookupMatcher(string $id): ?Matcher
    {
        return $this->matcherMap[$id] ?? null;
    }

    /**
     * Registers a matcher with the identification $id. The matcher can later be
     * looked up using lookupMatcher() to figure out if it has been invoked.
     *
     * @throws MatcherAlreadyRegisteredException
     */
    public function registerMatcher(string $id, Matcher $matcher): void
    {
        if (isset($this->matcherMap[$id])) {
            throw new MatcherAlreadyRegisteredException($id);
        }

        $this->matcherMap[$id] = $matcher;
    }

    public function expects(InvocationOrder $rule): InvocationMocker
    {
        $matcher = new Matcher($rule);
        $this->addMatcher($matcher);

        return new InvocationMocker(
            $this,
            $matcher,
            ...$this->configurableMethods,
        );
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function invoke(Invocation $invocation): mixed
    {
        $exception      = null;
        $hasReturnValue = false;
        $returnValue    = null;

        foreach ($this->matchers as $match) {
            try {
                if ($match->matches($invocation)) {
                    $value = $match->invoked($invocation);

                    if (!$hasReturnValue) {
                        $returnValue    = $value;
                        $hasReturnValue = true;
                    }
                }
            } catch (Exception $e) {
                $exception = $e;
            }
        }

        if ($exception !== null) {
            throw $exception;
        }

        if ($hasReturnValue) {
            return $returnValue;
        }

        if (!$this->returnValueGeneration) {
            if (strtolower($invocation->methodName()) === '__tostring') {
                return '';
            }

            throw new ReturnValueNotConfiguredException($invocation);
        }

        return $invocation->generateReturnValue();
    }

    /**
     * @throws Throwable
     */
    public function verify(): void
    {
        foreach ($this->matchers as $matcher) {
            $matcher->verify();
        }
    }

    private function addMatcher(Matcher $matcher): void
    {
        $this->matchers[] = $matcher;
    }
}
