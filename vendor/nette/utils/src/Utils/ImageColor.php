<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function hexdec, ltrim, max, min, round, strlen;


/**
 * Represent RGB color (0..255) with opacity (0..1).
 */
class ImageColor
{
	/**
	 * Creates a color from RGB components (0..255) and opacity (0..1).
	 */
	public static function rgb(int $red, int $green, int $blue, float $opacity = 1): self
	{
		return new self($red, $green, $blue, $opacity);
	}


	/**
	 * Accepts formats #RRGGBB, #RRGGBBAA, #RGB, #RGBA
	 */
	public static function hex(string $hex): self
	{
		$hex = ltrim($hex, '#');
		$len = strlen($hex);
		if ($len === 3 || $len === 4) {
			return new self(
				(int) hexdec($hex[0]) * 17,
				(int) hexdec($hex[1]) * 17,
				(int) hexdec($hex[2]) * 17,
				(int) hexdec($hex[3] ?? 'F') * 17 / 255,
			);
		} elseif ($len === 6 || $len === 8) {
			return new self(
				(int) hexdec($hex[0] . $hex[1]),
				(int) hexdec($hex[2] . $hex[3]),
				(int) hexdec($hex[4] . $hex[5]),
				(int) hexdec(($hex[6] ?? 'F') . ($hex[7] ?? 'F')) / 255,
			);
		} else {
			throw new Nette\InvalidArgumentException('Invalid hex color format.');
		}
	}


	private function __construct(
		public int $red,
		public int $green,
		public int $blue,
		public float $opacity = 1,
	) {
		$this->red = max(0, min(255, $red));
		$this->green = max(0, min(255, $green));
		$this->blue = max(0, min(255, $blue));
		$this->opacity = max(0, min(1, $opacity));
	}


	/**
	 * Returns GD-compatible color array [R, G, B, alpha].
	 * @return array{int<0, 255>, int<0, 255>, int<0, 255>, int<0, 127>}
	 */
	public function toRGBA(): array
	{
		return [
			max(0, min(255, $this->red)),
			max(0, min(255, $this->green)),
			max(0, min(255, $this->blue)),
			max(0, min(127, (int) round(127 - $this->opacity * 127))),
		];
	}
}
