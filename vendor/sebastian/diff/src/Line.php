<?php declare(strict_types=1);
/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Diff;

final class Line
{
    public const ADDED     = 1;
    public const REMOVED   = 2;
    public const UNCHANGED = 3;
    private int $type;
    private string $content;

    public function __construct(int $type = self::UNCHANGED, string $content = '')
    {
        $this->type    = $type;
        $this->content = $content;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function type(): int
    {
        return $this->type;
    }

    public function isAdded(): bool
    {
        return $this->type === self::ADDED;
    }

    public function isRemoved(): bool
    {
        return $this->type === self::REMOVED;
    }

    public function isUnchanged(): bool
    {
        return $this->type === self::UNCHANGED;
    }
}
