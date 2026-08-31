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

/**
 * NameGeneratorFactory retrieves a default name generator, based on the environment
 */
class NameGeneratorFactory
{
    /**
     * Returns a default name generator, based on the current environment
     */
    public function getGenerator(): NameGeneratorInterface
    {
        return new DefaultNameGenerator();
    }
}
