<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class MongoDbSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mongo;
    private $storage;
    public $options;

    protected function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('MongoDbSessionHandler requires the PHP "mongo" extension.');
        }

        $mongoClass = version_compare(phpversion('mongo'), '1.3.0', '<') ? 'Mongo' : 'MongoClient';

        $this->mongo = $this->getMockBuilder($mongoClass)
            ->getMock();

        $this->options = array(
            'id_field'   => '_id',
            'data_field' => 'data',
            'time_field' => 'time',
            'database' => 'sf2-test',
            'collection' => 'session-test',
        );

        $this->storage = new MongoDbSessionHandler($this->mongo, $this->options);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorShouldThrowExceptionForInvalidMongo()
    {
        new MongoDbSessionHandler(new \stdClass(), $this->options);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorShouldThrowExceptionForMissingOptions()
    {
        new MongoDbSessionHandler($this->mongo, array());
    }

    public function testOpenMethodAlwaysReturnTrue()
    {
        $this->assertTrue($this->storage->open('test', 'test'), 'The "open" method should always return true');
    }

    public function testCloseMethodAlwaysReturnTrue()
    {
        $this->assertTrue($this->storage->close(), 'The "close" method should always return true');
    }

    public function testWrite()
    {
        $collection = $this->createMongoCollectionMock();

        $this->mongo->expects($this->once())
            ->method('selectCollection')
            ->with($this->options['database'], $this->options['collection'])
            ->will($this->returnValue($collection));

        $that = $this;
        $data = array();

        $collection->expects($this->once())
            ->method('update')
            ->will($this->returnCallback(function ($criteria, $updateData, $options) use ($that, &$data) {
                $that->assertEquals(array($that->options['id_field'] => 'foo'), $criteria);
                $that->assertEquals(array('upsert' => true, 'multiple' => false), $options);

                $data = $updateData['$set'];
            }));

        $this->assertTrue($this->storage->write('foo', 'bar'));

        $this->assertEquals('bar', $data[$this->options['data_field']]->bin);
        $that->assertInstanceOf('MongoDate', $data[$this->options['time_field']]);
    }

    public function testReplaceSessionData()
    {
        $collection = $this->createMongoCollectionMock();

        $this->mongo->expects($this->once())
            ->method('selectCollection')
            ->with($this->options['database'], $this->options['collection'])
            ->will($this->returnValue($collection));

        $data = array();

        $collection->expects($this->exactly(2))
            ->method('update')
            ->will($this->returnCallback(function ($criteria, $updateData, $options) use (&$data) {
                $data = $updateData;
            }));

        $this->storage->write('foo', 'bar');
        $this->storage->write('foo', 'foobar');

        $this->assertEquals('foobar', $data['$set'][$this->options['data_field']]->bin);
    }

    public function testDestroy()
    {
        $collection = $this->createMongoCollectionMock();

        $this->mongo->expects($this->once())
            ->method('selectCollection')
            ->with($this->options['database'], $this->options['collection'])
            ->will($this->returnValue($collection));

        $collection->expects($this->once())
            ->method('remove')
            ->with(array($this->options['id_field'] => 'foo'));

        $this->assertTrue($this->storage->destroy('foo'));
    }

    public function testGc()
    {
        $collection = $this->createMongoCollectionMock();

        $this->mongo->expects($this->once())
            ->method('selectCollection')
            ->with($this->options['database'], $this->options['collection'])
            ->will($this->returnValue($collection));

        $that = $this;

        $collection->expects($this->once())
            ->method('remove')
            ->will($this->returnCallback(function ($criteria) use ($that) {
                $that->assertInstanceOf('MongoDate', $criteria[$that->options['time_field']]['$lt']);
                $that->assertGreaterThanOrEqual(time() - -1, $criteria[$that->options['time_field']]['$lt']->sec);
            }));

        $this->assertTrue($this->storage->gc(-1));
    }

    public function testGetConnection()
    {
        $method = new \ReflectionMethod($this->storage, 'getMongo');
        $method->setAccessible(true);

        $mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';

        $this->assertInstanceOf($mongoClass, $method->invoke($this->storage));
    }

    private function createMongoCollectionMock()
    {
        $mongoClient = $this->getMockBuilder('MongoClient')
            ->getMock();
        $mongoDb = $this->getMockBuilder('MongoDB')
            ->setConstructorArgs(array($mongoClient, 'database-name'))
            ->getMock();
        $collection = $this->getMockBuilder('MongoCollection')
            ->setConstructorArgs(array($mongoDb, 'collection-name'))
            ->getMock();

        return $collection;
    }
}
