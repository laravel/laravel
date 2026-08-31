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

use Symfony\Component\Routing\Exception\InvalidArgumentException;

final class EnumRequirement implements \Stringable
{
    private string $requirement;

    /**
     * @template T of \BackedEnum
     *
     * @param class-string<T>|list<T> $cases
     */
    public function __construct(string|array $cases = [])
    {
        if (\is_string($cases)) {
            if (!is_subclass_of($cases, \BackedEnum::class, true)) {
                throw new InvalidArgumentException(\sprintf('"%s" is not a "BackedEnum" class.', $cases));
            }

            $cases = $cases::cases();
        } else {
            $class = null;

            foreach ($cases as $case) {
                if (!$case instanceof \BackedEnum) {
                    throw new InvalidArgumentException(\sprintf('Case must be a "BackedEnum" instance, "%s" given.', get_debug_type($case)));
                }

                $class ??= $case::class;

                if (!$case instanceof $class) {
                    throw new InvalidArgumentException(\sprintf('"%s::%s" is not a case of "%s".', get_debug_type($case), $case->name, $class));
                }
            }
        }

        $this->requirement = implode('|', array_map(static fn ($e) => preg_quote($e->value), $cases));
    }

    public function __toString(): string
    {
        return $this->requirement;
    }
}
