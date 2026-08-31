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

namespace Ramsey\Uuid\Generator;

use RandomLib\Factory;
use RandomLib\Generator;

/**
 * RandomLibAdapter generates strings of random binary data using the paragonie/random-lib library
 *
 * @deprecated This class will be removed in 5.0.0. Use the default RandomBytesGenerator or implement your own generator
 *     that implements RandomGeneratorInterface.
 *
 * @link https://packagist.org/packages/paragonie/random-lib paragonie/random-lib
 */
class RandomLibAdapter implements RandomGeneratorInterface
{
    private Generator $generator;

    /**
     * Constructs a RandomLibAdapter
     *
     * By default, if no Generator is passed in, this creates a high-strength generator to use when generating random
     * binary data.
     *
     * @param Generator | null $generator The generator to use when generating binary data
     */
    public function __construct(?Generator $generator = null)
    {
        if ($generator === null) {
            $factory = new Factory();
            $generator = $factory->getHighStrengthGenerator();
        }

        $this->generator = $generator;
    }

    public function generate(int $length): string
    {
        return $this->generator->generate($length);
    }
}
