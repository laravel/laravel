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

use ONGR\ElasticsearchDSL\Aggregation\GlobalAggregation;

class GlobalAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testToArray().
     *
     * @return array
     */
    public function getToArrayData()
    {
        $out = [];

        // Case #0 global aggregation.
        $aggregation = new GlobalAggregation('test_agg');

        $result = [
            'global' => new \stdClass(),
        ];

        $out[] = [
            $aggregation,
            $result,
        ];

        // Case #1 nested global aggregation.
        $aggregation = new GlobalAggregation('test_agg');
        $aggregation2 = new GlobalAggregation('test_agg_2');
        $aggregation->addAggregation($aggregation2);

        $result = [
            'global' => new \stdClass(),
            'aggregations' => [
                $aggregation2->getName() => $aggregation2->toArray(),
            ],
        ];

        $out[] = [
            $aggregation,
            $result,
        ];

        return $out;
    }

    /**
     * Test for global aggregation toArray() method.
     *
     * @param GlobalAggregation $aggregation
     * @param array             $expectedResult
     *
     * @dataProvider getToArrayData
     */
    public function testToArray($aggregation, $expectedResult)
    {
        $this->assertEquals(
            json_encode($expectedResult),
            json_encode($aggregation->toArray())
        );
    }

    /**
     * Test for setField method on global aggregation.
     *
     * @expectedException \LogicException
     */
    public function testSetField()
    {
        $aggregation = new GlobalAggregation('test_agg');
        $aggregation->setField('test_field');
    }
}
