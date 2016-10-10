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

use ONGR\ElasticsearchDSL\Aggregation\DateRangeAggregation;

class DateRangeAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Date range aggregation must have field, format set and range added.
     */
    public function testIfExceptionIsThrownWhenNoParametersAreSet()
    {
        $agg = new DateRangeAggregation('test_agg');
        $agg->getArray();
    }

    /**
     * Test if exception is thrown when both range parameters are null.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Either from or to must be set. Both cannot be null.
     */
    public function testIfExceptionIsThrownWhenBothRangesAreNull()
    {
        $agg = new DateRangeAggregation('test_agg');
        $agg->addRange(null, null);
    }

    /**
     * Test getArray method.
     */
    public function testDateRangeAggregationGetArray()
    {
        $agg = new DateRangeAggregation('foo');
        $agg->addRange(10, 20);
        $agg->setFormat('bar');
        $agg->setField('baz');
        $result = $agg->getArray();
        $expected = [
            'format' => 'bar',
            'field' => 'baz',
            'ranges' => [['from' => 10, 'to' => 20]],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests getType method.
     */
    public function testDateRangeAggregationGetType()
    {
        $aggregation = new DateRangeAggregation('foo');
        $result = $aggregation->getType();
        $this->assertEquals('date_range', $result);
    }

    /**
     * Data provider for testDateRangeAggregationConstructor.
     *
     * @return array
     */
    public function testDateRangeAggregationConstructorProvider()
    {
        return [
            // Case #0. Minimum arguments.
            [],
            // Case #1. Provide field.
            ['field' => 'fieldName'],
            // Case #2. Provide format.
            ['field' => 'fieldName', 'format' => 'formatString'],
            // Case #3. Provide empty ranges.
            ['field' => 'fieldName', 'format' => 'formatString', 'ranges' => []],
            // Case #4. Provide 1 range.
            [
                'field' => 'fieldName',
                'format' => 'formatString',
                'ranges' => [['from' => 'value']],
            ],
            // Case #4. Provide 2 ranges.
            [
                'field' => 'fieldName',
                'format' => 'formatString',
                'ranges' => [['from' => 'value'], ['to' => 'value']],
            ],
            // Case #5. Provide 3 ranges.
            [
                'field' => 'fieldName',
                'format' => 'formatString',
                'ranges' => [['from' => 'value'], ['to' => 'value'], ['from' => 'value', 'to' => 'value2']],
            ],
        ];
    }

    /**
     * Tests constructor method.
     *
     * @param string $field
     * @param string $format
     * @param array  $ranges
     *
     * @dataProvider testDateRangeAggregationConstructorProvider
     */
    public function testDateRangeAggregationConstructor($field = null, $format = null, array $ranges = null)
    {
        /** @var DateRangeAggregation|\PHPUnit_Framework_MockObject_MockObject $aggregation */
        $aggregation = $this->getMockBuilder('ONGR\ElasticsearchDSL\Aggregation\DateRangeAggregation')
            ->disableOriginalConstructor()->getMock();
        $aggregation->expects($this->once())->method('setField')->with($field);
        $aggregation->expects($this->once())->method('setFormat')->with($format);
        $aggregation->expects($this->exactly(count($ranges)))->method('addRange');

        if ($field !== null) {
            if ($format !== null) {
                if ($ranges !== null) {
                    $aggregation->__construct('mock', $field, $format, $ranges);
                } else {
                    $aggregation->__construct('mock', $field, $format);
                }
            } else {
                $aggregation->__construct('mock', $field);
            }
        } else {
            $aggregation->__construct('mock');
        }
    }
}
