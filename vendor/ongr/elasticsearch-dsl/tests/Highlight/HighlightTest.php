<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Highlight;

use ONGR\ElasticsearchDSL\Highlight\Highlight;

class HighlightTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests GetType method, it should return 'highlight'.
     */
    public function testGetType()
    {
        $highlight = new Highlight();
        $result = $highlight->getType();
        $this->assertEquals('highlight', $result);
    }

    /**
     * Tests ParametersTrait hasParameter method.
     */
    public function testTraitHasParameter()
    {
        $highlight = new Highlight();
        $highlight->addParameter('_source', ['include' => ['title']]);
        $result = $highlight->hasParameter('_source');
        $this->assertTrue($result);
    }

    /**
     * Tests ParametersTrait removeParameter method.
     */
    public function testTraitRemoveParameter()
    {
        $highlight = new Highlight();
        $highlight->addParameter('_source', ['include' => ['title']]);
        $highlight->removeParameter('_source');
        $result = $highlight->hasParameter('_source');
        $this->assertFalse($result);
    }

    /**
     * Tests ParametersTrait getParameter method.
     */
    public function testTraitGetParameter()
    {
        $highlight = new Highlight();
        $highlight->addParameter('_source', ['include' => 'title']);
        $expectedResult = ['include' => 'title'];
        $this->assertEquals($expectedResult, $highlight->getParameter('_source'));
    }

    /**
     * Tests ParametersTrait getParameters and setParameters methods.
     */
    public function testTraitSetGetParameters()
    {
        $highlight = new Highlight();
        $highlight->setParameters(
            [
                '_source',
                ['include' => 'title'],
                'content',
                ['force_source' => true],
            ]
        );
        $expectedResult = [
            '_source',
            ['include' => 'title'],
            'content',
            ['force_source' => true],
        ];
        $this->assertEquals($expectedResult, $highlight->getParameters());
    }

    /**
     * Test toArray method.
     */
    public function testToArray()
    {
        $highlight = new Highlight();
        $highlight->addField('ok');
        $highlight->addParameter('_source', ['include' => ['title']]);
        $highlight->setTags(['<tag>'], ['</tag>']);
        $result = $highlight->toArray();
        $expectedResult = [
            'fields' => [
                'ok' => new \StdClass,
            ],
            '_source' => [
                'include' => [
                    'title',
                ],
            ],
            'pre_tags' => [
                '<tag>',
            ],
            'post_tags' => [
                '</tag>',
            ],
        ];
        $this->assertEquals($expectedResult, $result);
    }
}
