<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Query;

use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermsQuery;

class NestedQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider to testGetToArray.
     *
     * @return array
     */
    public function getArrayDataProvider()
    {
        $query = [
            'terms' => [
                'foo' => 'bar',
            ],
        ];

        return [
            'query_only' => [
                'product.sub_item',
                [],
                ['path' => 'product.sub_item', 'query' => $query],
            ],
            'query_with_parameters' => [
                'product.sub_item',
                ['_cache' => true, '_name' => 'named_result'],
                [
                    'path' => 'product.sub_item',
                    'query' => $query,
                    '_cache' => true,
                    '_name' => 'named_result',
                ],
            ],
        ];
    }

    /**
     * Test for query toArray() method.
     *
     * @param string $path
     * @param array  $parameters
     * @param array  $expected
     *
     * @dataProvider getArrayDataProvider
     */
    public function testToArray($path, $parameters, $expected)
    {
        $query = new TermsQuery('foo', 'bar');
        $query = new NestedQuery($path, $query, $parameters);
        $result = $query->toArray();
        $this->assertEquals(['nested' => $expected], $result);
    }
}
