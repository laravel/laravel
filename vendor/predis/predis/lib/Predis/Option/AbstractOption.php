<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Option;

/**
 * Implements a client option.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class AbstractOption implements OptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(ClientOptionsInterface $options, $value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(ClientOptionsInterface $options)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ClientOptionsInterface $options, $value)
    {
        if (isset($value)) {
            return $this->filter($options, $value);
        }

        return $this->getDefault($options);
    }
}
