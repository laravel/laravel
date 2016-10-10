<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\Type\BucketingTrait;

/**
 * Class representing NestedAggregation.
 */
class NestedAggregation extends AbstractAggregation
{
    use BucketingTrait;

    /**
     * @var string
     */
    private $path;

    /**
     * Inner aggregations container init.
     *
     * @param string $name
     * @param string $path
     */
    public function __construct($name, $path = null)
    {
        parent::__construct($name);

        $this->setPath($path);
    }

    /**
     * Return path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets path.
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'nested';
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        return ['path' => $this->getPath()];
    }
}
