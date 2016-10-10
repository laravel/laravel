<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\PercentileRanksAggregation;

/**
 * Percentile ranks aggregation unit tests.
 */
class PercentileRanksAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PercentileRanksAggregation
     */
    public $agg;

    /**
     * Phpunit setup.
     */
    public function setUp()
    {
        $this->agg = new PercentileRanksAggregation('foo');
    }

    /**
     * Tests if exception is thrown when required parameters not set.
     *
     * @expectedException \LogicException
     */
    public function testIfPercentileRanksAggregationThrowsAnException()
    {
        $this->agg->toArray();
    }

    /**
     * Tests exception when only field is set.
     *
     * @expectedException \LogicException
     */
    public function testIfExceptionIsThrownWhenFieldSetAndValueNotSet()
    {
        $this->agg->setField('bar');
        $this->agg->toArray();
    }

    /**
     * Tests exception when only value is set.
     *
     * @expectedException \LogicException
     */
    public function testIfExceptionIsThrownWhenScriptSetAndValueNotSet()
    {
        $this->agg->setScript('bar');
        $this->agg->toArray();
    }

    /**
     * Test getType method.
     */
    public function testGetType()
    {
        $this->assertEquals('percentile_ranks', $this->agg->getType());
    }

    /**
     * Test toArray method.
     */
    public function testToArray()
    {
        $this->agg->setField('bar');
        $this->agg->setValues(['bar']);
        $this->assertSame(
            [
                'percentile_ranks' => [
                    'field' => 'bar',
                    'values' => ['bar'],
                ],
            ],
            $this->agg->toArray()
        );
    }
}
