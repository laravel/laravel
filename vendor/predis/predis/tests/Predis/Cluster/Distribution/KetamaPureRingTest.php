<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster\Distribution;

/**
 * @todo To be improved.
 */
class KetamaPureRingTest extends PredisDistributorTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getDistributorInstance()
    {
        return new KetamaPureRing();
    }

    /**
     * @group disconnected
     */
    public function testHash()
    {
        $ring = $this->getDistributorInstance();
        list(, $hash) = unpack('V', md5('foobar', true));

        $this->assertEquals($hash, $ring->hash('foobar'));
    }

    /**
     * @group disconnected
     */
    public function testSingleNodeInRing()
    {
        $node = '127.0.0.1:7000';

        $ring = $this->getDistributorInstance();
        $ring->add($node);

        $expected = array_fill(0, 20, $node);
        $actual = $this->getNodes($ring, 20);

        $this->assertSame($expected, $actual);
    }

    /**
     * @group disconnected
     */
    public function testMultipleNodesInRing()
    {
        $nodes = array(
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7002',
        );

        $ring = $this->getDistributorInstance();
        foreach ($nodes as $node) {
            $ring->add($node);
        }

        $expected = array(
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7002',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7002',
            '127.0.0.1:7000',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7002',
            '127.0.0.1:7000',
            '127.0.0.1:7002',
            '127.0.0.1:7001',
            '127.0.0.1:7002',
        );

        $actual = $this->getNodes($ring, 20);

        $this->assertSame($expected, $actual);
    }

    /**
     * @group disconnected
     */
    public function testSubsequendAddAndRemoveFromRing()
    {
        $ring = $this->getDistributorInstance();

        $expected1 = array_fill(0, 10, '127.0.0.1:7000');
        $expected3 = array_fill(0, 10, '127.0.0.1:7001');
        $expected2 = array(
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7000',
            '127.0.0.1:7001',
        );

        $ring->add('127.0.0.1:7000');
        $actual1 = $this->getNodes($ring, 10);

        $ring->add('127.0.0.1:7001');
        $actual2 = $this->getNodes($ring, 10);

        $ring->remove('127.0.0.1:7000');
        $actual3 = $this->getNodes($ring, 10);

        $this->assertSame($expected1, $actual1);
        $this->assertSame($expected2, $actual2);
        $this->assertSame($expected3, $actual3);
    }

    /**
     * @todo This tests should be moved in Predis\Cluster\Distribution\DistributionStrategyTestCase
     * @group disconnected
     */
    public function testCallbackToGetNodeHash()
    {
        $node = '127.0.0.1:7000';
        $callable = $this->getMock('stdClass', array('__invoke'));

        $callable->expects($this->once())
                 ->method('__invoke')
                 ->with($node)
                 ->will($this->returnValue($node));

        $ring = new KetamaPureRing($callable);
        $ring->add($node);

        $this->getNodes($ring);
    }
}
