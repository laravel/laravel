<?php declare(strict_types=1);
/*
 * This file is part of sebastian/object-reflector.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\ObjectReflector;

use function count;
use function explode;

final class ObjectReflector
{
    /**
     * @return array<string, mixed>
     */
    public function getProperties(object $object): array
    {
        $properties = [];
        $className  = $object::class;

        foreach ((array) $object as $name => $value) {
            $name = explode("\0", (string) $name);

            if (count($name) === 1) {
                $name = $name[0];
            } elseif ($name[1] !== $className) {
                $name = $name[1] . '::' . $name[2];
            } else {
                $name = $name[2];
            }

            $properties[$name] = $value;
        }

        return $properties;
    }
}
