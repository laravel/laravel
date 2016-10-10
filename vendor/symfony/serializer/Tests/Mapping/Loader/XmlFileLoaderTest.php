<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Mapping\Loader;

use Symfony\Component\Serializer\Mapping\Loader\XmlFileLoader;
use Symfony\Component\Serializer\Mapping\ClassMetadata;
use Symfony\Component\Serializer\Tests\Mapping\TestClassMetadataFactory;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class XmlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XmlFileLoader
     */
    private $loader;
    /**
     * @var ClassMetadata
     */
    private $metadata;

    protected function setUp()
    {
        $this->loader = new XmlFileLoader(__DIR__.'/../../Fixtures/serialization.xml');
        $this->metadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Serializer\Mapping\Loader\LoaderInterface', $this->loader);
    }

    public function testLoadClassMetadataReturnsTrueIfSuccessful()
    {
        $this->assertTrue($this->loader->loadClassMetadata($this->metadata));
    }

    public function testLoadClassMetadata()
    {
        $this->loader->loadClassMetadata($this->metadata);

        $this->assertEquals(TestClassMetadataFactory::createXmlCLassMetadata(), $this->metadata);
    }

    public function testMaxDepth()
    {
        $classMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\MaxDepthDummy');
        $this->loader->loadClassMetadata($classMetadata);

        $attributesMetadata = $classMetadata->getAttributesMetadata();
        $this->assertEquals(2, $attributesMetadata['foo']->getMaxDepth());
        $this->assertEquals(3, $attributesMetadata['bar']->getMaxDepth());
    }
}
