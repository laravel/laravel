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
use ONGR\ElasticsearchDSL\SearchEndpoint\PostFilterEndpoint;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class PostFilterEndpointTest.
 */
class PostFilterEndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests constructor.
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf('ONGR\ElasticsearchDSL\SearchEndpoint\PostFilterEndpoint', new PostFilterEndpoint());
    }

    /**
     * Tests if correct order is returned. It's very important that filters must be executed second.
     */
    public function testGetOrder()
    {
        $instance = new PostFilterEndpoint();
        $this->assertEquals(2, $instance->getOrder());
    }

    /**
     * Test normalization.
     */
    public function testNormalization()
    {
        $instance = new PostFilterEndpoint();
        /** @var NormalizerInterface|MockObject $normalizerInterface */
        $normalizerInterface = $this->getMockForAbstractClass(
            'Symfony\Component\Serializer\Normalizer\NormalizerInterface'
        );
        $this->assertNull($instance->normalize($normalizerInterface));

        $matchAll = new MatchAllQuery();
        $instance->add($matchAll);

        $this->assertEquals(
            json_encode($matchAll->toArray()),
            json_encode($instance->normalize($normalizerInterface))
        );
    }

    /**
     * Tests if endpoint returns builders.
     */
    public function testEndpointGetter()
    {
        $filterName = 'acme_post_filter';
        $filter = new MatchAllQuery();

        $endpoint = new PostFilterEndpoint();
        $endpoint->add($filter, $filterName);
        $builders = $endpoint->getAll();

        $this->assertCount(1, $builders);
        $this->assertSame($filter, $builders[$filterName]);
    }
}
