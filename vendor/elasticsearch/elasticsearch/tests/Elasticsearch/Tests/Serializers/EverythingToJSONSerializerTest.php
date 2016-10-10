<?php

namespace Elasticsearch\Tests\Serializers;

use Elasticsearch\Serializers\EverythingToJSONSerializer;
use PHPUnit_Framework_TestCase;
use Mockery as m;

/**
 * Class EverythingToJSONSerializerTest
 * @package Elasticsearch\Tests\Serializers
 */
class EverythingToJSONSerializerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSerializeArray()
    {
        $serializer = new EverythingToJSONSerializer();
        $body = array('value' => 'field');

        $ret = $serializer->serialize($body);

        $body = json_encode($body);
        $this->assertEquals($body, $ret);
    }

    public function testSerializeString()
    {
        $serializer = new EverythingToJSONSerializer();
        $body = 'abc';

        $ret = $serializer->serialize($body);

        $body = '"abc"';
        $this->assertEquals($body, $ret);
    }

    public function testDeserializeJSON()
    {
        $serializer = new EverythingToJSONSerializer();
        $body = '{"field":"value"}';

        $ret = $serializer->deserialize($body, array());

        $body = json_decode($body, true);
        $this->assertEquals($body, $ret);
    }
}
