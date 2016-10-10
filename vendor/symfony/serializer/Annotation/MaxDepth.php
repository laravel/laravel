<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Annotation;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * Annotation class for @MaxDepth().
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MaxDepth
{
    /**
     * @var int
     */
    private $maxDepth;

    public function __construct(array $data)
    {
        if (!isset($data['value']) || !$data['value']) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" cannot be empty.', get_class($this)));
        }

        if (!is_int($data['value']) || $data['value'] <= 0) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" must be a positive integer.', get_class($this)));
        }

        $this->maxDepth = $data['value'];
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }
}
