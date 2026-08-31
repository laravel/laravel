<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * @author Yewhen Khoptynskyi <khoptynskyi@gmail.com>
 */
class TableCellStyle
{
    public const DEFAULT_ALIGN = 'left';

    private const TAG_OPTIONS = [
        'fg',
        'bg',
        'options',
    ];

    private const ALIGN_MAP = [
        'left' => \STR_PAD_RIGHT,
        'center' => \STR_PAD_BOTH,
        'right' => \STR_PAD_LEFT,
    ];

    private array $options = [
        'fg' => 'default',
        'bg' => 'default',
        'options' => null,
        'align' => self::DEFAULT_ALIGN,
        'cellFormat' => null,
    ];

    public function __construct(array $options = [])
    {
        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new InvalidArgumentException(\sprintf('The TableCellStyle does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        if (isset($options['align']) && !\array_key_exists($options['align'], self::ALIGN_MAP)) {
            throw new InvalidArgumentException(\sprintf('Wrong align value. Value must be following: \'%s\'.', implode('\', \'', array_keys(self::ALIGN_MAP))));
        }

        $this->options = array_merge($this->options, $options);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Gets options we need for tag for example fg, bg.
     *
     * @return string[]
     */
    public function getTagOptions(): array
    {
        return array_filter(
            $this->getOptions(),
            fn ($key) => \in_array($key, self::TAG_OPTIONS, true) && isset($this->options[$key]),
            \ARRAY_FILTER_USE_KEY
        );
    }

    public function getPadByAlign(): int
    {
        return self::ALIGN_MAP[$this->getOptions()['align']];
    }

    public function getCellFormat(): ?string
    {
        return $this->getOptions()['cellFormat'];
    }
}
