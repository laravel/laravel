<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Group extends Metadata
{
    /**
     * @var non-empty-string
     */
    private string $groupName;

    /**
     * @param 0|1              $level
     * @param non-empty-string $groupName
     */
    protected function __construct(int $level, string $groupName)
    {
        parent::__construct($level);

        $this->groupName = $groupName;
    }

    public function isGroup(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function groupName(): string
    {
        return $this->groupName;
    }
}
