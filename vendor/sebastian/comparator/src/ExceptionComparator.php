<?php declare(strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Comparator;

use function assert;
use Exception;

/**
 * Compares Exception instances for equality.
 */
final class ExceptionComparator extends ObjectComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return $expected instanceof Exception && $actual instanceof Exception;
    }

    /**
     * @return array<mixed>
     */
    protected function toArray(object $object): array
    {
        assert($object instanceof Exception);

        $array = parent::toArray($object);

        unset(
            $array['file'],
            $array['line'],
            $array['trace'],
            $array['string'],
            $array['xdebug_message'],
        );

        return $array;
    }
}
