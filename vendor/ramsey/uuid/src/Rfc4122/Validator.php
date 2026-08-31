<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Validator\ValidatorInterface;

use function preg_match;
use function str_replace;

/**
 * Rfc4122\Validator validates strings as UUIDs of the RFC 9562 (formerly RFC 4122) variant
 *
 * @immutable
 */
final class Validator implements ValidatorInterface
{
    private const VALID_PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-'
        . '[1-8][0-9A-Fa-f]{3}-[ABab89][0-9A-Fa-f]{3}-[0-9A-Fa-f]{12}\z';

    /**
     * @return non-empty-string
     */
    public function getPattern(): string
    {
        return self::VALID_PATTERN;
    }

    public function validate(string $uuid): bool
    {
        /** @phpstan-ignore possiblyImpure.functionCall */
        $uuid = strtolower(str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $uuid));

        /** @phpstan-ignore possiblyImpure.functionCall */
        return $uuid === Uuid::NIL || $uuid === Uuid::MAX || preg_match('/' . self::VALID_PATTERN . '/Dms', $uuid);
    }
}
