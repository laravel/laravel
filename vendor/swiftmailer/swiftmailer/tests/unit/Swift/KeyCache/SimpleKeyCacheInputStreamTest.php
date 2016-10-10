<?php

class Swift_KeyCache_SimpleKeyCacheInputStreamTest extends \PHPUnit_Framework_TestCase
{
    private $_nsKey = 'ns1';

    public function testStreamWritesToCacheInAppendMode()
    {
        $cache = $this->getMock('Swift_KeyCache');
        $cache->expects($this->at(0))
              ->method('setString')
              ->with($this->_nsKey, 'foo', 'a', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(1))
              ->method('setString')
              ->with($this->_nsKey, 'foo', 'b', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(2))
              ->method('setString')
              ->with($this->_nsKey, 'foo', 'c', Swift_KeyCache::MODE_APPEND);

        $stream = new Swift_KeyCache_SimpleKeyCacheInputStream();
        $stream->setKeyCache($cache);
        $stream->setNsKey($this->_nsKey);
        $stream->setItemKey('foo');

        $stream->write('a');
        $stream->write('b');
        $stream->write('c');
    }

    public function testFlushContentClearsKey()
    {
        $cache = $this->getMock('Swift_KeyCache');
        $cache->expects($this->once())
              ->method('clearKey')
              ->with($this->_nsKey, 'foo');

        $stream = new Swift_KeyCache_SimpleKeyCacheInputStream();
        $stream->setKeyCache($cache);
        $stream->setNsKey($this->_nsKey);
        $stream->setItemKey('foo');

        $stream->flushBuffers();
    }

    public function testClonedStreamStillReferencesSameCache()
    {
        $cache = $this->getMock('Swift_KeyCache');
        $cache->expects($this->at(0))
              ->method('setString')
              ->with($this->_nsKey, 'foo', 'a', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(1))
              ->method('setString')
              ->with($this->_nsKey, 'foo', 'b', Swift_KeyCache::MODE_APPEND);
        $cache->expects($this->at(2))
              ->method('setString')
              ->with('test', 'bar', 'x', Swift_KeyCache::MODE_APPEND);

        $stream = new Swift_KeyCache_SimpleKeyCacheInputStream();
        $stream->setKeyCache($cache);
        $stream->setNsKey($this->_nsKey);
        $stream->setItemKey('foo');

        $stream->write('a');
        $stream->write('b');

        $newStream = clone $stream;
        $newStream->setKeyCache($cache);
        $newStream->setNsKey('test');
        $newStream->setItemKey('bar');

        $newStream->write('x');
    }
}
