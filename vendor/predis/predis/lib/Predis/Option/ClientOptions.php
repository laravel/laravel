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
 * Class that manages client options with filtering and conversion.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientOptions implements ClientOptionsInterface
{
    private $handlers;
    private $defined;
    private $options = array();

    /**
     * @param array $options Array of client options.
     */
    public function __construct(Array $options = array())
    {
        $this->handlers = $this->initialize($options);
        $this->defined = array_fill_keys(array_keys($options), true);
    }

    /**
     * Ensures that the default options are initialized.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'profile' => new ClientProfile(),
            'connections' => new ClientConnectionFactory(),
            'cluster' => new ClientCluster(),
            'replication' => new ClientReplication(),
            'prefix' => new ClientPrefix(),
            'exceptions' => new ClientExceptions(),
        );
    }

    /**
     * Initializes client options handlers.
     *
     * @param  array $options List of client options values.
     * @return array
     */
    protected function initialize(Array $options)
    {
        $handlers = $this->getDefaultOptions();

        foreach ($options as $option => $value) {
            if (isset($handlers[$option])) {
                $handler = $handlers[$option];
                $handlers[$option] = function ($options) use ($handler, $value) {
                    return $handler->filter($options, $value);
                };
            } else {
                $this->options[$option] = $value;
            }
        }

        return $handlers;
    }

    /**
     * Checks if the specified option is set.
     *
     * @param  string $option Name of the option.
     * @return bool
     */
    public function __isset($option)
    {
        return isset($this->defined[$option]);
    }

    /**
     * Returns the value of the specified option.
     *
     * @param  string $option Name of the option.
     * @return mixed
     */
    public function __get($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        if (isset($this->handlers[$option])) {
            $handler = $this->handlers[$option];
            $value = $handler instanceof OptionInterface ? $handler->getDefault($this) : $handler($this);
            $this->options[$option] = $value;

            return $value;
        }
    }

    /**
     * Returns the default value for the specified option.
     *
     * @param  string|OptionInterface $option Name or instance of the option.
     * @return mixed
     */
    public function getDefault($option)
    {
        if ($option instanceof OptionInterface) {
            return $option->getDefault($this);
        }

        $options = $this->getDefaultOptions();

        if (isset($options[$option])) {
            return $options[$option]->getDefault($this);
        }
    }
}
