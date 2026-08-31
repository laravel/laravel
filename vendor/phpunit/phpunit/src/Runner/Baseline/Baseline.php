<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Baseline;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Baseline
{
    public const VERSION = 1;

    /**
     * @var array<non-empty-string, array<positive-int, list<Issue>>>
     */
    private array $issues = [];

    public function add(Issue $issue): void
    {
        if (!isset($this->issues[$issue->file()])) {
            $this->issues[$issue->file()] = [];
        }

        if (!isset($this->issues[$issue->file()][$issue->line()])) {
            $this->issues[$issue->file()][$issue->line()] = [];
        }

        $this->issues[$issue->file()][$issue->line()][] = $issue;
    }

    public function has(Issue $issue): bool
    {
        if (!isset($this->issues[$issue->file()][$issue->line()])) {
            return false;
        }

        foreach ($this->issues[$issue->file()][$issue->line()] as $_issue) {
            if ($_issue->equals($issue)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, array<positive-int, list<Issue>>>
     */
    public function groupedByFileAndLine(): array
    {
        return $this->issues;
    }
}
