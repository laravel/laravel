<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Requirement;

/*
 * A collection of universal regular-expression constants to use as route parameter requirements.
 */
enum Requirement
{
    public const ASCII_SLUG = '[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*'; // symfony/string AsciiSlugger default implementation
    public const CATCH_ALL = '.+';
    public const DATE_YMD = '[0-9]{4}-(?:0[1-9]|1[012])-(?:0[1-9]|[12][0-9]|(?<!02-)3[01])'; // YYYY-MM-DD
    public const DIGITS = '[0-9]+';
    public const MONGODB_ID = '[0-9a-f]{24}';
    public const POSITIVE_INT = '[1-9][0-9]*';
    public const UID_BASE32 = '[0-9A-HJKMNP-TV-Z]{26}';
    public const UID_BASE58 = '[1-9A-HJ-NP-Za-km-z]{22}';
    public const UID_RFC4122 = '[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}';
    public const UID_RFC9562 = self::UID_RFC4122;
    public const ULID = '[0-7][0-9A-HJKMNP-TV-Z]{25}';
    public const UUID = '[0-9a-f]{8}-[0-9a-f]{4}-[13-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    public const UUID_V1 = '[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    public const UUID_V3 = '[0-9a-f]{8}-[0-9a-f]{4}-3[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    public const UUID_V4 = '[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    public const UUID_V5 = '[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    public const UUID_V6 = '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    public const UUID_V7 = '[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    public const UUID_V8 = '[0-9a-f]{8}-[0-9a-f]{4}-8[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
}
