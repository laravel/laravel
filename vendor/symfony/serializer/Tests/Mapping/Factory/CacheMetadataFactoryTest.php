<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Mapping\Factory;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Serializer\Mapping\ClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Factory\CacheClassMetadataFactory;
use Symfony\Component\Serializer\Tests\Fixtures\Dummy;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CacheMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMetadataFor()
    {
        $metadata = new ClassMetadata(Dummy::class);

        $decorated = $this->getMock(ClassMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('getMetadataFor')
            ->will($this->returnValue($metadata))
        ;

        $factory = new CacheClassMetadataFactory($decorated, new ArrayAdapter());

        $this->assertEquals($metadata, $factory->getMetadataFor(Dummy::class));
        // The second call should retrieve the value from the cache
        $this->assertEquals($metadata, $factory->getMetadataFor(Dummy::class));
    }

    public function testHasMetadataFor()
    {
        $decorated = $this->getMock(ClassMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('hasMetadataFor')
            ->will($this->returnValue(true))
        ;

        $factory = new CacheClassMetadataFactory($decorated, new ArrayAdapter());

        $this->assertTrue($factory->hasMetadataFor(Dummy::class));
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function testInvalidClassThrowsException()
    {
        $decorated = $this->getMock(ClassMetadataFactoryInterface::class);
        $factory = new CacheClassMetadataFactory($decorated, new ArrayAdapter());

        $factory->getMetadataFor('Not\Exist');
    }
}
