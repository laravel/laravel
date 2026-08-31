<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function is_array, is_int, is_string;
use const IMG_BMP, IMG_FLIP_BOTH, IMG_FLIP_HORIZONTAL, IMG_FLIP_VERTICAL, IMG_GIF, IMG_JPG, IMG_PNG, IMG_WEBP, PATHINFO_EXTENSION;


/**
 * Basic manipulation with images. Supported types are JPEG, PNG, GIF, WEBP, AVIF and BMP.
 *
 * <code>
 * $image = Image::fromFile('nette.jpg');
 * $image->resize(150, 100);
 * $image->sharpen();
 * $image->send();
 * </code>
 *
 * @method Image affine(array<int, float|int> $affine, ?array{x: int, y: int, width: int, height: int} $clip = null)
 * @method void alphaBlending(bool $enable)
 * @method void antialias(bool $enable)
 * @method void arc(int $centerX, int $centerY, int $width, int $height, int $startAngle, int $endAngle, ImageColor $color)
 * @method int colorAllocate(int $red, int $green, int $blue)
 * @method int colorAllocateAlpha(int $red, int $green, int $blue, int $alpha)
 * @method int colorAt(int $x, int $y)
 * @method int colorClosest(int $red, int $green, int $blue)
 * @method int colorClosestAlpha(int $red, int $green, int $blue, int $alpha)
 * @method int colorClosestHWB(int $red, int $green, int $blue)
 * @method void colorDeallocate(int $color)
 * @method int colorExact(int $red, int $green, int $blue)
 * @method int colorExactAlpha(int $red, int $green, int $blue, int $alpha)
 * @method void colorMatch(Image $image2)
 * @method int colorResolve(int $red, int $green, int $blue)
 * @method int colorResolveAlpha(int $red, int $green, int $blue, int $alpha)
 * @method void colorSet(int $index, int $red, int $green, int $blue, int $alpha = 0)
 * @method array{red: int, green: int, blue: int, alpha: int} colorsForIndex(int $color)
 * @method int colorsTotal()
 * @method int colorTransparent(?int $color = null)
 * @method void convolution(array<int, array<int, float>> $matrix, float $div, float $offset)
 * @method void copy(Image $src, int $dstX, int $dstY, int $srcX, int $srcY, int $srcW, int $srcH)
 * @method void copyMerge(Image $src, int $dstX, int $dstY, int $srcX, int $srcY, int $srcW, int $srcH, int $pct)
 * @method void copyMergeGray(Image $src, int $dstX, int $dstY, int $srcX, int $srcY, int $srcW, int $srcH, int $pct)
 * @method void copyResampled(Image $src, int $dstX, int $dstY, int $srcX, int $srcY, int $dstW, int $dstH, int $srcW, int $srcH)
 * @method void copyResized(Image $src, int $dstX, int $dstY, int $srcX, int $srcY, int $dstW, int $dstH, int $srcW, int $srcH)
 * @method Image cropAuto(int $mode = 0, float $threshold = .5, ?ImageColor $color = null)
 * @method void ellipse(int $centerX, int $centerY, int $width, int $height, ImageColor $color)
 * @method void fill(int $x, int $y, ImageColor $color)
 * @method void filledArc(int $centerX, int $centerY, int $width, int $height, int $startAngle, int $endAngle, ImageColor $color, int $style)
 * @method void filledEllipse(int $centerX, int $centerY, int $width, int $height, ImageColor $color)
 * @method void filledPolygon(array<int, int> $points, ImageColor $color)
 * @method void filledRectangle(int $x1, int $y1, int $x2, int $y2, ImageColor $color)
 * @method void fillToBorder(int $x, int $y, ImageColor $borderColor, ImageColor $color)
 * @method void filter(int $filter, ...$args)
 * @method void flip(int $mode)
 * @method array<int, int> ftText(float $size, float $angle, int $x, int $y, ImageColor $color, string $fontFile, string $text, array<string, mixed> $options = [])
 * @method void gammaCorrect(float $inputgamma, float $outputgamma)
 * @method array{int, int, int, int} getClip()
 * @method int getInterpolation()
 * @method int interlace(?bool $enable = null)
 * @method bool isTrueColor()
 * @method void layerEffect(int $effect)
 * @method void line(int $x1, int $y1, int $x2, int $y2, ImageColor $color)
 * @method void openPolygon(array<int, int> $points, ImageColor $color)
 * @method void paletteCopy(Image $source)
 * @method void paletteToTrueColor()
 * @method void polygon(array<int, int> $points, ImageColor $color)
 * @method void rectangle(int $x1, int $y1, int $x2, int $y2, ImageColor $color)
 * @method mixed resolution(?int $resolutionX = null, ?int $resolutionY = null)
 * @method Image rotate(float $angle, ImageColor $backgroundColor)
 * @method void saveAlpha(bool $enable)
 * @method Image scale(int $newWidth, int $newHeight = -1, int $mode = 3)
 * @method void setBrush(Image $brush)
 * @method void setClip(int $x1, int $y1, int $x2, int $y2)
 * @method void setInterpolation(int $method = 3)
 * @method void setPixel(int $x, int $y, ImageColor $color)
 * @method void setStyle(array<int, int> $style)
 * @method void setThickness(int $thickness)
 * @method void setTile(Image $tile)
 * @method void trueColorToPalette(bool $dither, int $ncolors)
 * @method array<int, int> ttfText(float $size, float $angle, int $x, int $y, ImageColor $color, string $fontfile, string $text, array<string, mixed> $options = [])
 * @property-read positive-int $width
 * @property-read positive-int $height
 * @property-read \GdImage $imageResource
 */
