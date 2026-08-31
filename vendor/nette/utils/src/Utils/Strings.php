<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use JetBrains\PhpStorm\Language;
use Nette;
use function array_keys, array_map, array_shift, array_values, bin2hex, class_exists, defined, extension_loaded, function_exists, htmlspecialchars, htmlspecialchars_decode, iconv, iconv_strlen, iconv_substr, implode, in_array, is_array, is_callable, is_int, is_object, is_string, key, max, mb_convert_case, mb_strlen, mb_strtolower, mb_strtoupper, mb_substr, pack, preg_last_error, preg_last_error_msg, preg_quote, preg_replace, str_contains, str_ends_with, str_repeat, str_replace, str_starts_with, strlen, strpos, strrev, strrpos, strtolower, strtoupper, strtr, substr, trim, unpack;
use const ENT_IGNORE, ENT_NOQUOTES, ICONV_IMPL, MB_CASE_TITLE, PHP_EOL, PREG_OFFSET_CAPTURE, PREG_PATTERN_ORDER, PREG_SET_ORDER, PREG_SPLIT_DELIM_CAPTURE, PREG_SPLIT_NO_EMPTY, PREG_SPLIT_OFFSET_CAPTURE, PREG_UNMATCHED_AS_NULL;


/**
 * String tools library.
 */
class Strings
{
	use Nette\StaticClass;

	public const TrimCharacters = " \t\n\r\0\x0B\u{A0}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200A}\u{200B}\u{2028}\u{3000}";

	#[\Deprecated('use Strings::TrimCharacters')]
	public const TRIM_CHARACTERS = self::TrimCharacters;


	/**
	 * @deprecated use Nette\Utils\Validators::isUnicode()
	 */
	public static function checkEncoding(string $s): bool
	{
		return $s === self::fixEncoding($s);
	}


