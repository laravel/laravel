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

use Symfony\Component\HttpKernel\Profiler\MongoDbProfilerStorage;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DummyMongoDbProfilerStorage extends MongoDbProfilerStorage
{
    public function getMongo()
    {
        return parent::getMongo();
    }
}

class MongoDbProfilerStorageTestDataCollector extends DataCollector
{
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    public function getName()
    {
        return 'test_data_collector';
    }
}

class MongoDbProfilerStorageTest extends AbstractProfilerStorageTest
{
    protected static $storage;

    public static function setUpBeforeClass()
    {
        if (extension_loaded('mongo')) {
            self::$storage = new DummyMongoDbProfilerStorage('mongodb://localhost/symfony_tests/profiler_data', '', '', 86400);
            try {
                self::$storage->getMongo();
            } catch (\MongoConnectionException $e) {
                self::$storage = null;
            }
        }
    }

    public static function tearDownAfterClass()
    {
        if (self::$storage) {
            self::$storage->purge();
            self::$storage = null;
        }
    }

    public function getDsns()
    {
        return array(
            array('mongodb://localhost/symfony_tests/profiler_data', array(
                'mongodb://localhost/symfony_tests',
                'symfony_tests',
                'profiler_data',
            )),
            array('mongodb://user:password@localhost/symfony_tests/profiler_data', array(
                'mongodb://user:password@localhost/symfony_tests',
                'symfony_tests',
                'profiler_data',
            )),
            array('mongodb://user:password@localhost/admin/symfony_tests/profiler_data', array(
                'mongodb://user:password@localhost/admin',
                'symfony_tests',
                'profiler_data',
            )),
            array('mongodb://user:password@localhost:27009,localhost:27010/?replicaSet=rs-name&authSource=admin/symfony_tests/profiler_data', array(
                'mongodb://user:password@localhost:27009,localhost:27010/?replicaSet=rs-name&authSource=admin',
                'symfony_tests',
                'profiler_data',
            )),
        );
    }

    public function testCleanup()
    {
        $dt = new \DateTime('-2 day');
        for ($i = 0; $i < 3; $i++) {
            $dt->modify('-1 day');
            $profile = new Profile('time_'.$i);
            $profile->setTime($dt->getTimestamp());
            $profile->setMethod('GET');
            self::$storage->write($profile);
        }
        $records = self::$storage->find('', '', 3, 'GET');
        $this->assertCount(1, $records, '->find() returns only one record');
        $this->assertEquals($records[0]['token'], 'time_2', '->find() returns the latest added record');
        self::$storage->purge();
    }

    /**
     * @dataProvider getDsns
     */
    public function testDsnParser($dsn, $expected)
    {
        $m = new \ReflectionMethod(self::$storage, 'parseDsn');
        $m->setAccessible(true);

        $this->assertEquals($expected, $m->invoke(self::$storage, $dsn));
    }

    public function testUtf8()
    {
        $profile = new Profile('utf8_test_profile');

        $data = 'HЁʃʃϿ, ϢorЃd!';
        $nonUtf8Data = mb_convert_encoding($data, 'UCS-2');

        $collector = new MongoDbProfilerStorageTestDataCollector();
        $collector->setData($nonUtf8Data);

        $profile->setCollectors(array($collector));

        self::$storage->write($profile);

        $readProfile = self::$storage->read('utf8_test_profile');
        $collectors = $readProfile->getCollectors();

        $this->assertCount(1, $collectors);
        $this->assertArrayHasKey('test_data_collector', $collectors);
        $this->assertEquals($nonUtf8Data, $collectors['test_data_collector']->getData(), 'Non-UTF8 data is properly encoded/decoded');
    }

    /**
     * @return \Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface
     */
    protected function getStorage()
    {
        return self::$storage;
    }

    protected function setUp()
    {
        if (self::$storage) {
            self::$storage->purge();
        } else {
            $this->markTestSkipped('MongoDbProfilerStorageTest requires the mongo PHP extension and a MongoDB server on localhost');
        }
    }
}
