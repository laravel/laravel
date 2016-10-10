<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Unit\SearchEndpoint;

use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\SearchEndpoint\FilterEndpoint;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class FilterEndpointTest.
 */
class FilterEndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests constructor.
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'ONGR\ElasticsearchDSL\SearchEndpoint\FilterEndpoint',
            new FilterEndpoint()
        );
    }

    /**
     * Tests if correct order is returned. It's very important that filters must be executed first.
     */
    public function testGetOrder()
    {
        $instance = new FilterEndpoint();
        $this->assertEquals(1, $instance->getOrder());
    }

    /**
     * Test normalization.
     */
    public function testNormalization()
    {
        $instance = new FilterEndpoint();
        /** @var NormalizerInterface|MockObject $normalizerInterface */
        $normalizerInterface = $this->getMockForAbstractClass(
            'Symfony\Component\Serializer\Normalizer\NormalizerInterface'
        );
        $this->assertNull($instance->normalize($normalizerInterface));
        $this->assertFalse($instance->hasReference('filter_query'));

        $matchAllFilter = new MatchAllQuery();
        $instance->add($matchAllFilter);

        $this->assertNull($instance->normalize($normalizerInterface));
        $this->assertTrue($instance->hasReference('filter_query'));

        /** @var \ONGR\ElasticsearchDSL\Query\BoolQuery $bool */
        $bool = $instance->getReference('filter_query');
        $this->assertInstanceOf('ONGR\ElasticsearchDSL\Query\BoolQuery', $bool);

        $must = $bool->getQueries('must');
        $realReference = reset($must);

        $this->assertSame($matchAllFilter, $realReference);
    }

    /**
     * Tests if endpoint returns builders.
     */
    public function testEndpointGetter()
    {
        $filterName = 'acme_filter';
        $filter = new MatchAllQuery();
        $endpoint = new FilterEndpoint();
        $endpoint->add($filter, $filterName);
        $builders = $endpoint->getAll();

        $this->assertCount(1, $builders);
        $this->assertSame($filter, $builders[$filterName]);
    }
}
