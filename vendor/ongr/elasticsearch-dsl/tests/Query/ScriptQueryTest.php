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

use ONGR\ElasticsearchDSL\Query\ScriptQuery;

class ScriptQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testToArray().
     *
     * @return array
     */
    public function getArrayDataProvider()
    {
        return [
            'simple_script' => [
                "doc['num1'].value > 1",
                [],
                ['script' => ['inline' => "doc['num1'].value > 1"]],
            ],
            'script_with_parameters' => [
                "doc['num1'].value > param1",
                ['params' => ['param1' => 5]],
                ['script' => ['inline' => "doc['num1'].value > param1", 'params' => ['param1' => 5]]],
            ],
        ];
    }

    /**
     * Test for toArray().
     *
     * @param string $script     Script
     * @param array  $parameters Optional parameters
     * @param array  $expected   Expected values
     *
     * @dataProvider getArrayDataProvider
     */
    public function testToArray($script, $parameters, $expected)
    {
        $filter = new ScriptQuery($script, $parameters);
        $result = $filter->toArray();
        $this->assertEquals(['script' => $expected], $result);
    }
}
