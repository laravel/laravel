<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Unit\DSL;

use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * Test for ParametersTrait.
 */
class ParametersTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParametersTrait
     */
    private $parametersTraitMock;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->parametersTraitMock = $this->getMockForTrait('ONGR\ElasticsearchDSL\ParametersTrait');
    }

    /**
     * Tests addParameter method.
     */
    public function testGetAndAddParameter()
    {
        $this->parametersTraitMock->addParameter('acme', 123);
        $this->assertEquals(123, $this->parametersTraitMock->getParameter('acme'));
        $this->parametersTraitMock->addParameter('bar', 321);
        $this->assertEquals(321, $this->parametersTraitMock->getParameter('bar'));
    }
}
