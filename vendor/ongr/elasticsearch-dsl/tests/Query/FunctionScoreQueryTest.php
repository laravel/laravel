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

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\FunctionScoreQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests for FunctionScoreQuery.
 */
class FunctionScoreQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testAddRandomFunction.
     *
     * @return array
     */
    public function addRandomFunctionProvider()
    {
        return [
            // Case #0. No seed.
            [
                'seed' => null,
                'expectedArray' => [
                    'query' => null,
                    'functions' => [
                        [
                            'random_score' => new \stdClass(),
                        ],
                    ],
                ],
            ],
            // Case #1. With seed.
            [
                'seed' => 'someSeed',
                'expectedArray' => [
                    'query' => null,
                    'functions' => [
                        [
                            'random_score' => [ 'seed' => 'someSeed'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Tests addRandomFunction method.
     *
     * @param mixed $seed
     * @param array $expectedArray
     *
     * @dataProvider addRandomFunctionProvider
     */
    public function testAddRandomFunction($seed, $expectedArray)
    {
        /** @var MatchAllQuery|MockObject $matchAllQuery */
        $matchAllQuery = $this->getMock('ONGR\ElasticsearchDSL\Query\MatchAllQuery');

        $functionScoreQuery = new FunctionScoreQuery($matchAllQuery);
        $functionScoreQuery->addRandomFunction($seed);

        $this->assertEquals(['function_score' => $expectedArray], $functionScoreQuery->toArray());
    }
    
    /**
     * Tests default argument values.
     */
    public function testAddFieldValueFactorFunction()
    {
        /** @var BuilderInterface|MockObject $builderInterface */
        $builderInterface = $this->getMockForAbstractClass('ONGR\ElasticsearchDSL\BuilderInterface');
        $functionScoreQuery = new FunctionScoreQuery($builderInterface);
        $functionScoreQuery->addFieldValueFactorFunction('field1', 2);
        $functionScoreQuery->addFieldValueFactorFunction('field2', 1.5, 'ln');

        $this->assertEquals(
            [
                'query' => null,
                'functions' => [
                    [
                        'field_value_factor' => [
                            'field' => 'field1',
                            'factor' => 2,
                            'modifier' => 'none',
                        ],
                    ],
                    [
                        'field_value_factor' => [
                            'field' => 'field2',
                            'factor' => 1.5,
                            'modifier' => 'ln',
                        ],
                    ],
                ],
            ],
            $functionScoreQuery->toArray()['function_score']
        );
    }
}
