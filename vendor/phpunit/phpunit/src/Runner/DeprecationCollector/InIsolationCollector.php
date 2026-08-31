<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\DeprecationCollector;

use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\TestRunner\IssueFilter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class InIsolationCollector
{
    private readonly IssueFilter $issueFilter;

    /**
     * @var list<non-empty-string>
     */
    private array $deprecations = [];

    /**
     * @var list<non-empty-string>
     */
    private array $filteredDeprecations = [];

    public function __construct(IssueFilter $issueFilter)
    {
        $this->issueFilter = $issueFilter;
    }

    /**
     * @return list<non-empty-string>
     */
    public function deprecations(): array
    {
        return $this->deprecations;
    }

    /**
     * @return list<non-empty-string>
     */
    public function filteredDeprecations(): array
    {
        return $this->filteredDeprecations;
    }

    public function testTriggeredDeprecation(DeprecationTriggered $event): void
    {
        $this->deprecations[] = $event->message();

        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        $this->filteredDeprecations[] = $event->message();
    }
}
