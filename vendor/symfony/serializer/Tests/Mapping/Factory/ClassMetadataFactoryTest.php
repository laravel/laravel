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

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\LoaderChain;
use Symfony\Component\Serializer\Tests\Mapping\TestClassMetadataFactory;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ClassMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $classMetadata = new ClassMetadataFactory(new LoaderChain(array()));
        $this->assertInstanceOf('Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface', $classMetadata);
    }

    public function testGetMetadataFor()
    {
        $factory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $classMetadata = $factory->getMetadataFor('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy');

        $this->assertEquals(TestClassMetadataFactory::createClassMetadata(true, true), $classMetadata);
    }

    public function testHasMetadataFor()
    {
        $factory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->assertTrue($factory->hasMetadataFor('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy'));
        $this->assertTrue($factory->hasMetadataFor('Symfony\Component\Serializer\Tests\Fixtures\GroupDummyParent'));
        $this->assertTrue($factory->hasMetadataFor('Symfony\Component\Serializer\Tests\Fixtures\GroupDummyInterface'));
        $this->assertFalse($factory->hasMetadataFor('Dunglas\Entity'));
    }

    /**
     * @group legacy
     */
    public function testCacheExists()
    {
        $cache = $this->getMock('Doctrine\Common\Cache\Cache');
        $cache
            ->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue('foo'))
        ;

        $factory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()), $cache);
        $this->assertEquals('foo', $factory->getMetadataFor('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy'));
    }

    /**
     * @group legacy
     */
    public function testCacheNotExists()
    {
        $cache = $this->getMock('Doctrine\Common\Cache\Cache');
        $cache->method('fetch')->will($this->returnValue(false));
        $cache->method('save');

        $factory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()), $cache);
        $metadata = $factory->getMetadataFor('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy');

        $this->assertEquals(TestClassMetadataFactory::createClassMetadata(true, true), $metadata);
    }
}
