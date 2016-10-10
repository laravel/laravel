<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Encoder;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;

class JsonEncoderTest extends \PHPUnit_Framework_TestCase
{
    private $encoder;
    private $serializer;

    protected function setUp()
    {
        $this->encoder = new JsonEncoder();
        $this->serializer = new Serializer(array(new CustomNormalizer()), array('json' => new JsonEncoder()));
    }

    public function testEncodeScalar()
    {
        $obj = new \stdClass();
        $obj->foo = 'foo';

        $expected = '{"foo":"foo"}';

        $this->assertEquals($expected, $this->encoder->encode($obj, 'json'));
    }

    public function testComplexObject()
    {
        $obj = $this->getObject();

        $expected = $this->getJsonSource();

        $this->assertEquals($expected, $this->encoder->encode($obj, 'json'));
    }

    public function testOptions()
    {
        $context = array('json_encode_options' => JSON_NUMERIC_CHECK);

        $arr = array();
        $arr['foo'] = '3';

        $expected = '{"foo":3}';

        $this->assertEquals($expected, $this->serializer->serialize($arr, 'json', $context));

        $arr = array();
        $arr['foo'] = '3';

        $expected = '{"foo":"3"}';

        $this->assertEquals($expected, $this->serializer->serialize($arr, 'json'), 'Context should not be persistent');
    }

    protected function getJsonSource()
    {
        return '{"foo":"foo","bar":["a","b"],"baz":{"key":"val","key2":"val","A B":"bar","item":[{"title":"title1"},{"title":"title2"}],"Barry":{"FooBar":{"Baz":"Ed","@id":1}}},"qux":"1"}';
    }

    protected function getObject()
    {
        $obj = new \stdClass();
        $obj->foo = 'foo';
        $obj->bar = array('a', 'b');
        $obj->baz = array('key' => 'val', 'key2' => 'val', 'A B' => 'bar', 'item' => array(array('title' => 'title1'), array('title' => 'title2')), 'Barry' => array('FooBar' => array('Baz' => 'Ed', '@id' => 1)));
        $obj->qux = '1';

        return $obj;
    }
}
