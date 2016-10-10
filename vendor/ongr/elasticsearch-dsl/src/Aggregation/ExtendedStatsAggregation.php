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

use ONGR\ElasticsearchDSL\Aggregation\Type\MetricTrait;
use ONGR\ElasticsearchDSL\ScriptAwareTrait;

/**
 * Class representing Extended stats aggregation.
 */
class ExtendedStatsAggregation extends AbstractAggregation
{
    use MetricTrait;
    use ScriptAwareTrait;

    /**
     * Inner aggregations container init.
     *
     * @param string $name
     * @param string $field
     * @param int    $sigma
     * @param string $script
     */
    public function __construct($name, $field = null, $sigma = null, $script = null)
    {
        parent::__construct($name);

        $this->setField($field);
        $this->setSigma($sigma);
        $this->setScript($script);
    }

    /**
     * @var int
     */
    private $sigma;

    /**
     * @return int
     */
    public function getSigma()
    {
        return $this->sigma;
    }

    /**
     * @param int $sigma
     */
    public function setSigma($sigma)
    {
        $this->sigma = $sigma;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'extended_stats';
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        $out = array_filter(
            [
                'field' => $this->getField(),
                'script' => $this->getScript(),
                'sigma' => $this->getSigma(),
            ],
            function ($val) {
                return ($val || is_numeric($val));
            }
        );

        return $out;
    }
}
