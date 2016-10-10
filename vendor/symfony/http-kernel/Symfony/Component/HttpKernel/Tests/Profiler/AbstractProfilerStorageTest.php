<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Profiler;

use Symfony\Component\HttpKernel\Profiler\Profile;

abstract class AbstractProfilerStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testStore()
    {
        for ($i = 0; $i < 10; $i ++) {
            $profile = new Profile('token_'.$i);
            $profile->setIp('127.0.0.1');
            $profile->setUrl('http://foo.bar');
            $profile->setMethod('GET');
            $this->getStorage()->write($profile);
        }
        $this->assertCount(10, $this->getStorage()->find('127.0.0.1', 'http://foo.bar', 20, 'GET'), '->write() stores data in the storage');
    }

    public function testChildren()
    {
        $parentProfile = new Profile('token_parent');
        $parentProfile->setIp('127.0.0.1');
        $parentProfile->setUrl('http://foo.bar/parent');

        $childProfile = new Profile('token_child');
        $childProfile->setIp('127.0.0.1');
        $childProfile->setUrl('http://foo.bar/child');

        $parentProfile->addChild($childProfile);

        $this->getStorage()->write($parentProfile);
        $this->getStorage()->write($childProfile);

        // Load them from storage
        $parentProfile = $this->getStorage()->read('token_parent');
        $childProfile  = $this->getStorage()->read('token_child');

        // Check child has link to parent
        $this->assertNotNull($childProfile->getParent());
        $this->assertEquals($parentProfile->getToken(), $childProfile->getParentToken());

        // Check parent has child
        $children = $parentProfile->getChildren();
        $this->assertCount(1, $children);
        $this->assertEquals($childProfile->getToken(), $children[0]->getToken());
    }

    public function testStoreSpecialCharsInUrl()
    {
        // The storage accepts special characters in URLs (Even though URLs are not
        // supposed to contain them)
        $profile = new Profile('simple_quote');
        $profile->setUrl('http://foo.bar/\'');
        $this->getStorage()->write($profile);
        $this->assertTrue(false !== $this->getStorage()->read('simple_quote'), '->write() accepts single quotes in URL');

        $profile = new Profile('double_quote');
        $profile->setUrl('http://foo.bar/"');
        $this->getStorage()->write($profile);
        $this->assertTrue(false !== $this->getStorage()->read('double_quote'), '->write() accepts double quotes in URL');

        $profile = new Profile('backslash');
        $profile->setUrl('http://foo.bar/\\');
        $this->getStorage()->write($profile);
        $this->assertTrue(false !== $this->getStorage()->read('backslash'), '->write() accepts backslash in URL');

        $profile = new Profile('comma');
        $profile->setUrl('http://foo.bar/,');
        $this->getStorage()->write($profile);
        $this->assertTrue(false !== $this->getStorage()->read('comma'), '->write() accepts comma in URL');
    }

    public function testStoreDuplicateToken()
    {
        $profile = new Profile('token');
        $profile->setUrl('http://example.com/');

        $this->assertTrue($this->getStorage()->write($profile), '->write() returns true when the token is unique');

        $profile->setUrl('http://example.net/');

        $this->assertTrue($this->getStorage()->write($profile), '->write() returns true when the token is already present in the storage');
        $this->assertEquals('http://example.net/', $this->getStorage()->read('token')->getUrl(), '->write() overwrites the current profile data');

        $this->assertCount(1, $this->getStorage()->find('', '', 1000, ''), '->find() does not return the same profile twice');
    }

    public function testRetrieveByIp()
    {
        $profile = new Profile('token');
        $profile->setIp('127.0.0.1');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', '', 10, 'GET'), '->find() retrieve a record by IP');
        $this->assertCount(0, $this->getStorage()->find('127.0.%.1', '', 10, 'GET'), '->find() does not interpret a "%" as a wildcard in the IP');
        $this->assertCount(0, $this->getStorage()->find('127.0._.1', '', 10, 'GET'), '->find() does not interpret a "_" as a wildcard in the IP');
    }

    public function testRetrieveByUrl()
    {
        $profile = new Profile('simple_quote');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://foo.bar/\'');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $profile = new Profile('double_quote');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://foo.bar/"');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $profile = new Profile('backslash');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://foo\\bar/');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $profile = new Profile('percent');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://foo.bar/%');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $profile = new Profile('underscore');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://foo.bar/_');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $profile = new Profile('semicolon');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://foo.bar/;');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', 'http://foo.bar/\'', 10, 'GET'), '->find() accepts single quotes in URLs');
        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', 'http://foo.bar/"', 10, 'GET'), '->find() accepts double quotes in URLs');
        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', 'http://foo\\bar/', 10, 'GET'), '->find() accepts backslash in URLs');
        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', 'http://foo.bar/;', 10, 'GET'), '->find() accepts semicolon in URLs');
        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', 'http://foo.bar/%', 10, 'GET'), '->find() does not interpret a "%" as a wildcard in the URL');
        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', 'http://foo.bar/_', 10, 'GET'), '->find() does not interpret a "_" as a wildcard in the URL');
    }

    public function testStoreTime()
    {
        $dt = new \DateTime('now');
        $start = $dt->getTimestamp();

        for ($i = 0; $i < 3; $i++) {
            $dt->modify('+1 minute');
            $profile = new Profile('time_'.$i);
            $profile->setIp('127.0.0.1');
            $profile->setUrl('http://foo.bar');
            $profile->setTime($dt->getTimestamp());
            $profile->setMethod('GET');
            $this->getStorage()->write($profile);
        }

        $records = $this->getStorage()->find('', '', 3, 'GET', $start, time() + 3 * 60);
        $this->assertCount(3, $records, '->find() returns all previously added records');
        $this->assertEquals($records[0]['token'], 'time_2', '->find() returns records ordered by time in descendant order');
        $this->assertEquals($records[1]['token'], 'time_1', '->find() returns records ordered by time in descendant order');
        $this->assertEquals($records[2]['token'], 'time_0', '->find() returns records ordered by time in descendant order');

        $records = $this->getStorage()->find('', '', 3, 'GET', $start, time() + 2 * 60);
        $this->assertCount(2, $records, '->find() should return only first two of the previously added records');
    }

    public function testRetrieveByEmptyUrlAndIp()
    {
        for ($i = 0; $i < 5; $i++) {
            $profile = new Profile('token_'.$i);
            $profile->setMethod('GET');
            $this->getStorage()->write($profile);
        }
        $this->assertCount(5, $this->getStorage()->find('', '', 10, 'GET'), '->find() returns all previously added records');
        $this->getStorage()->purge();
    }

    public function testRetrieveByMethodAndLimit()
    {
        foreach (array('POST', 'GET') as $method) {
            for ($i = 0; $i < 5; $i++) {
                $profile = new Profile('token_'.$i.$method);
                $profile->setMethod($method);
                $this->getStorage()->write($profile);
            }
        }

        $this->assertCount(5, $this->getStorage()->find('', '', 5, 'POST'));

        $this->getStorage()->purge();
    }

    public function testPurge()
    {
        $profile = new Profile('token1');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://example.com/');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $this->assertTrue(false !== $this->getStorage()->read('token1'));
        $this->assertCount(1, $this->getStorage()->find('127.0.0.1', '', 10, 'GET'));

        $profile = new Profile('token2');
        $profile->setIp('127.0.0.1');
        $profile->setUrl('http://example.net/');
        $profile->setMethod('GET');
        $this->getStorage()->write($profile);

        $this->assertTrue(false !== $this->getStorage()->read('token2'));
        $this->assertCount(2, $this->getStorage()->find('127.0.0.1', '', 10, 'GET'));

        $this->getStorage()->purge();

        $this->assertEmpty($this->getStorage()->read('token'), '->purge() removes all data stored by profiler');
        $this->assertCount(0, $this->getStorage()->find('127.0.0.1', '', 10, 'GET'), '->purge() removes all items from index');
    }

    public function testDuplicates()
    {
        for ($i = 1; $i <= 5; $i++) {
            $profile = new Profile('foo'.$i);
            $profile->setIp('127.0.0.1');
            $profile->setUrl('http://example.net/');
            $profile->setMethod('GET');

            ///three duplicates
            $this->getStorage()->write($profile);
            $this->getStorage()->write($profile);
            $this->getStorage()->write($profile);
        }
        $this->assertCount(3, $this->getStorage()->find('127.0.0.1', 'http://example.net/', 3, 'GET'), '->find() method returns incorrect number of entries');
    }

    /**
     * @return \Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface
     */
    abstract protected function getStorage();
}