class Image
{
	use Nette\SmartObject;

	/** Prevent from getting resized to a bigger size than the original */
	public const ShrinkOnly = 0b0001;

	/** Resizes to a specified width and height without keeping aspect ratio */
	public const Stretch = 0b0010;

	/** Resizes to fit into a specified width and height and preserves aspect ratio */
	public const OrSmaller = 0b0000;

	/** Resizes while bounding the smaller dimension to the specified width or height and preserves aspect ratio */
	public const OrBigger = 0b0100;

	/** Resizes to the smallest possible size to completely cover specified width and height and reserves aspect ratio */
	public const Cover = 0b1000;

	/** @deprecated use Image::ShrinkOnly */
	public const SHRINK_ONLY = self::ShrinkOnly;

	/** @deprecated use Image::Stretch */
	public const STRETCH = self::Stretch;

	/** @deprecated use Image::OrSmaller */
	public const FIT = self::OrSmaller;

	/** @deprecated use Image::OrBigger */
	public const FILL = self::OrBigger;

	/** @deprecated use Image::Cover */
	public const EXACT = self::Cover;

	/** @deprecated use Image::EmptyGIF */
	public const EMPTY_GIF = self::EmptyGIF;

	/** image types */
	public const
		JPEG = ImageType::JPEG,
		PNG = ImageType::PNG,
		GIF = ImageType::GIF,
		WEBP = ImageType::WEBP,
		AVIF = ImageType::AVIF,
		BMP = ImageType::BMP;

	public const EmptyGIF = "GIF89a\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;";

	private const Formats = [ImageType::JPEG => 'jpeg', ImageType::PNG => 'png', ImageType::GIF => 'gif', ImageType::WEBP => 'webp', ImageType::AVIF => 'avif', ImageType::BMP => 'bmp'];
	private const Sentinel = "\0";

	private \GdImage $image;


	/**
	 * Returns RGB color (0..255) and transparency (0..127).
	 * @deprecated use ImageColor::rgb()
	 * @return array{red: int, green: int, blue: int, alpha: int}
	 */
	public static function rgb(int $red, int $green, int $blue, int $transparency = 0): array
	{
		return [
			'red' => max(0, min(255, $red)),
			'green' => max(0, min(255, $green)),
			'blue' => max(0, min(255, $blue)),
			'alpha' => max(0, min(127, $transparency)),
		];
	}


	/**
	 * Reads an image from a file and returns its type in $type. If $warnings is passed, recoverable decoder
	 * warnings are returned in it instead of being raised as a PHP warning.
	 * @param-out ?string  $warnings
	 * @throws Nette\NotSupportedException if gd extension is not loaded
	 * @throws UnknownImageFileException if file not found or file type is not known
	 */
	public static function fromFile(string $file, ?int &$type = null, ?string &$warnings = self::Sentinel): static
	{
		self::ensureExtension();
		$type = self::detectTypeFromFile($file);
		if (!$type) {
			throw new UnknownImageFileException(is_file($file) ? "Unknown type of file '$file'." : "File '$file' not found.");
		}

		return self::invokeSafe('imagecreatefrom' . self::Formats[$type], $file, "Unable to open file '$file'.", __METHOD__, $warnings);
	}


