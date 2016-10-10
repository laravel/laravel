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
 * Class representing PercentilesAggregation.
 */
class PercentilesAggregation extends AbstractAggregation
{
    use MetricTrait;
    use ScriptAwareTrait;

    /**
     * @var array
     */
    private $percents;

    /**
     * @var int
     */
    private $compression;

    /**
     * Inner aggregations container init.
     *
     * @param string $name
     * @param string $field
     * @param array  $percents
     * @param string $script
     * @param int    $compression
     */
    public function __construct($name, $field = null, $percents = null, $script = null, $compression = null)
    {
        parent::__construct($name);

        $this->setField($field);
        $this->setPercents($percents);
        $this->setScript($script);
        $this->setCompression($compression);
    }

    /**
     * @return array
     */
    public function getPercents()
    {
        return $this->percents;
    }

    /**
     * @param array $percents
     */
    public function setPercents($percents)
    {
        $this->percents = $percents;
    }

    /**
     * @return int
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @param int $compression
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'percentiles';
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        $out = array_filter(
            [
                'compression' => $this->getCompression(),
                'percents' => $this->getPercents(),
                'field' => $this->getField(),
                'script' => $this->getScript(),
            ],
            function ($val) {
                return ($val || is_numeric($val));
            }
        );

        $this->isRequiredParametersSet($out);

        return $out;
    }

    /**
     * @param array $a
     *
     * @throws \LogicException
     */
    private function isRequiredParametersSet($a)
    {
        if (!array_key_exists('field', $a) && !array_key_exists('script', $a)) {
            throw new \LogicException('Percentiles aggregation must have field or script set.');
        }
    }
}
