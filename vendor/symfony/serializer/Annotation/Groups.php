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
 * Annotation class for @Groups().
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Groups
{
    /**
     * @var array
     */
    private $groups;

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) || !$data['value']) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" cannot be empty.', get_class($this)));
        }

        if (!is_array($data['value'])) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" must be an array of strings.', get_class($this)));
        }

        foreach ($data['value'] as $group) {
            if (!is_string($group)) {
                throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" must be an array of strings.', get_class($this)));
            }
        }

        $this->groups = $data['value'];
    }

    /**
     * Gets groups.
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