	/**
	 * Reads an image from a string and returns its type in $type. If $warnings is passed, recoverable decoder
	 * warnings are returned in it instead of being raised as a PHP warning.
	 * @param-out ?string  $warnings
	 * @throws Nette\NotSupportedException if gd extension is not loaded
	 * @throws ImageException
	 */
	public static function fromString(string $s, ?int &$type = null, ?string &$warnings = self::Sentinel): static
	{
		self::ensureExtension();
		$type = self::detectTypeFromString($s);
		if (!$type) {
			throw new UnknownImageFileException('Unknown type of image.');
		}

		return self::invokeSafe('imagecreatefromstring', $s, 'Unable to open image from string.', __METHOD__, $warnings);
	}


	/** @param  callable-string  $func */
	private static function invokeSafe(
		string $func,
		string $arg,
		string $message,
		string $callee,
		?string &$warnings = self::Sentinel,
	): static
	{
		$errors = [];
		$res = Callback::invokeSafe($func, [$arg], function (string $message) use (&$errors): void {
			$errors[] = $message;
		});

		$raiseWarning = $warnings === self::Sentinel;
		$warnings = $errors ? implode(', ', $errors) : null;

		if (!$res) {
			throw new ImageException($message . ' Errors: ' . $warnings);
		} elseif ($errors && $raiseWarning) {
			trigger_error($callee . '(): ' . $warnings, E_USER_WARNING);
		}

		return new static($res);
	}


	/**
	 * Creates a new true color image of the given dimensions. The default color is black.
	 * @param  positive-int  $width
	 * @param  positive-int  $height
	 * @param  ImageColor|array{red: int, green: int, blue: int, alpha?: int}|null  $color
	 * @throws Nette\NotSupportedException if gd extension is not loaded
	 */
	public static function fromBlank(int $width, int $height, ImageColor|array|null $color = null): static
	{
		self::ensureExtension();
		if ($width < 1 || $height < 1) {
			throw new Nette\InvalidArgumentException('Image width and height must be greater than zero.');
		}

		$image = new static(imagecreatetruecolor($width, $height));
		if ($color) {
			$image->alphaBlending(false);
			$image->filledRectangle(0, 0, $width - 1, $height - 1, self::normalizeColor($color));
			$image->alphaBlending(true);
		}

		return $image;
	}


	/**
	 * Returns the type of image from file.
	 * @param-out ?int  $width
	 * @param-out ?int  $height
	 * @return ?ImageType::*
	 */
	public static function detectTypeFromFile(string $file, mixed &$width = null, mixed &$height = null): ?int
	{
		[$width, $height, $type] = Helpers::falseToNull(@getimagesize($file)); // @ - files smaller than 12 bytes causes read error
		return $type && isset(self::Formats[$type]) ? $type : null;
	}


	/**
	 * Returns the type of image from string.
	 * @param-out ?int  $width
	 * @param-out ?int  $height
	 * @return ?ImageType::*
	 */
	public static function detectTypeFromString(string $s, mixed &$width = null, mixed &$height = null): ?int
	{
		[$width, $height, $type] = Helpers::falseToNull(@getimagesizefromstring($s)); // @ - strings smaller than 12 bytes causes read error
		return $type && isset(self::Formats[$type]) ? $type : null;
	}


	/**
	 * Returns the file extension for the given image type.
	 * @param  ImageType::*  $type
	 * @return value-of<self::Formats>
	 */
	public static function typeToExtension(int $type): string
	{
		if (!isset(self::Formats[$type])) {
			throw new Nette\InvalidArgumentException("Unsupported image type '$type'.");
		}

		return self::Formats[$type];
	}


	/**
	 * Returns the image type for given file extension.
	 * @return ImageType::*
	 */
	public static function extensionToType(string $extension): int
	{
		$extensions = array_flip(self::Formats) + ['jpg' => ImageType::JPEG];
		$extension = strtolower($extension);
		if (!isset($extensions[$extension])) {
			throw new Nette\InvalidArgumentException("Unsupported file extension '$extension'.");
		}

		return $extensions[$extension];
	}


