<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL;

/**
 * A trait which handles the behavior of parameters in queries, filters, etc.
 */
trait ParametersTrait
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Checks if parameter exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Removes parameter.
     *
     * @param string $name
     */
    public function removeParameter($name)
    {
        if ($this->hasParameter($name)) {
            unset($this->parameters[$name]);
        }
    }

    /**
     * Returns one parameter by it's name.
     *
     * @param string $name
     *
     * @return array|false
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * Returns an array of all parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string                 $name
     * @param array|string|\stdClass $value
     */
    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Sets an array of parameters.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns given array merged with parameters.
     *
     * @param array $array
     *
     * @return array
     */
    protected function processArray(array $array = [])
    {
        return array_merge($array, $this->parameters);
    }
}
