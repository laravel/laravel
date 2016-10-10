<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\Store;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    protected $request;
    protected $response;
    protected $store;

    protected function setUp()
    {
        $this->request = Request::create('/');
        $this->response = new Response('hello world', 200, array());

        HttpCacheTestCase::clearDirectory(sys_get_temp_dir().'/http_cache');

        $this->store = new Store(sys_get_temp_dir().'/http_cache');
    }

    protected function tearDown()
    {
        $this->store = null;
        $this->request = null;
        $this->response = null;

        HttpCacheTestCase::clearDirectory(sys_get_temp_dir().'/http_cache');
    }

    public function testReadsAnEmptyArrayWithReadWhenNothingCachedAtKey()
    {
        $this->assertEmpty($this->getStoreMetadata('/nothing'));
    }

    public function testUnlockFileThatDoesExist()
    {
        $cacheKey = $this->storeSimpleEntry();
        $this->store->lock($this->request);

        $this->assertTrue($this->store->unlock($this->request));
    }

    public function testUnlockFileThatDoesNotExist()
    {
        $this->assertFalse($this->store->unlock($this->request));
    }

    public function testRemovesEntriesForKeyWithPurge()
    {
        $request = Request::create('/foo');
        $this->store->write($request, new Response('foo'));

        $metadata = $this->getStoreMetadata($request);
        $this->assertNotEmpty($metadata);

        $this->assertTrue($this->store->purge('/foo'));
        $this->assertEmpty($this->getStoreMetadata($request));

        // cached content should be kept after purging
        $path = $this->store->getPath($metadata[0][1]['x-content-digest'][0]);
        $this->assertTrue(is_file($path));

        $this->assertFalse($this->store->purge('/bar'));
    }

    public function testStoresACacheEntry()
    {
        $cacheKey = $this->storeSimpleEntry();

        $this->assertNotEmpty($this->getStoreMetadata($cacheKey));
    }

    public function testSetsTheXContentDigestResponseHeaderBeforeStoring()
    {
        $cacheKey = $this->storeSimpleEntry();
        $entries = $this->getStoreMetadata($cacheKey);
        list ($req, $res) = $entries[0];

        $this->assertEquals('en9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08', $res['x-content-digest'][0]);
    }

    public function testFindsAStoredEntryWithLookup()
    {
        $this->storeSimpleEntry();
        $response = $this->store->lookup($this->request);

        $this->assertNotNull($response);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testDoesNotFindAnEntryWithLookupWhenNoneExists()
    {
        $request = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar'));

        $this->assertNull($this->store->lookup($request));
    }

    public function testCanonizesUrlsForCacheKeys()
    {
        $this->storeSimpleEntry($path = '/test?x=y&p=q');
        $hitsReq = Request::create($path);
        $missReq = Request::create('/test?p=x');

        $this->assertNotNull($this->store->lookup($hitsReq));
        $this->assertNull($this->store->lookup($missReq));
    }

    public function testDoesNotFindAnEntryWithLookupWhenTheBodyDoesNotExist()
    {
        $this->storeSimpleEntry();
        $this->assertNotNull($this->response->headers->get('X-Content-Digest'));
        $path = $this->getStorePath($this->response->headers->get('X-Content-Digest'));
        @unlink($path);
        $this->assertNull($this->store->lookup($this->request));
    }

    public function testRestoresResponseHeadersProperlyWithLookup()
    {
        $this->storeSimpleEntry();
        $response = $this->store->lookup($this->request);

        $this->assertEquals($response->headers->all(), array_merge(array('content-length' => 4, 'x-body-file' => array($this->getStorePath($response->headers->get('X-Content-Digest')))), $this->response->headers->all()));
    }

    public function testRestoresResponseContentFromEntityStoreWithLookup()
    {
        $this->storeSimpleEntry();
        $response = $this->store->lookup($this->request);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test')), $response->getContent());
    }

    public function testInvalidatesMetaAndEntityStoreEntriesWithInvalidate()
    {
        $this->storeSimpleEntry();
        $this->store->invalidate($this->request);
        $response = $this->store->lookup($this->request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertFalse($response->isFresh());
    }

    public function testSucceedsQuietlyWhenInvalidateCalledWithNoMatchingEntries()
    {
        $req = Request::create('/test');
        $this->store->invalidate($req);
        $this->assertNull($this->store->lookup($this->request));
    }

    public function testDoesNotReturnEntriesThatVaryWithLookup()
    {
        $req1 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar'));
        $req2 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Bling', 'HTTP_BAR' => 'Bam'));
        $res = new Response('test', 200, array('Vary' => 'Foo Bar'));
        $this->store->write($req1, $res);

        $this->assertNull($this->store->lookup($req2));
    }

    public function testStoresMultipleResponsesForEachVaryCombination()
    {
        $req1 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar'));
        $res1 = new Response('test 1', 200, array('Vary' => 'Foo Bar'));
        $key = $this->store->write($req1, $res1);

        $req2 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Bling', 'HTTP_BAR' => 'Bam'));
        $res2 = new Response('test 2', 200, array('Vary' => 'Foo Bar'));
        $this->store->write($req2, $res2);

        $req3 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Baz', 'HTTP_BAR' => 'Boom'));
        $res3 = new Response('test 3', 200, array('Vary' => 'Foo Bar'));
        $this->store->write($req3, $res3);

        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 3')), $this->store->lookup($req3)->getContent());
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 2')), $this->store->lookup($req2)->getContent());
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 1')), $this->store->lookup($req1)->getContent());

        $this->assertCount(3, $this->getStoreMetadata($key));
    }

    public function testOverwritesNonVaryingResponseWithStore()
    {
        $req1 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar'));
        $res1 = new Response('test 1', 200, array('Vary' => 'Foo Bar'));
        $key = $this->store->write($req1, $res1);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 1')), $this->store->lookup($req1)->getContent());

        $req2 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Bling', 'HTTP_BAR' => 'Bam'));
        $res2 = new Response('test 2', 200, array('Vary' => 'Foo Bar'));
        $this->store->write($req2, $res2);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 2')), $this->store->lookup($req2)->getContent());

        $req3 = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar'));
        $res3 = new Response('test 3', 200, array('Vary' => 'Foo Bar'));
        $key = $this->store->write($req3, $res3);
        $this->assertEquals($this->getStorePath('en'.hash('sha256', 'test 3')), $this->store->lookup($req3)->getContent());

        $this->assertCount(2, $this->getStoreMetadata($key));
    }

    public function testLocking()
    {
        $req = Request::create('/test', 'get', array(), array(), array(), array('HTTP_FOO' => 'Foo', 'HTTP_BAR' => 'Bar'));
        $this->assertTrue($this->store->lock($req));

        $path = $this->store->lock($req);
        $this->assertTrue($this->store->isLocked($req));

        $this->store->unlock($req);
        $this->assertFalse($this->store->isLocked($req));
    }

    protected function storeSimpleEntry($path = null, $headers = array())
    {
        if (null === $path) {
            $path = '/test';
        }

        $this->request = Request::create($path, 'get', array(), array(), array(), $headers);
        $this->response = new Response('test', 200, array('Cache-Control' => 'max-age=420'));

        return $this->store->write($this->request, $this->response);
    }

    protected function getStoreMetadata($key)
    {
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('getMetadata');
        $m->setAccessible(true);

        if ($key instanceof Request) {
            $m1 = $r->getMethod('getCacheKey');
            $m1->setAccessible(true);
            $key = $m1->invoke($this->store, $key);
        }

        return $m->invoke($this->store, $key);
    }

    protected function getStorePath($key)
    {
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('getPath');
        $m->setAccessible(true);

        return $m->invoke($this->store, $key);
    }
}
