<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Code\IssueTrigger;

use function sprintf;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class IssueTrigger
{
    private ?Code $callee;
    private ?Code $caller;

    public static function from(?Code $callee, ?Code $caller): self
    {
        return new self($callee, $caller);
    }

    private function __construct(?Code $callee, ?Code $caller)
    {
        $this->callee = $callee;
        $this->caller = $caller;
    }

    /**
     * An issue is triggered in first-party code or in test code.
     */
    public function isSelf(): bool
    {
        return $this->callee !== null && $this->callee->isFirstPartyOrTest();
    }

    /**
     * First-party code triggers an issue in third-party code.
     */
    public function isDirect(): bool
    {
        return $this->caller !== null && $this->caller->isFirstPartyOrTest() &&
               $this->callee !== null && $this->callee->isThirdPartyOrPhpunitOrPhp();
    }

    /**
     * Third-party code triggers an issue.
     */
    public function isIndirect(): bool
    {
        return $this->caller !== null && $this->caller->isThirdPartyOrPhpunitOrPhp() &&
               $this->callee !== null && $this->callee->isThirdPartyOrPhpunitOrPhp();
    }

    public function isUnknown(): bool
    {
        return !$this->isSelf() && !$this->isDirect() && !$this->isIndirect();
    }

    public function asString(): string
    {
        if ($this->callee === null || $this->caller === null) {
            return 'unknown if issue was triggered in first-party code or third-party code';
        }

        return sprintf(
            'issue triggered by %s calling into %s',
            $this->caller->value,
            $this->callee->value,
        );
    }
}
