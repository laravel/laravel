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

use function array_keys;
use function assert;
use function str_starts_with;
use PHPUnit\Framework\MockObject\Stub;

/**
 * Compares PHPUnit\Framework\MockObject\MockObject instances for equality.
 */
final class MockObjectComparator extends ObjectComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return $expected instanceof Stub && $actual instanceof Stub;
    }

    /**
     * @return array<mixed>
     */
    protected function toArray(object $object): array
    {
        assert($object instanceof Stub);

        $array = parent::toArray($object);

        foreach (array_keys($array) as $key) {
            if (!str_starts_with($key, '__phpunit_')) {
                continue;
            }

            unset($array[$key]);
        }

        return $array;
    }
}
