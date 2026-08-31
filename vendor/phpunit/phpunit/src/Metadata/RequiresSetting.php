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
final readonly class RequiresSetting extends Metadata
{
    /**
     * @var non-empty-string
     */
    private string $setting;

    /**
     * @var non-empty-string
     */
    private string $value;

    /**
     * @param 0|1              $level
     * @param non-empty-string $setting
     * @param non-empty-string $value
     */
    protected function __construct(int $level, string $setting, string $value)
    {
        parent::__construct($level);

        $this->setting = $setting;
        $this->value   = $value;
    }

    public function isRequiresSetting(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function setting(): string
    {
        return $this->setting;
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
