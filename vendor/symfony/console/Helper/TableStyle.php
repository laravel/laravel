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
use Symfony\Component\Console\Exception\LogicException;

/**
 * Defines the styles for a Table.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class TableStyle
{
    private string $paddingChar = ' ';
    private string $horizontalOutsideBorderChar = '-';
    private string $horizontalInsideBorderChar = '-';
    private string $verticalOutsideBorderChar = '|';
    private string $verticalInsideBorderChar = '|';
    private string $crossingChar = '+';
    private string $crossingTopRightChar = '+';
    private string $crossingTopMidChar = '+';
    private string $crossingTopLeftChar = '+';
    private string $crossingMidRightChar = '+';
    private string $crossingBottomRightChar = '+';
    private string $crossingBottomMidChar = '+';
    private string $crossingBottomLeftChar = '+';
    private string $crossingMidLeftChar = '+';
    private string $crossingTopLeftBottomChar = '+';
    private string $crossingTopMidBottomChar = '+';
    private string $crossingTopRightBottomChar = '+';
    private string $headerTitleFormat = '<fg=black;bg=white;options=bold> %s </>';
    private string $footerTitleFormat = '<fg=black;bg=white;options=bold> %s </>';
    private string $cellHeaderFormat = '<info>%s</info>';
    private string $cellRowFormat = '%s';
    private string $cellRowContentFormat = ' %s ';
    private string $borderFormat = '%s';
    private bool $displayOutsideBorder = true;
    private int $padType = \STR_PAD_RIGHT;

    /**
     * Sets padding character, used for cell padding.
     *
     * @return $this
     */
    public function setPaddingChar(string $paddingChar): static
    {
        if (!$paddingChar) {
            throw new LogicException('The padding char must not be empty.');
        }

        $this->paddingChar = $paddingChar;

        return $this;
    }

    /**
     * Gets padding character, used for cell padding.
     */
    public function getPaddingChar(): string
    {
        return $this->paddingChar;
    }

    /**
     * Sets horizontal border characters.
     *
     * <code>
     * ╔═══════════════╤══════════════════════════╤══════════════════╗
     * ║ ISBN          │ Title                    │ Author           ║
     * ╠═══════1═══════╪══════════════════════════╪══════════════════╣
     * ║ 99921-58-10-7 │ Divine Comedy            │ Dante Alighieri  ║
     * ║ 9971-5-0210-0 │ A Tale of Two Cities     │ Charles Dickens  ║
     * ╟───────2───────┼──────────────────────────┼──────────────────╢
     * ║ 960-425-059-0 │ The Lord of the Rings    │ J. R. R. Tolkien ║
     * ║ 80-902734-1-6 │ And Then There Were None │ Agatha Christie  ║
     * ╚═══════════════╧══════════════════════════╧══════════════════╝
     * </code>
     *
     * @return $this
     */
    public function setHorizontalBorderChars(string $outside, ?string $inside = null): static
    {
        $this->horizontalOutsideBorderChar = $outside;
        $this->horizontalInsideBorderChar = $inside ?? $outside;

        return $this;
    }

    /**
     * Sets vertical border characters.
     *
     * <code>
     * ╔═══════════════╤══════════════════════════╤══════════════════╗
     * 1 ISBN          2 Title                    │ Author           ║
     * ╠═══════════════╪══════════════════════════╪══════════════════╣
     * ║ 99921-58-10-7 │ Divine Comedy            │ Dante Alighieri  ║
     * ║ 9971-5-0210-0 │ A Tale of Two Cities     │ Charles Dickens  ║
     * ║ 960-425-059-0 │ The Lord of the Rings    │ J. R. R. Tolkien ║
     * ║ 80-902734-1-6 │ And Then There Were None │ Agatha Christie  ║
     * ╚═══════════════╧══════════════════════════╧══════════════════╝
     * </code>
     *
     * @return $this
     */
    public function setVerticalBorderChars(string $outside, ?string $inside = null): static
    {
        $this->verticalOutsideBorderChar = $outside;
        $this->verticalInsideBorderChar = $inside ?? $outside;

        return $this;
    }

    /**
     * Gets border characters.
     *
     * @internal
     */
    public function getBorderChars(): array
    {
        return [
            $this->horizontalOutsideBorderChar,
            $this->verticalOutsideBorderChar,
            $this->horizontalInsideBorderChar,
            $this->verticalInsideBorderChar,
        ];
    }

    /**
     * Sets crossing characters.
     *
     * Example:
     * <code>
     * 1═══════════════2══════════════════════════2══════════════════3
     * ║ ISBN          │ Title                    │ Author           ║
     * 8'══════════════0'═════════════════════════0'═════════════════4'
     * ║ 99921-58-10-7 │ Divine Comedy            │ Dante Alighieri  ║
     * ║ 9971-5-0210-0 │ A Tale of Two Cities     │ Charles Dickens  ║
     * 8───────────────0──────────────────────────0──────────────────4
     * ║ 960-425-059-0 │ The Lord of the Rings    │ J. R. R. Tolkien ║
     * ║ 80-902734-1-6 │ And Then There Were None │ Agatha Christie  ║
     * 7═══════════════6══════════════════════════6══════════════════5
     * </code>
     *
     * @param string      $cross          Crossing char (see #0 of example)
     * @param string      $topLeft        Top left char (see #1 of example)
     * @param string      $topMid         Top mid char (see #2 of example)
     * @param string      $topRight       Top right char (see #3 of example)
     * @param string      $midRight       Mid right char (see #4 of example)
     * @param string      $bottomRight    Bottom right char (see #5 of example)
     * @param string      $bottomMid      Bottom mid char (see #6 of example)
     * @param string      $bottomLeft     Bottom left char (see #7 of example)
     * @param string      $midLeft        Mid left char (see #8 of example)
     * @param string|null $topLeftBottom  Top left bottom char (see #8' of example), equals to $midLeft if null
     * @param string|null $topMidBottom   Top mid bottom char (see #0' of example), equals to $cross if null
     * @param string|null $topRightBottom Top right bottom char (see #4' of example), equals to $midRight if null
     *
     * @return $this
     */
    public function setCrossingChars(string $cross, string $topLeft, string $topMid, string $topRight, string $midRight, string $bottomRight, string $bottomMid, string $bottomLeft, string $midLeft, ?string $topLeftBottom = null, ?string $topMidBottom = null, ?string $topRightBottom = null): static
    {
        $this->crossingChar = $cross;
        $this->crossingTopLeftChar = $topLeft;
        $this->crossingTopMidChar = $topMid;
        $this->crossingTopRightChar = $topRight;
        $this->crossingMidRightChar = $midRight;
        $this->crossingBottomRightChar = $bottomRight;
        $this->crossingBottomMidChar = $bottomMid;
        $this->crossingBottomLeftChar = $bottomLeft;
        $this->crossingMidLeftChar = $midLeft;
        $this->crossingTopLeftBottomChar = $topLeftBottom ?? $midLeft;
        $this->crossingTopMidBottomChar = $topMidBottom ?? $cross;
        $this->crossingTopRightBottomChar = $topRightBottom ?? $midRight;

        return $this;
    }

    /**
     * Sets default crossing character used for each cross.
     *
     * @see {@link setCrossingChars()} for setting each crossing individually.
     */
    public function setDefaultCrossingChar(string $char): self
    {
        return $this->setCrossingChars($char, $char, $char, $char, $char, $char, $char, $char, $char);
    }

    /**
     * Gets crossing character.
     */
    public function getCrossingChar(): string
    {
        return $this->crossingChar;
    }

    /**
     * Gets crossing characters.
     *
     * @internal
     */
    public function getCrossingChars(): array
    {
        return [
            $this->crossingChar,
            $this->crossingTopLeftChar,
            $this->crossingTopMidChar,
            $this->crossingTopRightChar,
            $this->crossingMidRightChar,
            $this->crossingBottomRightChar,
            $this->crossingBottomMidChar,
            $this->crossingBottomLeftChar,
            $this->crossingMidLeftChar,
            $this->crossingTopLeftBottomChar,
            $this->crossingTopMidBottomChar,
            $this->crossingTopRightBottomChar,
        ];
    }

    /**
     * Sets header cell format.
     *
     * @return $this
     */
    public function setCellHeaderFormat(string $cellHeaderFormat): static
    {
        $this->cellHeaderFormat = $cellHeaderFormat;

        return $this;
    }

    /**
     * Gets header cell format.
     */
    public function getCellHeaderFormat(): string
    {
        return $this->cellHeaderFormat;
    }

    /**
     * Sets row cell format.
     *
     * @return $this
     */
    public function setCellRowFormat(string $cellRowFormat): static
    {
        $this->cellRowFormat = $cellRowFormat;

        return $this;
    }

    /**
     * Gets row cell format.
     */
    public function getCellRowFormat(): string
    {
        return $this->cellRowFormat;
    }

    /**
     * Sets row cell content format.
     *
     * @return $this
     */
    public function setCellRowContentFormat(string $cellRowContentFormat): static
    {
        $this->cellRowContentFormat = $cellRowContentFormat;

        return $this;
    }

    /**
     * Gets row cell content format.
     */
    public function getCellRowContentFormat(): string
    {
        return $this->cellRowContentFormat;
    }

    /**
     * Sets table border format.
     *
     * @return $this
     */
    public function setBorderFormat(string $borderFormat): static
    {
        $this->borderFormat = $borderFormat;

        return $this;
    }

    /**
     * Gets table border format.
     */
    public function getBorderFormat(): string
    {
        return $this->borderFormat;
    }

    /**
     * Sets cell padding type.
     *
     * @return $this
     */
    public function setPadType(int $padType): static
    {
        if (!\in_array($padType, [\STR_PAD_LEFT, \STR_PAD_RIGHT, \STR_PAD_BOTH], true)) {
            throw new InvalidArgumentException('Invalid padding type. Expected one of (STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH).');
        }

        $this->padType = $padType;

        return $this;
    }

    /**
     * Gets cell padding type.
     */
    public function getPadType(): int
    {
        return $this->padType;
    }

    public function getHeaderTitleFormat(): string
    {
        return $this->headerTitleFormat;
    }

    /**
     * @return $this
     */
    public function setHeaderTitleFormat(string $format): static
    {
        $this->headerTitleFormat = $format;

        return $this;
    }

    public function getFooterTitleFormat(): string
    {
        return $this->footerTitleFormat;
    }

    /**
     * @return $this
     */
    public function setFooterTitleFormat(string $format): static
    {
        $this->footerTitleFormat = $format;

        return $this;
    }

    public function setDisplayOutsideBorder($displayOutSideBorder): static
    {
        $this->displayOutsideBorder = $displayOutSideBorder;

        return $this;
    }

    public function displayOutsideBorder(): bool
    {
        return $this->displayOutsideBorder;
    }
}
