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

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class PdoSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $pdo;

    protected function setUp()
    {
        if (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('This test requires SQLite support in your environment');
        }

        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $sql = 'CREATE TABLE sessions (sess_id VARCHAR(128) PRIMARY KEY, sess_data TEXT, sess_time INTEGER)';
        $this->pdo->exec($sql);
    }

    public function testIncompleteOptions()
    {
        $this->setExpectedException('InvalidArgumentException');
        $storage = new PdoSessionHandler($this->pdo, array());
    }

    public function testWrongPdoErrMode()
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        $pdo->exec('CREATE TABLE sessions (sess_id VARCHAR(128) PRIMARY KEY, sess_data TEXT, sess_time INTEGER)');

        $this->setExpectedException('InvalidArgumentException');
        $storage = new PdoSessionHandler($pdo, array('db_table' => 'sessions'));
    }

    public function testWrongTableOptionsWrite()
    {
        $storage = new PdoSessionHandler($this->pdo, array('db_table' => 'bad_name'));
        $this->setExpectedException('RuntimeException');
        $storage->write('foo', 'bar');
    }

    public function testWrongTableOptionsRead()
    {
        $storage = new PdoSessionHandler($this->pdo, array('db_table' => 'bad_name'));
        $this->setExpectedException('RuntimeException');
        $storage->read('foo', 'bar');
    }

    public function testWriteRead()
    {
        $storage = new PdoSessionHandler($this->pdo, array('db_table' => 'sessions'));
        $storage->write('foo', 'bar');
        $this->assertEquals('bar', $storage->read('foo'), 'written value can be read back correctly');
    }

    public function testMultipleInstances()
    {
        $storage1 = new PdoSessionHandler($this->pdo, array('db_table' => 'sessions'));
        $storage1->write('foo', 'bar');

        $storage2 = new PdoSessionHandler($this->pdo, array('db_table' => 'sessions'));
        $this->assertEquals('bar', $storage2->read('foo'), 'values persist between instances');
    }

    public function testSessionDestroy()
    {
        $storage = new PdoSessionHandler($this->pdo, array('db_table' => 'sessions'));
        $storage->write('foo', 'bar');
        $this->assertCount(1, $this->pdo->query('SELECT * FROM sessions')->fetchAll());

        $storage->destroy('foo');

        $this->assertCount(0, $this->pdo->query('SELECT * FROM sessions')->fetchAll());
    }

    public function testSessionGC()
    {
        $storage = new PdoSessionHandler($this->pdo, array('db_table' => 'sessions'));

        $storage->write('foo', 'bar');
        $storage->write('baz', 'bar');

        $this->assertCount(2, $this->pdo->query('SELECT * FROM sessions')->fetchAll());

        $storage->gc(-1);
        $this->assertCount(0, $this->pdo->query('SELECT * FROM sessions')->fetchAll());
    }

    public function testGetConnection()
    {
        $storage = new PdoSessionHandler($this->pdo, array('db_table' => 'sessions'), array());

        $method = new \ReflectionMethod($storage, 'getConnection');
        $method->setAccessible(true);

        $this->assertInstanceOf('\PDO', $method->invoke($storage));
    }
}
