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
 * Class representing geo distance aggregation.
 */
class GeoDistanceAggregation extends AbstractAggregation
{
    use BucketingTrait;

    /**
     * @var mixed
     */
    private $origin;

    /**
     * @var string
     */
    private $distanceType;

    /**
     * @var string
     */
    private $unit;

    /**
     * @var array
     */
    private $ranges = [];

    /**
     * Inner aggregations container init.
     *
     * @param string $name
     * @param string $field
     * @param mixed  $origin
     * @param array  $ranges
     * @param string $unit
     * @param string $distanceType
     */
    public function __construct($name, $field = null, $origin = null, $ranges = [], $unit = null, $distanceType = null)
    {
        parent::__construct($name);

        $this->setField($field);
        $this->setOrigin($origin);
        foreach ($ranges as $range) {
            $from = isset($range['from']) ? $range['from'] : null;
            $to = isset($range['to']) ? $range['to'] : null;
            $this->addRange($from, $to);
        }
        $this->setUnit($unit);
        $this->setDistanceType($distanceType);
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param mixed $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getDistanceType()
    {
        return $this->distanceType;
    }

    /**
     * @param string $distanceType
     */
    public function setDistanceType($distanceType)
    {
        $this->distanceType = $distanceType;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * Add range to aggregation.
     *
     * @param int|float|null $from
     * @param int|float|null $to
     *
     * @throws \LogicException
     *
     * @return GeoDistanceAggregation
     */
    public function addRange($from = null, $to = null)
    {
        $range = array_filter(
            [
                'from' => $from,
                'to' => $to,
            ]
        );

        if (empty($range)) {
            throw new \LogicException('Either from or to must be set. Both cannot be null.');
        }

        $this->ranges[] = $range;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        $data = [];

        if ($this->getField()) {
            $data['field'] = $this->getField();
        } else {
            throw new \LogicException('Geo distance aggregation must have a field set.');
        }

        if ($this->getOrigin()) {
            $data['origin'] = $this->getOrigin();
        } else {
            throw new \LogicException('Geo distance aggregation must have an origin set.');
        }

        if ($this->getUnit()) {
            $data['unit'] = $this->getUnit();
        }

        if ($this->getDistanceType()) {
            $data['distance_type'] = $this->getDistanceType();
        }

        $data['ranges'] = $this->ranges;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'geo_distance';
    }
}
