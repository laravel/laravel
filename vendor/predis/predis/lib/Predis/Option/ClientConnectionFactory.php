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

use Predis\Connection\ConnectionFactory;
use Predis\Connection\ConnectionFactoryInterface;

/**
 * Option class that returns a connection factory to be used by a client.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientConnectionFactory extends AbstractOption
{
    /**
     * {@inheritdoc}
     */
    public function filter(ClientOptionsInterface $options, $value)
    {
        if ($value instanceof ConnectionFactoryInterface) {
            return $value;
        }

        if (is_array($value)) {
            $factory = $this->getDefault($options);

            foreach ($value as $scheme => $initializer) {
                $factory->define($scheme, $initializer);
            }

            return $factory;
        }

        if (is_callable($value)) {
            $factory = call_user_func($value, $options, $this);

            if (!$factory instanceof ConnectionFactoryInterface) {
                throw new \InvalidArgumentException('Instance of Predis\Connection\ConnectionFactoryInterface expected');
            }

            return $factory;
        }

        if (is_string($value) && @class_exists($value)) {
            $factory = new $value();

            if (!$factory instanceof ConnectionFactoryInterface) {
                throw new \InvalidArgumentException("Class $value must be an instance of Predis\Connection\ConnectionFactoryInterface");
            }

            return $factory;
        }

        throw new \InvalidArgumentException('Invalid value for the connections option');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(ClientOptionsInterface $options)
    {
        return new ConnectionFactory($options->profile);
    }
}
