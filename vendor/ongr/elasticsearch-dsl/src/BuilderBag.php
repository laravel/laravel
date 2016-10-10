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
 * Container for named builders.
 */
class BuilderBag
{
    /**
     * @var BuilderInterface[]
     */
    private $bag = [];

    /**
     * @param BuilderInterface[] $builders
     */
    public function __construct($builders = [])
    {
        foreach ($builders as $builder) {
            $this->add($builder);
        }
    }

    /**
     * Adds a builder.
     *
     * @param BuilderInterface $builder
     *
     * @return string
     */
    public function add(BuilderInterface $builder)
    {
        if (method_exists($builder, 'getName')) {
            $name = $builder->getName();
        } else {
            $name = uniqid();
        }

        $this->bag[$name] = $builder;

        return $name;
    }

    /**
     * Checks if builder exists by a specific name.
     *
     * @param string $name Builder name.
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->bag[$name]);
    }

    /**
     * Removes a builder by name.
     *
     * @param string $name Builder name.
     */
    public function remove($name)
    {
        unset($this->bag[$name]);
    }

    /**
     * Clears contained builders.
     */
    public function clear()
    {
        $this->bag = [];
    }

    /**
     * Returns a builder by name.
     *
     * @param string $name Builder name.
     *
     * @return BuilderInterface
     */
    public function get($name)
    {
        return $this->bag[$name];
    }

    /**
     * Returns all builders contained.
     *
     * @param string|null $type Builder type.
     *
     * @return BuilderInterface[]
     */
    public function all($type = null)
    {
        return array_filter(
            $this->bag,
            /** @var BuilderInterface $builder */
            function (BuilderInterface $builder) use ($type) {
                return $type === null || $builder->getType() == $type;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $output = [];
        foreach ($this->all() as $builder) {
            $output = array_merge($output, $builder->toArray());
        }

        return $output;
    }
}
