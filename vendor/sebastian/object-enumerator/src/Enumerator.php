<?php declare(strict_types=1);
/*
 * This file is part of sebastian/object-enumerator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\ObjectEnumerator;

use function array_merge;
use function is_array;
use function is_object;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SebastianBergmann\RecursionContext\Context;

final class Enumerator
{
    /**
     * @param array<mixed>|object $variable
     *
     * @return list<object>
     */
    public function enumerate(array|object $variable, Context $processed = new Context): array
    {
        $objects = [];

        if ($processed->contains($variable)) {
            return $objects;
        }

        $array = $variable;

        /* @noinspection UnusedFunctionResultInspection */
        $processed->add($variable);

        if (is_array($variable)) {
            /** @phpstan-ignore foreach.nonIterable */
            foreach ($array as $element) {
                if (!is_array($element) && !is_object($element)) {
                    continue;
                }

                /** @noinspection SlowArrayOperationsInLoopInspection */
                $objects = array_merge(
                    $objects,
                    $this->enumerate($element, $processed),
                );
            }

            return $objects;
        }

        $objects[] = $variable;

        foreach ((new ObjectReflector)->getProperties($variable) as $value) {
            if (!is_array($value) && !is_object($value)) {
                continue;
            }

            /** @noinspection SlowArrayOperationsInLoopInspection */
            $objects = array_merge(
                $objects,
                $this->enumerate($value, $processed),
            );
        }

        return $objects;
    }
}
