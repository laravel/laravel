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
final readonly class RequiresOperatingSystemFamily extends Metadata
{
    /**
     * @var non-empty-string
     */
    private string $operatingSystemFamily;

    /**
     * @param 0|1              $level
     * @param non-empty-string $operatingSystemFamily
     */
    protected function __construct(int $level, string $operatingSystemFamily)
    {
        parent::__construct($level);

        $this->operatingSystemFamily = $operatingSystemFamily;
    }

    public function isRequiresOperatingSystemFamily(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function operatingSystemFamily(): string
    {
        return $this->operatingSystemFamily;
    }
}
