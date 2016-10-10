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

use ONGR\ElasticsearchDSL\Query\MissingQuery;

class MissingQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider to testGetToArray.
     *
     * @return array
     */
    public function getArrayDataProvider()
    {
        return [
            // Case 1.
            ['user', [], ['field' => 'user']],
            // Case 2.
            ['user', ['existence' => true], ['field' => 'user', 'existence' => true]],
        ];
    }

    /**
     * Test for query toArray() method.
     *
     * @param string $field
     * @param array  $parameters
     * @param array  $expected
     *
     * @dataProvider getArrayDataProvider
     */
    public function testToArray($field, $parameters, $expected)
    {
        $query = new MissingQuery($field, $parameters);
        $result = $query->toArray();
        $this->assertEquals(['missing' => $expected], $result);
    }
}