	/**
	 * Returns the mime type for the given image type.
	 * @param  ImageType::*  $type
	 */
	public static function typeToMimeType(int $type): string
	{
		return 'image/' . self::typeToExtension($type);
	}


	/**
	 * Checks whether the given image type is supported by the GD extension.
	 * @param  ImageType::*  $type
	 */
	public static function isTypeSupported(int $type): bool
	{
		self::ensureExtension();
		return (bool) (imagetypes() & match ($type) {
			ImageType::JPEG => IMG_JPG,
			ImageType::PNG => IMG_PNG,
			ImageType::GIF => IMG_GIF,
			ImageType::WEBP => IMG_WEBP,
			ImageType::AVIF => IMG_AVIF,
			ImageType::BMP => IMG_BMP,
			default => 0,
		});
	}


	/**
	 * Returns list of image types supported by the GD extension.
	 * @return  ImageType::*[]
	 */
	public static function getSupportedTypes(): array
	{
		self::ensureExtension();
		$flag = imagetypes();
		return array_filter([
			$flag & IMG_GIF ? ImageType::GIF : null,
			$flag & IMG_JPG ? ImageType::JPEG : null,
			$flag & IMG_PNG ? ImageType::PNG : null,
			$flag & IMG_WEBP ? ImageType::WEBP : null,
			$flag & IMG_AVIF ? ImageType::AVIF : null,
			$flag & IMG_BMP ? ImageType::BMP : null,
		]);
	}


	/**
	 * Wraps GD image.
	 */
	public function __construct(\GdImage $image)
	{
		$this->setImageResource($image);
		imagesavealpha($image, true);
	}


	/**
	 * Returns image width.
	 * @return positive-int
	 */
	public function getWidth(): int
	{
		return imagesx($this->image);
	}


	/**
	 * Returns image height.
	 * @return positive-int
	 */
	public function getHeight(): int
	{
		return imagesy($this->image);
	}


	/**
	 * Sets image resource.
	 */
	protected function setImageResource(\GdImage $image): static
	{
		$this->image = $image;
		return $this;
	}


	/**
	 * Returns image GD resource.
	 */
	public function getImageResource(): \GdImage
	{
		return $this->image;
	}


	/**
	 * Scales an image. Width and height accept pixels or percent.
	 * @param  int-mask-of<self::OrSmaller|self::OrBigger|self::Stretch|self::Cover|self::ShrinkOnly>  $mode
	 */
	public function resize(int|string|null $width, int|string|null $height, int $mode = self::OrSmaller): static
	{
		if ($mode & self::Cover) {
			if ($width === null || $height === null) {
				throw new Nette\InvalidArgumentException('Both width and height must be set for Cover mode.');
			}

			return $this->resize($width, $height, self::OrBigger)->crop('50%', '50%', $width, $height);
		}

		[$newWidth, $newHeight] = static::calculateSize($this->getWidth(), $this->getHeight(), $width, $height, $mode);

		if ($newWidth !== $this->getWidth() || $newHeight !== $this->getHeight()) { // resize
			$newImage = static::fromBlank($newWidth, $newHeight, ImageColor::rgb(0, 0, 0, 0))->getImageResource();
			imagecopyresampled(
				$newImage,
				$this->image,
				0,
				0,
				0,
				0,
				$newWidth,
				$newHeight,
				$this->getWidth(),
				$this->getHeight(),
			);
			$this->image = $newImage;
		}

		if ($width < 0 || $height < 0) {
			imageflip($this->image, $width < 0 ? ($height < 0 ? IMG_FLIP_BOTH : IMG_FLIP_HORIZONTAL) : IMG_FLIP_VERTICAL);
		}

		return $this;
	}


