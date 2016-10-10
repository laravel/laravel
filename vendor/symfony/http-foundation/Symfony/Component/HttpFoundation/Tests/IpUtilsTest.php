<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\IpUtils;

class IpUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testIpv4Provider
     */
    public function testIpv4($matches, $remoteAddr, $cidr)
    {
        $this->assertSame($matches, IpUtils::checkIp($remoteAddr, $cidr));
    }

    public function testIpv4Provider()
    {
        return array(
            array(true, '192.168.1.1', '192.168.1.1'),
            array(true, '192.168.1.1', '192.168.1.1/1'),
            array(true, '192.168.1.1', '192.168.1.0/24'),
            array(false, '192.168.1.1', '1.2.3.4/1'),
            array(false, '192.168.1.1', '192.168.1/33'),
            array(true, '192.168.1.1', array('1.2.3.4/1', '192.168.1.0/24')),
            array(true, '192.168.1.1', array('192.168.1.0/24', '1.2.3.4/1')),
            array(false, '192.168.1.1', array('1.2.3.4/1', '4.3.2.1/1')),
        );
    }

    /**
     * @dataProvider testIpv6Provider
     */
    public function testIpv6($matches, $remoteAddr, $cidr)
    {
        if (!defined('AF_INET6')) {
            $this->markTestSkipped('Only works when PHP is compiled without the option "disable-ipv6".');
        }

        $this->assertSame($matches, IpUtils::checkIp($remoteAddr, $cidr));
    }

    public function testIpv6Provider()
    {
        return array(
            array(true, '2a01:198:603:0:396e:4789:8e99:890f', '2a01:198:603:0::/65'),
            array(false, '2a00:198:603:0:396e:4789:8e99:890f', '2a01:198:603:0::/65'),
            array(false, '2a01:198:603:0:396e:4789:8e99:890f', '::1'),
            array(true, '0:0:0:0:0:0:0:1', '::1'),
            array(false, '0:0:603:0:396e:4789:8e99:0001', '::1'),
            array(true, '2a01:198:603:0:396e:4789:8e99:890f', array('::1', '2a01:198:603:0::/65')),
            array(true, '2a01:198:603:0:396e:4789:8e99:890f', array('2a01:198:603:0::/65', '::1')),
            array(false, '2a01:198:603:0:396e:4789:8e99:890f', array('::1', '1a01:198:603:0::/65')),
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAnIpv6WithOptionDisabledIpv6()
    {
        if (!extension_loaded('sockets')) {
            $this->markTestSkipped('Only works when the socket extension is enabled');
        }

        if (defined('AF_INET6')) {
            $this->markTestSkipped('Only works when PHP is compiled with the option "disable-ipv6".');
        }

        IpUtils::checkIp('2a01:198:603:0:396e:4789:8e99:890f', '2a01:198:603:0::/65');
    }
}
