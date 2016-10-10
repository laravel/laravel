<?php

class Swift_KeyCache_ArrayKeyCacheTest extends \PHPUnit_Framework_TestCase
{
    private $_key1 = 'key1';
    private $_key2 = 'key2';

    public function testStringDataCanBeSetAndFetched()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);
        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );
        $this->assertEquals('test', $cache->getString($this->_key1, 'foo'));
    }

    public function testStringDataCanBeOverwritten()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);
        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );
        $cache->setString(
            $this->_key1, 'foo', 'whatever', Swift_KeyCache::MODE_WRITE
            );

        $this->assertEquals('whatever', $cache->getString($this->_key1, 'foo'));
    }

    public function testStringDataCanBeAppended()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);
        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );
        $cache->setString(
            $this->_key1, 'foo', 'ing', Swift_KeyCache::MODE_APPEND
            );

        $this->assertEquals('testing', $cache->getString($this->_key1, 'foo'));
    }

    public function testHasKeyReturnValue()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);
        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );

        $this->assertTrue($cache->hasKey($this->_key1, 'foo'));
    }

    public function testNsKeyIsWellPartitioned()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);
        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );
        $cache->setString(
            $this->_key2, 'foo', 'ing', Swift_KeyCache::MODE_WRITE
            );

        $this->assertEquals('test', $cache->getString($this->_key1, 'foo'));
        $this->assertEquals('ing', $cache->getString($this->_key2, 'foo'));
    }

    public function testItemKeyIsWellPartitioned()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);
        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );
        $cache->setString(
            $this->_key1, 'bar', 'ing', Swift_KeyCache::MODE_WRITE
            );

        $this->assertEquals('test', $cache->getString($this->_key1, 'foo'));
        $this->assertEquals('ing', $cache->getString($this->_key1, 'bar'));
    }

    public function testByteStreamCanBeImported()
    {
        $os = $this->_createOutputStream();
        $os->expects($this->at(0))
           ->method('read')
           ->will($this->returnValue('abc'));
        $os->expects($this->at(1))
           ->method('read')
           ->will($this->returnValue('def'));
        $os->expects($this->at(2))
           ->method('read')
           ->will($this->returnValue(false));

        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);
        $cache->importFromByteStream(
            $this->_key1, 'foo', $os, Swift_KeyCache::MODE_WRITE
            );
        $this->assertEquals('abcdef', $cache->getString($this->_key1, 'foo'));
    }

    public function testByteStreamCanBeAppended()
    {
        $os1 = $this->_createOutputStream();
        $os1->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue('abc'));
        $os1->expects($this->at(1))
            ->method('read')
            ->will($this->returnValue('def'));
        $os1->expects($this->at(2))
            ->method('read')
            ->will($this->returnValue(false));

        $os2 = $this->_createOutputStream();
        $os2->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue('xyz'));
        $os2->expects($this->at(1))
            ->method('read')
            ->will($this->returnValue('uvw'));
        $os2->expects($this->at(2))
            ->method('read')
            ->will($this->returnValue(false));

        $is = $this->_createKeyCacheInputStream(true);

        $cache = $this->_createCache($is);

        $cache->importFromByteStream(
            $this->_key1, 'foo', $os1, Swift_KeyCache::MODE_APPEND
            );
        $cache->importFromByteStream(
            $this->_key1, 'foo', $os2, Swift_KeyCache::MODE_APPEND
            );

        $this->assertEquals('abcdefxyzuvw', $cache->getString($this->_key1, 'foo'));
    }

    public function testByteStreamAndStringCanBeAppended()
    {
        $os = $this->_createOutputStream();
        $os->expects($this->at(0))
           ->method('read')
           ->will($this->returnValue('abc'));
        $os->expects($this->at(1))
           ->method('read')
           ->will($this->returnValue('def'));
        $os->expects($this->at(2))
           ->method('read')
           ->will($this->returnValue(false));

        $is = $this->_createKeyCacheInputStream(true);

        $cache = $this->_createCache($is);

        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_APPEND
            );
        $cache->importFromByteStream(
            $this->_key1, 'foo', $os, Swift_KeyCache::MODE_APPEND
            );
        $this->assertEquals('testabcdef', $cache->getString($this->_key1, 'foo'));
    }

    public function testDataCanBeExportedToByteStream()
    {
        //See acceptance test for more detail
        $is = $this->_createInputStream();
        $is->expects($this->atLeastOnce())
           ->method('write');

        $kcis = $this->_createKeyCacheInputStream(true);

        $cache = $this->_createCache($kcis);

        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );

        $cache->exportToByteStream($this->_key1, 'foo', $is);
    }

    public function testKeyCanBeCleared()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);

        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );
        $this->assertTrue($cache->hasKey($this->_key1, 'foo'));
        $cache->clearKey($this->_key1, 'foo');
        $this->assertFalse($cache->hasKey($this->_key1, 'foo'));
    }

    public function testNsKeyCanBeCleared()
    {
        $is = $this->_createKeyCacheInputStream();
        $cache = $this->_createCache($is);

        $cache->setString(
            $this->_key1, 'foo', 'test', Swift_KeyCache::MODE_WRITE
            );
        $cache->setString(
            $this->_key1, 'bar', 'xyz', Swift_KeyCache::MODE_WRITE
            );
        $this->assertTrue($cache->hasKey($this->_key1, 'foo'));
        $this->assertTrue($cache->hasKey($this->_key1, 'bar'));
        $cache->clearAll($this->_key1);
        $this->assertFalse($cache->hasKey($this->_key1, 'foo'));
        $this->assertFalse($cache->hasKey($this->_key1, 'bar'));
    }

    // -- Creation methods

    private function _createCache($is)
    {
        return new Swift_KeyCache_ArrayKeyCache($is);
    }

    private function _createKeyCacheInputStream()
    {
        return $this->getMock('Swift_KeyCache_KeyCacheInputStream');
    }

    private function _createOutputStream()
    {
        return $this->getMock('Swift_OutputByteStream');
    }

    private function _createInputStream()
    {
        return $this->getMock('Swift_InputByteStream');
    }
}
