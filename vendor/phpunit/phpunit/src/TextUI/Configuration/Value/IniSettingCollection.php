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
use Countable;
use IteratorAggregate;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 *
 * @template-implements IteratorAggregate<non-negative-int, IniSetting>
 */
final readonly class IniSettingCollection implements Countable, IteratorAggregate
{
    /**
     * @var list<IniSetting>
     */
    private array $iniSettings;

    /**
     * @param list<IniSetting> $iniSettings
     */
    public static function fromArray(array $iniSettings): self
    {
        return new self(...$iniSettings);
    }

    private function __construct(IniSetting ...$iniSettings)
    {
        $this->iniSettings = $iniSettings;
    }

    /**
     * @return list<IniSetting>
     */
    public function asArray(): array
    {
        return $this->iniSettings;
    }

    public function count(): int
    {
        return count($this->iniSettings);
    }

    public function getIterator(): IniSettingCollectionIterator
    {
        return new IniSettingCollectionIterator($this);
    }
}
