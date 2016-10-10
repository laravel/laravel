<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Suggest;

use ONGR\ElasticsearchDSL\Suggest\CompletionSuggest;

class CompletionSuggestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getType method.
     */
    public function testSuggestGetType()
    {
        $suggest = new CompletionSuggest('foo', 'bar');
        $result = $suggest->getType();
        $this->assertEquals('completion_suggest', $result);
    }

    /**
     * Tests toArray() method.
     */
    public function testSuggestWithoutFieldAndSize()
    {
        // Case #1 suggest without field and size params.
        $suggest = new CompletionSuggest('foo', 'bar');
        $expected = ['foo' => [
            'text' => 'bar',
            'completion' => [
                'field' => 'suggest',
                'size' => 3,
            ],
        ]];
        $this->assertEquals($expected, $suggest->toArray());
    }

    /**
     * Tests toArray() method.
     */
    public function testToArray()
    {
        $suggest = new CompletionSuggest(
            'foo',
            'bar',
            [
                'size' => 5,
                'field' => 'title',
                'fuzzy' => ['fuzziness' => 2]
            ]
        );
        $expected = ['foo' => [
            'text' => 'bar',
            'completion' => [
                'field' => 'suggest',
                'size' => 5,
                'fuzzy' => ['fuzziness' => 2]
            ],
        ]];
        $this->assertEquals($expected, $suggest->toArray());
    }
}
