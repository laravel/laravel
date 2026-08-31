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
final readonly class TestDox extends Metadata
{
    /**
     * @var non-empty-string
     */
    private string $text;

    /**
     * @param 0|1              $level
     * @param non-empty-string $text
     */
    protected function __construct(int $level, string $text)
    {
        parent::__construct($level);

        $this->text = $text;
    }

    public function isTestDox(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function text(): string
    {
        return $this->text;
    }
}
