<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Uri\Idna;

enum Error: int
{
    case NONE                   = 0;
    case EMPTY_LABEL            = 1;
    case LABEL_TOO_LONG         = 2;
    case DOMAIN_NAME_TOO_LONG   = 4;
    case LEADING_HYPHEN         = 8;
    case TRAILING_HYPHEN        = 0x10;
    case HYPHEN_3_4             = 0x20;
    case LEADING_COMBINING_MARK = 0x40;
    case DISALLOWED             = 0x80;
    case PUNYCODE               = 0x100;
    case LABEL_HAS_DOT          = 0x200;
    case INVALID_ACE_LABEL      = 0x400;
    case BIDI                   = 0x800;
    case CONTEXTJ               = 0x1000;
    case CONTEXTO_PUNCTUATION   = 0x2000;
    case CONTEXTO_DIGITS        = 0x4000;

    public function description(): string
    {
        return match ($this) {
            self::NONE => 'No error has occurred',
            self::EMPTY_LABEL => 'a non-final domain name label (or the whole domain name) is empty',
            self::LABEL_TOO_LONG => 'a domain name label is longer than 63 bytes',
            self::DOMAIN_NAME_TOO_LONG => 'a domain name is longer than 255 bytes in its storage form',
            self::LEADING_HYPHEN => 'a label starts with a hyphen-minus ("-")',
            self::TRAILING_HYPHEN => 'a label ends with a hyphen-minus ("-")',
            self::HYPHEN_3_4 => 'a label contains hyphen-minus ("-") in the third and fourth positions',
            self::LEADING_COMBINING_MARK => 'a label starts with a combining mark',
            self::DISALLOWED => 'a label or domain name contains disallowed characters',
            self::PUNYCODE => 'a label starts with "xn--" but does not contain valid Punycode',
            self::LABEL_HAS_DOT => 'a label contains a dot=full stop',
            self::INVALID_ACE_LABEL => 'An ACE label does not contain a valid label string',
            self::BIDI => 'a label does not meet the IDNA BiDi requirements (for right-to-left characters)',
            self::CONTEXTJ => 'a label does not meet the IDNA CONTEXTJ requirements',
            self::CONTEXTO_DIGITS => 'a label does not meet the IDNA CONTEXTO requirements for digits',
            self::CONTEXTO_PUNCTUATION => 'a label does not meet the IDNA CONTEXTO requirements for punctuation characters. Some punctuation characters "Would otherwise have been DISALLOWED" but are allowed in certain contexts',
        };
    }

    public static function filterByErrorBytes(int $errors): array
    {
        return array_values(
            array_filter(
                self::cases(),
                fn (self $error): bool => 0 !== ($error->value & $errors)
            )
        );
    }
}
