<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

use function count;
use Iterator;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @template-implements Iterator<non-negative-int, IniSetting>
 */
final class IniSettingCollectionIterator implements Iterator
{
    /**
     * @var list<IniSetting>
     */
    private readonly array $iniSettings;

    /**
     * @var non-negative-int
     */
    private int $position = 0;

    public function __construct(IniSettingCollection $iniSettings)
    {
        $this->iniSettings = $iniSettings->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->iniSettings);
    }

    /**
     * @return non-negative-int
     */
    public function key(): int
    {
        return $this->position;
    }

    public function current(): IniSetting
    {
        return $this->iniSettings[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