	/**
	 * Calculates dimensions of resized image. Width and height accept pixels or percent.
	 * @param  int-mask-of<self::OrSmaller|self::OrBigger|self::Stretch|self::Cover|self::ShrinkOnly>  $mode
	 * @return array{int<1, max>, int<1, max>}
	 */
	public static function calculateSize(
		int $srcWidth,
		int $srcHeight,
		int|string|null $newWidth,
		int|string|null $newHeight,
		int $mode = self::OrSmaller,
	): array
	{
		if ($newWidth === null) {
		} elseif (self::isPercent($newWidth)) {
			$newWidth = (int) round($srcWidth / 100 * abs($newWidth));
			$percents = true;
		} else {
			$newWidth = abs($newWidth);
		}

		if ($newHeight === null) {
		} elseif (self::isPercent($newHeight)) {
			$newHeight = (int) round($srcHeight / 100 * abs($newHeight));
			$mode |= empty($percents) ? 0 : self::Stretch;
		} else {
			$newHeight = abs($newHeight);
		}

		if ($mode & self::Stretch) { // non-proportional
			if (!$newWidth || !$newHeight) {
				throw new Nette\InvalidArgumentException('For stretching must be both width and height specified.');
			}

			if ($mode & self::ShrinkOnly) {
				$newWidth = min($srcWidth, $newWidth);
				$newHeight = min($srcHeight, $newHeight);
			}
		} else {  // proportional
			if (!$newWidth && !$newHeight) {
				throw new Nette\InvalidArgumentException('At least width or height must be specified.');
			}

			$scale = [];
			if ($newWidth > 0) { // fit width
				$scale[] = $newWidth / $srcWidth;
			}

			if ($newHeight > 0) { // fit height
				$scale[] = $newHeight / $srcHeight;
			}

			if ($mode & self::OrBigger) {
				$scale = [max($scale ?: [1])];
			}

			if ($mode & self::ShrinkOnly) {
				$scale[] = 1;
			}

			$scale = min($scale ?: [1]);
			$newWidth = (int) round($srcWidth * $scale);
			$newHeight = (int) round($srcHeight * $scale);
		}

		return [max((int) $newWidth, 1), max((int) $newHeight, 1)];
	}


	/**
	 * Crops image. Arguments accepts pixels or percent.
	 */
	public function crop(int|string $left, int|string $top, int|string $width, int|string $height): static
	{
		[$r['x'], $r['y'], $r['width'], $r['height']]
			= static::calculateCutout($this->getWidth(), $this->getHeight(), $left, $top, $width, $height);
		if (gd_info()['GD Version'] === 'bundled (2.1.0 compatible)') {
			$this->image = imagecrop($this->image, $r);
			imagesavealpha($this->image, true);
		} else {
			$newImage = static::fromBlank(max(1, $r['width']), max(1, $r['height']), ImageColor::rgb(0, 0, 0, 0))->getImageResource();
			imagecopy($newImage, $this->image, 0, 0, $r['x'], $r['y'], $r['width'], $r['height']);
			$this->image = $newImage;
		}

		return $this;
	}


	/**
	 * Calculates dimensions of cutout in image. Arguments accepts pixels or percent.
	 * @return array{int, int, int, int}
	 */
	public static function calculateCutout(
		int $srcWidth,
		int $srcHeight,
		int|string $left,
		int|string $top,
		int|string $newWidth,
		int|string $newHeight,
	): array
	{
		$newWidth = (int) (self::isPercent($newWidth) ? round($srcWidth / 100 * $newWidth) : $newWidth);
		$newHeight = (int) (self::isPercent($newHeight) ? round($srcHeight / 100 * $newHeight) : $newHeight);
		$left = (int) (self::isPercent($left) ? round(($srcWidth - $newWidth) / 100 * $left) : $left);
		$top = (int) (self::isPercent($top) ? round(($srcHeight - $newHeight) / 100 * $top) : $top);

		if ($left < 0) {
			$newWidth += $left;
			$left = 0;
		}

		if ($top < 0) {
			$newHeight += $top;
			$top = 0;
		}

		$newWidth = min($newWidth, $srcWidth - $left);
		$newHeight = min($newHeight, $srcHeight - $top);
		return [$left, $top, $newWidth, $newHeight];
	}


	/**
	 * Sharpens image a little bit.
	 */
	public function sharpen(): static
	{
		imageconvolution($this->image, [ // my magic numbers ;)
			[-1, -1, -1],
			[-1, 24, -1],
			[-1, -1, -1],
		], 16, 0);
		return $this;
	}


