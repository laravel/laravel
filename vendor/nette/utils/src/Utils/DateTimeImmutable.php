<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use function array_merge, checkdate, implode, is_int, is_string, preg_match, preg_replace_callback, sprintf, trim;


/**
 * Extends PHP's DateTimeImmutable with strict validation and additional factory methods.
 * Invalid dates and times are rejected with an exception instead of being silently adjusted.
 * All modifications return a new instance, the original object never changes.
 */
class DateTimeImmutable extends \DateTimeImmutable implements \JsonSerializable
{
	/** matches relative sub-day parts (minutes, seconds, ...) that must be applied in UTC to be DST-safe */
	private const RelativePattern = '/[+-]?\s*\d+\s+((microsecond|millisecond|[mµu]sec)s?|[mµ]s|sec(ond)?s?|min(ute)?s?|hours?)(\s+ago)?\b/iu';


	/**
	 * Creates a DateTimeImmutable object from a string, UNIX timestamp, or other DateTimeInterface object.
	 * @throws \Exception if the date and time are not valid.
	 */
	public static function from(string|int|\DateTimeInterface|null $time): static
	{
		if ($time instanceof \DateTimeInterface) {
			return static::createFromInterface($time);
		} elseif (is_int($time)) {
			return (new static)->setTimestamp($time);
		} else { // textual or null
			return new static((string) $time);
		}
	}


	/**
	 * Creates DateTimeImmutable object.
	 * @throws \Exception if the date and time are not valid.
	 */
	public static function fromParts(
		int $year,
		int $month,
		int $day,
		int $hour = 0,
		int $minute = 0,
		float $second = 0.0,
	): static
	{
		$sec = (int) floor($second);
		return (new static)
			->setDate($year, $month, $day)
			->setTime($hour, $minute, $sec, (int) round(($second - $sec) * 1e6));
	}


	/**
	 * Returns a new DateTimeImmutable object formatted according to the specified format.
	 */
	public static function createFromFormat(
		string $format,
		string $datetime,
		string|\DateTimeZone|null $timezone = null,
	): static|false
	{
		if (is_string($timezone)) {
			$timezone = new \DateTimeZone($timezone);
		}

		$date = parent::createFromFormat($format, $datetime, $timezone);
		return $date ? static::from($date) : false;
	}


	public function __construct(string $datetime = 'now', ?\DateTimeZone $timezone = null)
	{
		if (preg_match(self::RelativePattern, $datetime)) {
			// sub-day relative parts must be applied in UTC, which cannot happen inside a constructor => resolve & re-parse
			$result = self::resolve(null, $datetime, $timezone);
			parent::__construct($result->format('Y-m-d H:i:s.u'), $result->getTimezone());
		} else {
			parent::__construct($datetime, $timezone);
			self::handleErrors($datetime);
		}
	}


	public function modify(string $modifier): static
	{
		return static::createFromInterface(self::resolve($this, $modifier, null));
	}


	public function setDate(int $year, int $month, int $day): static
	{
		if (!checkdate($month, $day, $year)) {
			throw new \Exception(sprintf('The date %04d-%02d-%02d is not valid.', $year, $month, $day));
		}
		return parent::setDate($year, $month, $day);
	}


	public function setTime(int $hour, int $minute, int $second = 0, int $microsecond = 0): static
	{
		if (
			$hour < 0 || $hour > 23
			|| $minute < 0 || $minute > 59
			|| $second < 0 || $second >= 60
			|| $microsecond < 0 || $microsecond >= 1_000_000
		) {
			throw new \Exception(sprintf('The time %02d:%02d:%08.5F is not valid.', $hour, $minute, $second + $microsecond / 1_000_000));
		}
		return parent::setTime($hour, $minute, $second, $microsecond);
	}


	/**
	 * Splits the input into absolute and relative parts and returns the resulting instant. Relative sub-day
	 * parts are applied in UTC so that crossing a DST boundary does not shift the result.
	 */
	private static function resolve(?\DateTimeInterface $base, string $input, ?\DateTimeZone $timezone): \DateTimeImmutable
	{
		$relPart = '';
		$absPart = preg_replace_callback(
			self::RelativePattern,
			function ($m) use (&$relPart) {
				$relPart .= $m[0] . ' ';
				return '';
			},
			$input,
		);

		if ($base === null) {
			$result = new \DateTimeImmutable($absPart, $timezone);
			self::handleErrors($input);
		} else {
			$result = \DateTimeImmutable::createFromInterface($base);
			if (trim($absPart) !== '') {
				$modified = @$result->modify($absPart); // @ - on PHP 8.2 an invalid modifier emits a warning and returns false instead of throwing; handleErrors() turns it into an exception
				self::handleErrors($input);
				$result = $modified ?: $result;
			}
		}

		if ($relPart !== '') {
			$timezone ??= $result->getTimezone();
			$result = $result
				->setTimezone(new \DateTimeZone('UTC'))
				->modify($relPart)
				->setTimezone($timezone);
			self::handleErrors($input);
		}

		return $result;
	}


	/**
	 * Returns JSON representation in ISO 8601 (used by JavaScript).
	 */
	public function jsonSerialize(): string
	{
		return $this->format('c');
	}


	/**
	 * Returns the date and time in the format 'Y-m-d H:i:s'.
	 */
	public function __toString(): string
	{
		return $this->format('Y-m-d H:i:s');
	}


	private static function handleErrors(string $value): void
	{
		$errors = self::getLastErrors();
		$errors = array_merge($errors['errors'] ?? [], $errors['warnings'] ?? []);
		if ($errors) {
			throw new \Exception(implode(', ', $errors) . " '$value'");
		}
	}
}
