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

use ONGR\ElasticsearchDSL\Query\ExistsQuery;

/**
 * Unit test for ExistsQuery.
 */
class ExistsQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toArray() method.
     */
    public function testToArray()
    {
        $query = new ExistsQuery('bar');
        $this->assertEquals(['exists' => ['field' => 'bar']], $query->toArray());
    }
}