	/**
	 * Puts another image into this image. Left and top accepts pixels or percent.
	 * @param  int<0, 100>  $opacity 0..100
	 */
	public function place(self $image, int|string $left = 0, int|string $top = 0, int $opacity = 100): static
	{
		$opacity = max(0, min(100, $opacity));
		if ($opacity === 0) {
			return $this;
		}

		$width = $image->getWidth();
		$height = $image->getHeight();
		$left = (int) (self::isPercent($left) ? round(($this->getWidth() - $width) / 100 * $left) : $left);
		$top = (int) (self::isPercent($top) ? round(($this->getHeight() - $height) / 100 * $top) : $top);

		$output = $input = $image->image;
		if ($opacity < 100) {
			$tbl = [];
			for ($i = 0; $i < 128; $i++) {
				$tbl[$i] = round(127 - (127 - $i) * $opacity / 100);
			}

			$output = imagecreatetruecolor($width, $height);
			imagealphablending($output, false);
			if (!$image->isTrueColor()) {
				$input = $output;
				imagefilledrectangle($output, 0, 0, $width, $height, (int) imagecolorallocatealpha($output, 0, 0, 0, 127));
				imagecopy($output, $image->image, 0, 0, 0, 0, $width, $height);
			}

			for ($x = 0; $x < $width; $x++) {
				for ($y = 0; $y < $height; $y++) {
					$c = \imagecolorat($input, $x, $y);
					$c = ($c & 0xFFFFFF) + ($tbl[$c >> 24] << 24);
					\imagesetpixel($output, $x, $y, $c);
				}
			}

			imagealphablending($output, true);
		}

		imagecopy(
			$this->image,
			$output,
			$left,
			$top,
			0,
			0,
			$width,
			$height,
		);
		return $this;
	}


	/**
	 * Calculates the bounding box for a TrueType text. Returns keys left, top, width and height.
	 * @param  array<string, mixed>  $options
	 * @return array{left: int, top: int, width: int, height: int}
	 */
	public static function calculateTextBox(
		string $text,
		string $fontFile,
		float $size,
		float $angle = 0,
		array $options = [],
	): array
	{
		self::ensureExtension();
		$box = imagettfbbox($size, $angle, $fontFile, $text, $options);
		return [
			'left' => $minX = min([$box[0], $box[2], $box[4], $box[6]]),
			'top' => $minY = min([$box[1], $box[3], $box[5], $box[7]]),
			'width' => max([$box[0], $box[2], $box[4], $box[6]]) - $minX + 1,
			'height' => max([$box[1], $box[3], $box[5], $box[7]]) - $minY + 1,
		];
	}


	/**
	 * Draws a rectangle using top-left coordinates and dimensions instead of two corner coordinates.
	 */
	public function rectangleWH(int $x, int $y, int $width, int $height, ImageColor $color): void
	{
		if ($width !== 0 && $height !== 0) {
			$this->rectangle($x, $y, $x + $width + ($width > 0 ? -1 : 1), $y + $height + ($height > 0 ? -1 : 1), $color);
		}
	}


	/**
	 * Draws a filled rectangle using top-left coordinates and dimensions instead of two corner coordinates.
	 */
	public function filledRectangleWH(int $x, int $y, int $width, int $height, ImageColor $color): void
	{
		if ($width !== 0 && $height !== 0) {
			$this->filledRectangle($x, $y, $x + $width + ($width > 0 ? -1 : 1), $y + $height + ($height > 0 ? -1 : 1), $color);
		}
	}


	/**
	 * Saves image to the file. Quality is in the range 0..100 for JPEG (default 85), WEBP (default 80) and AVIF (default 30) and 0..9 for PNG (default 9).
	 * @param  ?ImageType::*  $type
	 * @throws ImageException
	 */
	public function save(string $file, ?int $quality = null, ?int $type = null): void
	{
		$type ??= self::extensionToType(pathinfo($file, PATHINFO_EXTENSION));
		$this->output($type, $quality, $file);
	}


	/**
	 * Outputs image to string. Quality is in the range 0..100 for JPEG (default 85), WEBP (default 80) and AVIF (default 30) and 0..9 for PNG (default 9).
	 * @param  ImageType::*  $type
	 */
	public function toString(int $type = ImageType::JPEG, ?int $quality = null): string
	{
		return Helpers::capture(function () use ($type, $quality): void {
			$this->output($type, $quality);
		});
	}


