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

namespace Ramsey\Uuid\Validator;

use Ramsey\Uuid\Uuid;

use function preg_match;
use function str_replace;

/**
 * GenericValidator validates strings as UUIDs of any variant
 *
 * @immutable
 */
final class GenericValidator implements ValidatorInterface
{
    /**
     * Regular expression pattern for matching a UUID of any variant.
     */
    private const VALID_PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z';

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
        $uuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $uuid);

        /** @phpstan-ignore possiblyImpure.functionCall */
        return $uuid === Uuid::NIL || preg_match('/' . self::VALID_PATTERN . '/Dms', $uuid);
    }
}
