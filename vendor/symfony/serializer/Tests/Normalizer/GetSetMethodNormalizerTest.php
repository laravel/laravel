<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Tests\Fixtures\CircularReferenceDummy;
use Symfony\Component\Serializer\Tests\Fixtures\MaxDepthDummy;
use Symfony\Component\Serializer\Tests\Fixtures\SiblingHolder;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Tests\Fixtures\GroupDummy;

class GetSetMethodNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GetSetMethodNormalizer
     */
    private $normalizer;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp()
    {
        $this->serializer = $this->getMock(__NAMESPACE__.'\SerializerNormalizer');
        $this->normalizer = new GetSetMethodNormalizer();
        $this->normalizer->setSerializer($this->serializer);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\NormalizerInterface', $this->normalizer);
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\DenormalizerInterface', $this->normalizer);
    }

    public function testNormalize()
    {
        $obj = new GetSetDummy();
        $object = new \stdClass();
        $obj->setFoo('foo');
        $obj->setBar('bar');
        $obj->setBaz(true);
        $obj->setCamelCase('camelcase');
        $obj->setObject($object);

        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->with($object, 'any')
            ->will($this->returnValue('string_object'))
        ;

        $this->assertEquals(
            array(
                'foo' => 'foo',
                'bar' => 'bar',
                'baz' => true,
                'fooBar' => 'foobar',
                'camelCase' => 'camelcase',
                'object' => 'string_object',
            ),
            $this->normalizer->normalize($obj, 'any')
        );
    }

    public function testDenormalize()
    {
        $obj = $this->normalizer->denormalize(
            array('foo' => 'foo', 'bar' => 'bar', 'baz' => true, 'fooBar' => 'foobar'),
            __NAMESPACE__.'\GetSetDummy',
            'any'
        );
        $this->assertEquals('foo', $obj->getFoo());
        $this->assertEquals('bar', $obj->getBar());
        $this->assertTrue($obj->isBaz());
    }

    public function testDenormalizeWithObject()
    {
        $data = new \stdClass();
        $data->foo = 'foo';
        $data->bar = 'bar';
        $data->fooBar = 'foobar';
        $obj = $this->normalizer->denormalize($data, __NAMESPACE__.'\GetSetDummy', 'any');
        $this->assertEquals('foo', $obj->getFoo());
        $this->assertEquals('bar', $obj->getBar());
    }

    public function testDenormalizeNull()
    {
        $this->assertEquals(new GetSetDummy(), $this->normalizer->denormalize(null, __NAMESPACE__.'\GetSetDummy'));
    }

    public function testConstructorDenormalize()
    {
        $obj = $this->normalizer->denormalize(
            array('foo' => 'foo', 'bar' => 'bar', 'baz' => true, 'fooBar' => 'foobar'),
            __NAMESPACE__.'\GetConstructorDummy', 'any');
        $this->assertEquals('foo', $obj->getFoo());
        $this->assertEquals('bar', $obj->getBar());
        $this->assertTrue($obj->isBaz());
    }

    public function testConstructorDenormalizeWithNullArgument()
    {
        $obj = $this->normalizer->denormalize(
            array('foo' => 'foo', 'bar' => null, 'baz' => true),
            __NAMESPACE__.'\GetConstructorDummy', 'any');
        $this->assertEquals('foo', $obj->getFoo());
        $this->assertNull($obj->getBar());
        $this->assertTrue($obj->isBaz());
    }

    public function testConstructorDenormalizeWithMissingOptionalArgument()
    {
        $obj = $this->normalizer->denormalize(
            array('foo' => 'test', 'baz' => array(1, 2, 3)),
            __NAMESPACE__.'\GetConstructorOptionalArgsDummy', 'any');
        $this->assertEquals('test', $obj->getFoo());
        $this->assertEquals(array(), $obj->getBar());
        $this->assertEquals(array(1, 2, 3), $obj->getBaz());
    }

    public function testConstructorDenormalizeWithOptionalDefaultArgument()
    {
        $obj = $this->normalizer->denormalize(
            array('bar' => 'test'),
            __NAMESPACE__.'\GetConstructorArgsWithDefaultValueDummy', 'any');
        $this->assertEquals(array(), $obj->getFoo());
        $this->assertEquals('test', $obj->getBar());
    }

    /**
     * @requires PHP 5.6
     */
    public function testConstructorDenormalizeWithVariadicArgument()
    {
        $obj = $this->normalizer->denormalize(
            array('foo' => array(1, 2, 3)),
            'Symfony\Component\Serializer\Tests\Fixtures\VariadicConstructorArgsDummy', 'any');
        $this->assertEquals(array(1, 2, 3), $obj->getFoo());
    }

    /**
     * @requires PHP 5.6
     */
    public function testConstructorDenormalizeWithMissingVariadicArgument()
    {
        $obj = $this->normalizer->denormalize(
            array(),
            'Symfony\Component\Serializer\Tests\Fixtures\VariadicConstructorArgsDummy', 'any');
        $this->assertEquals(array(), $obj->getFoo());
    }

    public function testConstructorWithObjectDenormalize()
    {
        $data = new \stdClass();
        $data->foo = 'foo';
        $data->bar = 'bar';
        $data->baz = true;
        $data->fooBar = 'foobar';
        $obj = $this->normalizer->denormalize($data, __NAMESPACE__.'\GetConstructorDummy', 'any');
        $this->assertEquals('foo', $obj->getFoo());
        $this->assertEquals('bar', $obj->getBar());
    }

    public function testConstructorWArgWithPrivateMutator()
    {
        $obj = $this->normalizer->denormalize(array('foo' => 'bar'), __NAMESPACE__.'\ObjectConstructorArgsWithPrivateMutatorDummy', 'any');
        $this->assertEquals('bar', $obj->getFoo());
    }

    public function testGroupsNormalize()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->normalizer = new GetSetMethodNormalizer($classMetadataFactory);
        $this->normalizer->setSerializer($this->serializer);

        $obj = new GroupDummy();
        $obj->setFoo('foo');
        $obj->setBar('bar');
        $obj->setFooBar('fooBar');
        $obj->setSymfony('symfony');
        $obj->setKevin('kevin');
        $obj->setCoopTilleuls('coopTilleuls');

        $this->assertEquals(array(
            'bar' => 'bar',
        ), $this->normalizer->normalize($obj, null, array(GetSetMethodNormalizer::GROUPS => array('c'))));

        $this->assertEquals(array(
            'symfony' => 'symfony',
            'foo' => 'foo',
            'fooBar' => 'fooBar',
            'bar' => 'bar',
            'kevin' => 'kevin',
            'coopTilleuls' => 'coopTilleuls',
        ), $this->normalizer->normalize($obj, null, array(GetSetMethodNormalizer::GROUPS => array('a', 'c'))));
    }

    public function testGroupsDenormalize()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->normalizer = new GetSetMethodNormalizer($classMetadataFactory);
        $this->normalizer->setSerializer($this->serializer);

        $obj = new GroupDummy();
        $obj->setFoo('foo');

        $toNormalize = array('foo' => 'foo', 'bar' => 'bar');

        $normalized = $this->normalizer->denormalize(
            $toNormalize,
            'Symfony\Component\Serializer\Tests\Fixtures\GroupDummy',
            null,
            array(GetSetMethodNormalizer::GROUPS => array('a'))
        );
        $this->assertEquals($obj, $normalized);

        $obj->setBar('bar');

        $normalized = $this->normalizer->denormalize(
            $toNormalize,
            'Symfony\Component\Serializer\Tests\Fixtures\GroupDummy',
            null,
            array(GetSetMethodNormalizer::GROUPS => array('a', 'b'))
        );
        $this->assertEquals($obj, $normalized);
    }

    public function testGroupsNormalizeWithNameConverter()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->normalizer = new GetSetMethodNormalizer($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter());
        $this->normalizer->setSerializer($this->serializer);

        $obj = new GroupDummy();
        $obj->setFooBar('@dunglas');
        $obj->setSymfony('@coopTilleuls');
        $obj->setCoopTilleuls('les-tilleuls.coop');

        $this->assertEquals(
            array(
                'bar' => null,
                'foo_bar' => '@dunglas',
                'symfony' => '@coopTilleuls',
            ),
            $this->normalizer->normalize($obj, null, array(GetSetMethodNormalizer::GROUPS => array('name_converter')))
        );
    }

    public function testGroupsDenormalizeWithNameConverter()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->normalizer = new GetSetMethodNormalizer($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter());
        $this->normalizer->setSerializer($this->serializer);

        $obj = new GroupDummy();
        $obj->setFooBar('@dunglas');
        $obj->setSymfony('@coopTilleuls');

        $this->assertEquals(
            $obj,
            $this->normalizer->denormalize(array(
                'bar' => null,
                'foo_bar' => '@dunglas',
                'symfony' => '@coopTilleuls',
                'coop_tilleuls' => 'les-tilleuls.coop',
            ), 'Symfony\Component\Serializer\Tests\Fixtures\GroupDummy', null, array(GetSetMethodNormalizer::GROUPS => array('name_converter')))
        );
    }

    /**
     * @dataProvider provideCallbacks
     */
    public function testCallbacks($callbacks, $value, $result, $message)
    {
        $this->normalizer->setCallbacks($callbacks);

        $obj = new GetConstructorDummy('', $value, true);

        $this->assertEquals(
            $result,
            $this->normalizer->normalize($obj, 'any'),
            $message
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUncallableCallbacks()
    {
        $this->normalizer->setCallbacks(array('bar' => null));

        $obj = new GetConstructorDummy('baz', 'quux', true);

        $this->normalizer->normalize($obj, 'any');
    }

    public function testIgnoredAttributes()
    {
        $this->normalizer->setIgnoredAttributes(array('foo', 'bar', 'baz', 'camelCase', 'object'));

        $obj = new GetSetDummy();
        $obj->setFoo('foo');
        $obj->setBar('bar');
        $obj->setBaz(true);

        $this->assertEquals(
            array('fooBar' => 'foobar'),
            $this->normalizer->normalize($obj, 'any')
        );
    }

    public function provideCallbacks()
    {
        return array(
            array(
                array(
                    'bar' => function ($bar) {
                        return 'baz';
                    },
                ),
                'baz',
                array('foo' => '', 'bar' => 'baz', 'baz' => true),
                'Change a string',
            ),
            array(
                array(
                    'bar' => function ($bar) {
                    },
                ),
                'baz',
                array('foo' => '', 'bar' => null, 'baz' => true),
                'Null an item',
            ),
            array(
                array(
                    'bar' => function ($bar) {
                        return $bar->format('d-m-Y H:i:s');
                    },
                ),
                new \DateTime('2011-09-10 06:30:00'),
                array('foo' => '', 'bar' => '10-09-2011 06:30:00', 'baz' => true),
                'Format a date',
            ),
            array(
                array(
                    'bar' => function ($bars) {
                        $foos = '';
                        foreach ($bars as $bar) {
                            $foos .= $bar->getFoo();
                        }

                        return $foos;
                    },
                ),
                array(new GetConstructorDummy('baz', '', false), new GetConstructorDummy('quux', '', false)),
                array('foo' => '', 'bar' => 'bazquux', 'baz' => true),
                'Collect a property',
            ),
            array(
                array(
                    'bar' => function ($bars) {
                        return count($bars);
                    },
                ),
                array(new GetConstructorDummy('baz', '', false), new GetConstructorDummy('quux', '', false)),
                array('foo' => '', 'bar' => 2, 'baz' => true),
                'Count a property',
            ),
        );
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\LogicException
     * @expectedExceptionMessage Cannot normalize attribute "object" because the injected serializer is not a normalizer
     */
    public function testUnableToNormalizeObjectAttribute()
    {
        $serializer = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
        $this->normalizer->setSerializer($serializer);

        $obj = new GetSetDummy();
        $object = new \stdClass();
        $obj->setObject($object);

        $this->normalizer->normalize($obj, 'any');
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\CircularReferenceException
     */
    public function testUnableToNormalizeCircularReference()
    {
        $serializer = new Serializer(array($this->normalizer));
        $this->normalizer->setSerializer($serializer);
        $this->normalizer->setCircularReferenceLimit(2);

        $obj = new CircularReferenceDummy();

        $this->normalizer->normalize($obj);
    }

    public function testSiblingReference()
    {
        $serializer = new Serializer(array($this->normalizer));
        $this->normalizer->setSerializer($serializer);

        $siblingHolder = new SiblingHolder();

        $expected = array(
            'sibling0' => array('coopTilleuls' => 'Les-Tilleuls.coop'),
            'sibling1' => array('coopTilleuls' => 'Les-Tilleuls.coop'),
            'sibling2' => array('coopTilleuls' => 'Les-Tilleuls.coop'),
        );
        $this->assertEquals($expected, $this->normalizer->normalize($siblingHolder));
    }

    public function testCircularReferenceHandler()
    {
        $serializer = new Serializer(array($this->normalizer));
        $this->normalizer->setSerializer($serializer);
        $this->normalizer->setCircularReferenceHandler(function ($obj) {
            return get_class($obj);
        });

        $obj = new CircularReferenceDummy();

        $expected = array('me' => 'Symfony\Component\Serializer\Tests\Fixtures\CircularReferenceDummy');
        $this->assertEquals($expected, $this->normalizer->normalize($obj));
    }

    public function testObjectToPopulate()
    {
        $dummy = new GetSetDummy();
        $dummy->setFoo('foo');

        $obj = $this->normalizer->denormalize(
            array('bar' => 'bar'),
            __NAMESPACE__.'\GetSetDummy',
            null,
            array(GetSetMethodNormalizer::OBJECT_TO_POPULATE => $dummy)
        );

        $this->assertEquals($dummy, $obj);
        $this->assertEquals('foo', $obj->getFoo());
        $this->assertEquals('bar', $obj->getBar());
    }

    public function testDenormalizeNonExistingAttribute()
    {
        $this->assertEquals(
            new GetSetDummy(),
            $this->normalizer->denormalize(array('non_existing' => true), __NAMESPACE__.'\GetSetDummy')
        );
    }

    public function testDenormalizeShouldNotSetStaticAttribute()
    {
        $obj = $this->normalizer->denormalize(array('staticObject' => true), __NAMESPACE__.'\GetSetDummy');

        $this->assertEquals(new GetSetDummy(), $obj);
        $this->assertNull(GetSetDummy::getStaticObject());
    }

    public function testNoTraversableSupport()
    {
        $this->assertFalse($this->normalizer->supportsNormalization(new \ArrayObject()));
    }

    public function testNoStaticGetSetSupport()
    {
        $this->assertFalse($this->normalizer->supportsNormalization(new ObjectWithJustStaticSetterDummy()));
    }

    public function testPrivateSetter()
    {
        $obj = $this->normalizer->denormalize(array('foo' => 'foobar'), __NAMESPACE__.'\ObjectWithPrivateSetterDummy');
        $this->assertEquals('bar', $obj->getFoo());
    }

    public function testMaxDepth()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->normalizer = new GetSetMethodNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($this->normalizer));
        $this->normalizer->setSerializer($serializer);

        $level1 = new MaxDepthDummy();
        $level1->bar = 'level1';

        $level2 = new MaxDepthDummy();
        $level2->bar = 'level2';
        $level1->child = $level2;

        $level3 = new MaxDepthDummy();
        $level3->bar = 'level3';
        $level2->child = $level3;

        $level4 = new MaxDepthDummy();
        $level4->bar = 'level4';
        $level3->child = $level4;

        $result = $serializer->normalize($level1, null, array(GetSetMethodNormalizer::ENABLE_MAX_DEPTH => true));

        $expected = array(
            'bar' => 'level1',
            'child' => array(
                    'bar' => 'level2',
                    'child' => array(
                            'bar' => 'level3',
                            'child' => array(
                                    'child' => null,
                                ),
                        ),
                ),
            );

        $this->assertEquals($expected, $result);
    }
}

class GetSetDummy
{
    protected $foo;
    private $bar;
    private $baz;
    protected $camelCase;
    protected $object;
    private static $staticObject;

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    public function isBaz()
    {
        return $this->baz;
    }

    public function setBaz($baz)
    {
        $this->baz = $baz;
    }

    public function getFooBar()
    {
        return $this->foo.$this->bar;
    }

    public function getCamelCase()
    {
        return $this->camelCase;
    }

    public function setCamelCase($camelCase)
    {
        $this->camelCase = $camelCase;
    }

    public function otherMethod()
    {
        throw new \RuntimeException('Dummy::otherMethod() should not be called');
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    public static function getStaticObject()
    {
        return self::$staticObject;
    }

    public static function setStaticObject($object)
    {
        self::$staticObject = $object;
    }

    protected function getPrivate()
    {
        throw new \RuntimeException('Dummy::getPrivate() should not be called');
    }
}

class GetConstructorDummy
{
    protected $foo;
    private $bar;
    private $baz;

    public function __construct($foo, $bar, $baz)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function isBaz()
    {
        return $this->baz;
    }

    public function otherMethod()
    {
        throw new \RuntimeException('Dummy::otherMethod() should not be called');
    }
}

abstract class SerializerNormalizer implements SerializerInterface, NormalizerInterface
{
}

class GetConstructorOptionalArgsDummy
{
    protected $foo;
    private $bar;
    private $baz;

    public function __construct($foo, $bar = array(), $baz = array())
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function getBaz()
    {
        return $this->baz;
    }

    public function otherMethod()
    {
        throw new \RuntimeException('Dummy::otherMethod() should not be called');
    }
}

class GetConstructorArgsWithDefaultValueDummy
{
    protected $foo;
    protected $bar;

    public function __construct($foo = array(), $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function otherMethod()
    {
        throw new \RuntimeException('Dummy::otherMethod() should not be called');
    }
}

class GetCamelizedDummy
{
    private $kevinDunglas;
    private $fooBar;
    private $bar_foo;

    public function __construct($kevinDunglas = null)
    {
        $this->kevinDunglas = $kevinDunglas;
    }

    public function getKevinDunglas()
    {
        return $this->kevinDunglas;
    }

    public function setFooBar($fooBar)
    {
        $this->fooBar = $fooBar;
    }

    public function getFooBar()
    {
        return $this->fooBar;
    }

    public function setBar_foo($bar_foo)
    {
        $this->bar_foo = $bar_foo;
    }

    public function getBar_foo()
    {
        return $this->bar_foo;
    }
}

class ObjectConstructorArgsWithPrivateMutatorDummy
{
    private $foo;

    public function __construct($foo)
    {
        $this->setFoo($foo);
    }

    public function getFoo()
    {
        return $this->foo;
    }

    private function setFoo($foo)
    {
        $this->foo = $foo;
    }
}

class ObjectWithPrivateSetterDummy
{
    private $foo = 'bar';

    public function getFoo()
    {
        return $this->foo;
    }

    private function setFoo($foo)
    {
    }
}

class ObjectWithJustStaticSetterDummy
{
    private static $foo = 'bar';

    public static function getFoo()
    {
        return self::$foo;
    }

    public static function setFoo($foo)
    {
        self::$foo = $foo;
    }
}