	/**
	 * Outputs image to string.
	 */
	public function __toString(): string
	{
		return $this->toString();
	}


	/**
	 * Outputs image to browser. Quality is in the range 0..100 for JPEG (default 85), WEBP (default 80) and AVIF (default 30) and 0..9 for PNG (default 9).
	 * @param  ImageType::*  $type
	 * @throws ImageException
	 */
	public function send(int $type = ImageType::JPEG, ?int $quality = null): void
	{
		header('Content-Type: ' . self::typeToMimeType($type));
		$this->output($type, $quality);
	}


	/**
	 * Outputs image to browser or file.
	 * @param  ImageType::*  $type
	 * @throws ImageException
	 */
	private function output(int $type, ?int $quality, ?string $file = null): void
	{
		[$defQuality, $min, $max] = match ($type) {
			ImageType::JPEG => [85, 0, 100],
			ImageType::PNG => [9, 0, 9],
			ImageType::GIF => [null, null, null],
			ImageType::WEBP => [80, 0, 100],
			ImageType::AVIF => [30, 0, 100],
			ImageType::BMP => [null, null, null],
			default => throw new Nette\InvalidArgumentException("Unsupported image type '$type'."),
		};

		$args = [$this->image, $file];
		if ($defQuality !== null) {
			$args[] = $quality === null ? $defQuality : max($min, min($max, $quality));
		}

		Callback::invokeSafe('image' . self::Formats[$type], $args, function (string $message) use ($file): void {
			if ($file !== null) {
				@unlink($file);
			}
			throw new ImageException($message);
		});
	}


	/**
	 * Call to undefined method.
	 * @param  mixed[]  $args
	 * @throws Nette\MemberAccessException
	 */
	public function __call(string $name, array $args): mixed
	{
		$function = 'image' . $name;
		if (!function_exists($function)) {
			ObjectHelpers::strictCall(static::class, $name);
		}

		foreach ($args as $key => $value) {
			if ($value instanceof self) {
				$args[$key] = $value->getImageResource();

			} elseif ($value instanceof ImageColor || (is_array($value) && isset($value['red']))) {
				/** @var ImageColor|array{red: int, green: int, blue: int, alpha?: int} $value */
				$args[$key] = $this->resolveColor($value);
			}
		}

		$res = $function($this->image, ...$args);
		return $res instanceof \GdImage
			? $this->setImageResource($res)
			: $res;
	}


	public function __clone()
	{
		ob_start(fn() => '');
		imagepng($this->image, null, 0);
		$this->setImageResource(imagecreatefromstring(ob_get_clean()) ?: throw new Nette\ShouldNotHappenException);
	}


	/** @param-out int|float $num */
	private static function isPercent(int|string &$num): bool
	{
		if (is_string($num) && str_ends_with($num, '%')) {
			$num = (float) substr($num, 0, -1);
			return true;
		} elseif (is_int($num) || $num === (string) (int) $num) {
			$num = (int) $num;
			return false;
		}

		throw new Nette\InvalidArgumentException("Expected dimension in int|string, '$num' given.");
	}


	/**
	 * Prevents serialization.
	 */
	public function __serialize(): array
	{
		throw new Nette\NotSupportedException('You cannot serialize or unserialize ' . self::class . ' instances.');
	}


	/**
	 * Resolves a color to a GD color index for the current image.
	 * @param  ImageColor|array{red: int, green: int, blue: int, alpha?: int}  $color
	 */
	public function resolveColor(ImageColor|array $color): int
	{
		$color = self::normalizeColor($color)->toRGBA();
		return imagecolorallocatealpha($this->image, ...$color) ?: imagecolorresolvealpha($this->image, ...$color);
	}


	/** @param  ImageColor|array{red: int, green: int, blue: int, alpha?: int}  $color */
	private static function normalizeColor(ImageColor|array $color): ImageColor
	{
		return $color instanceof ImageColor
			? $color
			: ImageColor::rgb(
				$color['red'],
				$color['green'],
				$color['blue'],
				(127 - ($color['alpha'] ?? 0)) / 127,
			);
	}


	private static function ensureExtension(): void
	{
		if (!extension_loaded('gd')) {
			throw new Nette\NotSupportedException('PHP extension GD is not loaded.');
		}
	}
}