	/**
	 * Removes all invalid UTF-8 characters from a string.
	 */
	public static function fixEncoding(string $s): string
	{
		// removes xD800-xDFFF, x110000 and higher
		return htmlspecialchars_decode(htmlspecialchars($s, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8'), ENT_NOQUOTES);
	}


	/**
	 * Returns a specific character in UTF-8 from code point (number in range 0x0000..D7FF or 0xE000..10FFFF).
	 * @throws Nette\InvalidArgumentException if code point is not in valid range
	 */
	public static function chr(int $code): string
	{
		if ($code < 0 || ($code >= 0xD800 && $code <= 0xDFFF) || $code > 0x10FFFF) {
			throw new Nette\InvalidArgumentException('Code point must be in range 0x0 to 0xD7FF or 0xE000 to 0x10FFFF.');
		} elseif (!extension_loaded('iconv')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires ICONV extension that is not loaded.');
		}

		$res = iconv('UTF-32BE', 'UTF-8//IGNORE', pack('N', $code));
		return $res === false ? throw new Nette\ShouldNotHappenException : $res;
	}


	/**
	 * Returns a code point of specific character in UTF-8 (number in range 0x0000..D7FF or 0xE000..10FFFF).
	 */
	public static function ord(string $c): int
	{
		if (!extension_loaded('iconv')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires ICONV extension that is not loaded.');
		}

		$tmp = iconv('UTF-8', 'UTF-32BE//IGNORE', $c);
		if ($tmp === false || $tmp === '') {
			throw new Nette\InvalidArgumentException('Invalid UTF-8 character "' . ($c === '' ? '' : '\x' . strtoupper(bin2hex($c))) . '".');
		}

		return unpack('N', $tmp)[1] ?? throw new Nette\ShouldNotHappenException;
	}


	/**
	 * @deprecated use str_starts_with()
	 */
	public static function startsWith(string $haystack, string $needle): bool
	{
		return str_starts_with($haystack, $needle);
	}


	/**
	 * @deprecated use str_ends_with()
	 */
	public static function endsWith(string $haystack, string $needle): bool
	{
		return str_ends_with($haystack, $needle);
	}


	/**
	 * @deprecated use str_contains()
	 */
	public static function contains(string $haystack, string $needle): bool
	{
		return str_contains($haystack, $needle);
	}


	/**
	 * Returns a part of UTF-8 string specified by starting position and length. If start is negative,
	 * the returned string will start at the start'th character from the end of string.
	 */
	public static function substring(string $s, int $start, ?int $length = null): string
	{
		if (function_exists('mb_substr')) {
			return mb_substr($s, $start, $length, 'UTF-8'); // MB is much faster
		} elseif (!extension_loaded('iconv')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires extension ICONV or MBSTRING, neither is loaded.');
		} elseif ($length === null) {
			$length = self::length($s);
		} elseif ($start < 0 && $length < 0) {
			$start += self::length($s); // unifies iconv_substr behavior with mb_substr
		}

		$res = iconv_substr($s, $start, $length, 'UTF-8');
		return $res === false ? throw new Nette\InvalidStateException('iconv_substr() failed.') : $res;
	}


	/**
	 * Removes control characters, normalizes line breaks to `\n`, removes leading and trailing blank lines,
	 * trims end spaces on lines, normalizes UTF-8 to the normal form of NFC.
	 */
	public static function normalize(string $s): string
	{
		// convert to compressed normal form (NFC)
		if (class_exists('Normalizer', autoload: false) && ($n = \Normalizer::normalize($s, \Normalizer::FORM_C)) !== false) {
			$s = $n;
		}

		$s = self::unixNewLines($s);

		// remove control characters; leave \t + \n
		$s = self::pcre('preg_replace', ['#[\x00-\x08\x0B-\x1F\x7F-\x9F]+#u', '', $s]);

		// right trim
		$s = self::pcre('preg_replace', ['#[\t ]+$#m', '', $s]);

		// leading and trailing blank lines
		$s = trim($s, "\n");

		return $s;
	}


	/** @deprecated use Strings::unixNewLines() */
	public static function normalizeNewLines(string $s): string
	{
		return self::unixNewLines($s);
	}


	/**
	 * Converts line endings to \n used on Unix-like systems.
	 * Line endings are: \n, \r, \r\n, U+2028 line separator, U+2029 paragraph separator.
	 */
	public static function unixNewLines(string $s): string
	{
		return preg_replace("~\r\n?|\u{2028}|\u{2029}~", "\n", $s);
	}


	/**
	 * Converts line endings to platform-specific, i.e. \r\n on Windows and \n elsewhere.
	 * Line endings are: \n, \r, \r\n, U+2028 line separator, U+2029 paragraph separator.
	 */
	public static function platformNewLines(string $s): string
	{
		return preg_replace("~\r\n?|\n|\u{2028}|\u{2029}~", PHP_EOL, $s);
	}


	/**
	 * Converts UTF-8 string to ASCII, ie removes diacritics etc.
	 */
	public static function toAscii(string $s): string
	{
		if (!extension_loaded('intl')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires INTL extension that is not loaded.');
		}

		$iconv = defined('ICONV_IMPL') ? trim(ICONV_IMPL, '"\'') : null;

		// remove control characters and check UTF-8 validity
		$s = self::pcre('preg_replace', ['#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s]);

		// transliteration (by Transliterator and iconv) is not optimal, replace some characters directly
		$s = strtr($s, ["\u{201E}" => '"', "\u{201C}" => '"', "\u{201D}" => '"', "\u{201A}" => "'", "\u{2018}" => "'", "\u{2019}" => "'", "\u{B0}" => '^', "\u{42F}" => 'Ya', "\u{44F}" => 'ya', "\u{42E}" => 'Yu', "\u{44E}" => 'yu', "\u{c4}" => 'Ae', "\u{d6}" => 'Oe', "\u{dc}" => 'Ue', "\u{1e9e}" => 'Ss', "\u{e4}" => 'ae', "\u{f6}" => 'oe', "\u{fc}" => 'ue', "\u{df}" => 'ss']); // „ “ ” ‚ ‘ ’ ° Я я Ю ю Ä Ö Ü ẞ ä ö ü ß
		if ($iconv !== 'libiconv') {
			$s = strtr($s, ["\u{AE}" => '(R)', "\u{A9}" => '(c)', "\u{2026}" => '...', "\u{AB}" => '<<', "\u{BB}" => '>>', "\u{A3}" => 'lb', "\u{A5}" => 'yen', "\u{B2}" => '^2', "\u{B3}" => '^3', "\u{B5}" => 'u', "\u{B9}" => '^1', "\u{BA}" => 'o', "\u{BF}" => '?', "\u{2CA}" => "'", "\u{2CD}" => '_', "\u{2DD}" => '"', "\u{1FEF}" => '', "\u{20AC}" => 'EUR', "\u{2122}" => 'TM', "\u{212E}" => 'e', "\u{2190}" => '<-', "\u{2191}" => '^', "\u{2192}" => '->', "\u{2193}" => 'V', "\u{2194}" => '<->']); // ® © … « » £ ¥ ² ³ µ ¹ º ¿ ˊ ˍ ˝ ` € ™ ℮ ← ↑ → ↓ ↔
		}

		$s = \Transliterator::create('Any-Latin; Latin-ASCII')?->transliterate($s)
			?? throw new Nette\InvalidStateException('Transliterator::transliterate() failed.');

		// use iconv because The transliterator leaves some characters out of ASCII, eg → ʾ
		if ($iconv === 'glibc') {
			$s = strtr($s, '?', "\x01"); // temporarily hide ? to distinguish them from the garbage that iconv creates
			$s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
			if ($s === false) {
				throw new Nette\InvalidStateException('iconv() failed.');
			}

			$s = str_replace(['?', "\x01"], ['', '?'], $s); // remove garbage and restore ? characters
		} elseif ($iconv === 'libiconv') {
			$s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
			if ($s === false) {
				throw new Nette\InvalidStateException('iconv() failed.');
			}
		} else { // null or 'unknown' (#216)
			$s = self::pcre('preg_replace', ['#[^\x00-\x7F]++#', '', $s]); // remove non-ascii chars
		}

		return $s;
	}


	/**
	 * Modifies the UTF-8 string to the form used in the URL, ie removes diacritics and replaces all characters
	 * except letters of the English alphabet and numbers with a hyphens.
	 */
	public static function webalize(string $s, ?string $charlist = null, bool $lower = true): string
	{
		$s = self::toAscii($s);
		if ($lower) {
			$s = strtolower($s);
		}

		$s = self::pcre('preg_replace', ['#[^a-z0-9' . ($charlist !== null ? preg_quote($charlist, '#') : '') . ']+#i', '-', $s]);
		$s = trim($s, '-');
		return $s;
	}


	/**
	 * Truncates a UTF-8 string to given maximal length, while trying not to split whole words. Only if the string is truncated,
	 * an ellipsis (or something else set with third argument) is appended to the string.
	 */
	public static function truncate(string $s, int $maxLen, string $append = "\u{2026}"): string
	{
		if (self::length($s) > $maxLen) {
			$maxLen -= self::length($append);
			if ($maxLen < 1) {
				return $append;

			} elseif ($matches = self::match($s, '#^.{1,' . $maxLen . '}(?=[\s\x00-/:-@\[-`{-~])#us')) {
				return $matches[0] . $append;

			} else {
				return self::substring($s, 0, $maxLen) . $append;
			}
		}

		return $s;
	}


	/**
	 * Indents a multiline text from the left. Second argument sets how many indentation chars should be used,
	 * while the indent itself is the third argument (*tab* by default).
	 */
	public static function indent(string $s, int $level = 1, string $chars = "\t"): string
	{
		if ($level > 0) {
			$s = self::replace($s, '#(?:^|[\r\n]+)(?=[^\r\n])#', '$0' . str_repeat($chars, $level));
		}

		return $s;
	}


	/**
	 * Converts all characters of UTF-8 string to lower case.
	 */
	public static function lower(string $s): string
	{
		return mb_strtolower($s, 'UTF-8');
	}


	/**
	 * Converts the first character of a UTF-8 string to lower case and leaves the other characters unchanged.
	 */
	public static function firstLower(string $s): string
	{
		return self::lower(self::substring($s, 0, 1)) . self::substring($s, 1);
	}


	/**
	 * Converts all characters of a UTF-8 string to upper case.
	 */
	public static function upper(string $s): string
	{
		return mb_strtoupper($s, 'UTF-8');
	}


	/**
	 * Converts the first character of a UTF-8 string to upper case and leaves the other characters unchanged.
	 */
	public static function firstUpper(string $s): string
	{
		return self::upper(self::substring($s, 0, 1)) . self::substring($s, 1);
	}


	/**
	 * Converts the first character of every word of a UTF-8 string to upper case and the others to lower case.
	 */
	public static function capitalize(string $s): string
	{
		return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
	}


	/**
	 * Compares two UTF-8 strings or their parts, without taking character case into account. If length is null, whole strings are compared,
	 * if it is negative, the corresponding number of characters from the end of the strings is compared,
	 * otherwise the appropriate number of characters from the beginning is compared.
	 */
	public static function compare(string $left, string $right, ?int $length = null): bool
	{
		if (class_exists('Normalizer', autoload: false)) {
			$left = \Normalizer::normalize($left, \Normalizer::FORM_D) ?: $left; // form NFD is faster, false on invalid UTF-8
			$right = \Normalizer::normalize($right, \Normalizer::FORM_D) ?: $right; // form NFD is faster, false on invalid UTF-8
		}

		if ($length < 0) {
			$left = self::substring($left, $length, -$length);
			$right = self::substring($right, $length, -$length);
		} elseif ($length !== null) {
			$left = self::substring($left, 0, $length);
			$right = self::substring($right, 0, $length);
		}

		return self::lower($left) === self::lower($right);
	}


	/**
	 * Finds the common prefix of strings or returns empty string if the prefix was not found.
	 * @param  string[]  $strings
	 */
	public static function findPrefix(array $strings): string
	{
		$first = array_shift($strings);
		if ($first === null) {
			return '';
		}

		for ($i = 0; $i < strlen($first); $i++) {
			foreach ($strings as $s) {
				if (!isset($s[$i]) || $first[$i] !== $s[$i]) {
					while ($i && $first[$i - 1] >= "\x80" && $first[$i] >= "\x80" && $first[$i] < "\xC0") {
						$i--;
					}

					return substr($first, 0, $i);
				}
			}
		}

		return $first;
	}


	/**
	 * Returns number of characters (not bytes) in UTF-8 string.
	 * That is the number of Unicode code points which may differ from the number of graphemes.
	 */
	public static function length(string $s): int
	{
		return match (true) {
			extension_loaded('mbstring') => (int) mb_strlen($s, 'UTF-8'),
			extension_loaded('iconv') => (int) iconv_strlen($s, 'UTF-8'),
			default => strlen((string) preg_replace('#[\x80-\xBF]#', '', $s)), // strips UTF-8 continuation bytes
		};
	}


	/**
	 * Removes all left and right side spaces (or the characters passed as second argument) from a UTF-8 encoded string.
	 */
	public static function trim(string $s, string $charlist = self::TrimCharacters): string
	{
		$charlist = preg_quote($charlist, '#');
		return self::replace($s, '#^[' . $charlist . ']+|[' . $charlist . ']+$#Du');
	}


	/**
	 * Pads a UTF-8 string to given length by prepending the $pad string to the beginning.
	 * @param  non-empty-string  $pad
	 */
	public static function padLeft(string $s, int $length, string $pad = ' '): string
	{
		$length = max(0, $length - self::length($s));
		$padLen = self::length($pad);
		return str_repeat($pad, (int) ($length / $padLen)) . self::substring($pad, 0, $length % $padLen) . $s;
	}


	/**
	 * Pads UTF-8 string to given length by appending the $pad string to the end.
	 * @param  non-empty-string  $pad
	 */
	public static function padRight(string $s, int $length, string $pad = ' '): string
	{
		$length = max(0, $length - self::length($s));
		$padLen = self::length($pad);
		return $s . str_repeat($pad, (int) ($length / $padLen)) . self::substring($pad, 0, $length % $padLen);
	}


	/**
	 * Reverses UTF-8 string.
	 */
	public static function reverse(string $s): string
	{
		if (!extension_loaded('iconv')) {
			throw new Nette\NotSupportedException(__METHOD__ . '() requires ICONV extension that is not loaded.');
		}

		$tmp = iconv('UTF-8', 'UTF-32BE', $s);
		return $tmp === false
			? throw new Nette\InvalidStateException('iconv() failed.')
			: (string) iconv('UTF-32LE', 'UTF-8', strrev($tmp));
	}


	/**
	 * Returns part of $haystack before $nth occurence of $needle or returns null if the needle was not found.
	 * Negative value means searching from the end.
	 */
	public static function before(string $haystack, string $needle, int $nth = 1): ?string
	{
		$pos = self::pos($haystack, $needle, $nth);
		return $pos === null
			? null
			: substr($haystack, 0, $pos);
	}


	/**
	 * Returns part of $haystack after $nth occurence of $needle or returns null if the needle was not found.
	 * Negative value means searching from the end.
	 */
	public static function after(string $haystack, string $needle, int $nth = 1): ?string
	{
		$pos = self::pos($haystack, $needle, $nth);
		return $pos === null
			? null
			: substr($haystack, $pos + strlen($needle));
	}


	/**
	 * Returns position in characters of $nth occurence of $needle in $haystack or null if the $needle was not found.
	 * Negative value of `$nth` means searching from the end.
	 */
	public static function indexOf(string $haystack, string $needle, int $nth = 1): ?int
	{
		$pos = self::pos($haystack, $needle, $nth);
		return $pos === null
			? null
			: self::length(substr($haystack, 0, $pos));
	}


	/**
	 * Returns position in characters of $nth occurence of $needle in $haystack or null if the needle was not found.
	 */
	private static function pos(string $haystack, string $needle, int $nth = 1): ?int
	{
		if (!$nth) {
			return null;
		} elseif ($nth > 0) {
			if ($needle === '') {
				return 0;
			}

			$pos = 0;
			while (($pos = strpos($haystack, $needle, $pos)) !== false && --$nth) {
				$pos++;
			}
		} else {
			$len = strlen($haystack);
			if ($needle === '') {
				return $len;
			} elseif ($len === 0) {
				return null;
			}

			$pos = $len - 1;
			while (($pos = strrpos($haystack, $needle, $pos - $len)) !== false && ++$nth) {
				$pos--;
			}
		}

		return Helpers::falseToNull($pos);
	}


	/**
	 * Splits the string by a regular expression. Expressions in parentheses will be captured and returned as well.
	 * @return list<string>
	 */
	public static function split(
		string $subject,
		#[Language('RegExp')]
		string $pattern,
		bool|int $captureOffset = false,
		bool $skipEmpty = false,
		int $limit = -1,
		bool $utf8 = false,
	): array
	{
		$flags = is_int($captureOffset)  // back compatibility
			? $captureOffset
			: ($captureOffset ? PREG_SPLIT_OFFSET_CAPTURE : 0) | ($skipEmpty ? PREG_SPLIT_NO_EMPTY : 0);

		$pattern .= $utf8 ? 'u' : '';
		$m = self::pcre('preg_split', [$pattern, $subject, $limit, $flags | PREG_SPLIT_DELIM_CAPTURE]);
		return $utf8 && $captureOffset
			? self::bytesToChars($subject, [$m])[0]
			: $m;
	}


	/**
	 * Searches the string for the first match of the regular expression and returns
	 * an array with the found expression and individual subexpressions, or null.
	 * @return ?array<string>
	 */
	public static function match(
		string $subject,
		#[Language('RegExp')]
		string $pattern,
		bool|int $captureOffset = false,
		int $offset = 0,
		bool $unmatchedAsNull = false,
		bool $utf8 = false,
	): ?array
	{
		$flags = is_int($captureOffset) // back compatibility
			? $captureOffset
			: ($captureOffset ? PREG_OFFSET_CAPTURE : 0) | ($unmatchedAsNull ? PREG_UNMATCHED_AS_NULL : 0);

		if ($utf8) {
			$offset = strlen(self::substring($subject, 0, $offset));
			$pattern .= 'u';
		}

		$m = [];
		if ($offset > strlen($subject)) {
			return null;
		} elseif (!self::pcre('preg_match', [$pattern, $subject, &$m, $flags, $offset])) {
			return null;
		} elseif ($utf8 && $captureOffset) {
			return self::bytesToChars($subject, [$m])[0];
		} else {
			return $m;
		}
	}


	/**
	 * Searches the string for all occurrences matching the regular expression and returns
	 * an array of arrays containing the found expression and each subexpression.
	 * @return ($lazy is true ? \Generator<int, array<string>> : list<array<string>>)
	 */
	public static function matchAll(
		string $subject,
		#[Language('RegExp')]
		string $pattern,
		bool|int $captureOffset = false,
		int $offset = 0,
		bool $unmatchedAsNull = false,
		bool $patternOrder = false,
		bool $utf8 = false,
		bool $lazy = false,
	): array|\Generator
	{
		if ($utf8) {
			$offset = strlen(self::substring($subject, 0, $offset));
			$pattern .= 'u';
		}

		if ($lazy) {
			$flags = PREG_OFFSET_CAPTURE | ($unmatchedAsNull ? PREG_UNMATCHED_AS_NULL : 0);
			return (function () use ($utf8, $captureOffset, $flags, $subject, $pattern, $offset) {
				$counter = 0;
				$m = [];
				while (
					$offset <= strlen($subject) - ($counter ? 1 : 0)
					&& self::pcre('preg_match', [$pattern, $subject, &$m, $flags, $offset])
				) {
					/** @var list<array{string, int}> $m */
					$offset = $m[0][1] + max(1, strlen($m[0][0]));
					if (!$captureOffset) {
						$m = array_map(fn($item) => $item[0], $m);
					} elseif ($utf8) {
						$m = self::bytesToChars($subject, [$m])[0];
					}
					yield $counter++ => $m;
				}
			})();
		}

		if ($offset > strlen($subject)) {
			return [];
		}

		$flags = is_int($captureOffset) // back compatibility
			? $captureOffset
			: ($captureOffset ? PREG_OFFSET_CAPTURE : 0) | ($unmatchedAsNull ? PREG_UNMATCHED_AS_NULL : 0) | ($patternOrder ? PREG_PATTERN_ORDER : 0);

		$m = [];
		self::pcre('preg_match_all', [
			$pattern, $subject, &$m,
			($flags & PREG_PATTERN_ORDER) ? $flags : ($flags | PREG_SET_ORDER),
			$offset,
		]);
		return $utf8 && $captureOffset
			? self::bytesToChars($subject, $m)
			: $m;
	}


	/**
	 * Replaces all occurrences matching the regular expression $pattern, which can be a string or array in the form `pattern => replacement`.
	 * @param  string|array<string, string>  $pattern
	 */
	public static function replace(
		string $subject,
		#[Language('RegExp')]
		string|array $pattern,
		string|callable $replacement = '',
		int $limit = -1,
		bool $captureOffset = false,
		bool $unmatchedAsNull = false,
		bool $utf8 = false,
	): string
	{
		if (is_object($replacement) || is_array($replacement)) {
			if (!is_callable($replacement, false, $textual)) {
				throw new Nette\InvalidStateException("Callback '$textual' is not callable.");
			}

			$flags = ($captureOffset ? PREG_OFFSET_CAPTURE : 0) | ($unmatchedAsNull ? PREG_UNMATCHED_AS_NULL : 0);
			if ($utf8) {
				$pattern = is_array($pattern) ? array_map(fn($item) => $item . 'u', $pattern) : $pattern . 'u';
				if ($captureOffset) {
					$replacement = fn($m) => $replacement(self::bytesToChars($subject, [$m])[0]);
				}
			}

			return self::pcre('preg_replace_callback', [$pattern, $replacement, $subject, $limit, 0, $flags]);

		} elseif (is_array($pattern) && is_string(key($pattern))) {
			$replacement = array_values($pattern);
			$pattern = array_keys($pattern);
		}

		if ($utf8) {
			$pattern = array_map(fn($item) => $item . 'u', (array) $pattern);
		}

		return self::pcre('preg_replace', [$pattern, $replacement, $subject, $limit]);
	}


	/**
	 * @param  list<array<array{string, int}>>  $groups
	 * @return list<array<array{string, int}>>
	 */
	private static function bytesToChars(string $s, array $groups): array
	{
		$lastBytes = $lastChars = 0;
		foreach ($groups as &$matches) {
			foreach ($matches as &$match) {
				if ($match[1] > $lastBytes) {
					$lastChars += self::length(substr($s, $lastBytes, $match[1] - $lastBytes));
				} elseif ($match[1] < $lastBytes) {
					$lastChars -= self::length(substr($s, $match[1], $lastBytes - $match[1]));
				}

				$lastBytes = $match[1];
				$match[1] = $lastChars;
			}
		}

		return $groups;
	}


	/**
	 * @param  callable-string  $func
	 * @param  list<mixed>  $args
	 * @internal
	 */
	public static function pcre(string $func, array $args): mixed
	{
		$res = Callback::invokeSafe($func, $args, function (string $message) use ($args): void {
			// compile-time error, not detectable by preg_last_error
			throw new RegexpException($message . ' in pattern: ' . implode(' or ', (array) $args[0]));
		});

		if (($code = preg_last_error()) // run-time error, but preg_last_error & return code are liars
			&& ($res === null || !in_array($func, ['preg_filter', 'preg_replace_callback', 'preg_replace'], strict: true))
		) {
			throw new RegexpException(preg_last_error_msg()
				. ' (pattern: ' . implode(' or ', (array) $args[0]) . ')', $code);
		}

		return $res;
	}
}
