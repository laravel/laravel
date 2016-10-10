<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\TestCase;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class RavenHandlerTest extends TestCase
{
    public function setUp()
    {
        if (!class_exists('Raven_Client')) {
            $this->markTestSkipped('raven/raven not installed');
        }

        require_once __DIR__ . '/MockRavenClient.php';
    }

    /**
     * @covers Monolog\Handler\RavenHandler::__construct
     */
    public function testConstruct()
    {
        $handler = new RavenHandler($this->getRavenClient());
        $this->assertInstanceOf('Monolog\Handler\RavenHandler', $handler);
    }

    protected function getHandler($ravenClient)
    {
        $handler = new RavenHandler($ravenClient);

        return $handler;
    }

    protected function getRavenClient()
    {
        $dsn = 'http://43f6017361224d098402974103bfc53d:a6a0538fc2934ba2bed32e08741b2cd3@marca.python.live.cheggnet.com:9000/1';

        return new MockRavenClient($dsn);
    }

    public function testDebug()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $record = $this->getRecord(Logger::DEBUG, 'A test debug message');
        $handler->handle($record);

        $this->assertEquals($ravenClient::DEBUG, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }

    public function testWarning()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $record = $this->getRecord(Logger::WARNING, 'A test warning message');
        $handler->handle($record);

        $this->assertEquals($ravenClient::WARNING, $ravenClient->lastData['level']);
        $this->assertContains($record['message'], $ravenClient->lastData['message']);
    }

    public function testTag()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $tags = array(1, 2, 'foo');
        $record = $this->getRecord(Logger::INFO, 'test', array('tags' => $tags));
        $handler->handle($record);

        $this->assertEquals($tags, $ravenClient->lastData['tags']);
    }

    public function testExtraParameters()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $checksum = '098f6bcd4621d373cade4e832627b4f6';
        $release = '05a671c66aefea124cc08b76ea6d30bb';
        $record = $this->getRecord(Logger::INFO, 'test', array('checksum' => $checksum, 'release' => $release));
        $handler->handle($record);

        $this->assertEquals($checksum, $ravenClient->lastData['checksum']);
        $this->assertEquals($release, $ravenClient->lastData['release']);
    }

    public function testFingerprint()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $fingerprint = array('{{ default }}', 'other value');
        $record = $this->getRecord(Logger::INFO, 'test', array('fingerprint' => $fingerprint));
        $handler->handle($record);

        $this->assertEquals($fingerprint, $ravenClient->lastData['fingerprint']);
    }

    public function testUserContext()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $recordWithNoContext = $this->getRecord(Logger::INFO, 'test with default user context');
        // set user context 'externally'

        $user = array(
            'id' => '123',
            'email' => 'test@test.com',
        );

        $recordWithContext = $this->getRecord(Logger::INFO, 'test', array('user' => $user));

        $ravenClient->user_context(array('id' => 'test_user_id'));
        // handle context
        $handler->handle($recordWithContext);
        $this->assertEquals($user, $ravenClient->lastData['user']);

        // check to see if its reset
        $handler->handle($recordWithNoContext);
        $this->assertInternalType('array', $ravenClient->context->user);
        $this->assertSame('test_user_id', $ravenClient->context->user['id']);

        // handle with null context
        $ravenClient->user_context(null);
        $handler->handle($recordWithContext);
        $this->assertEquals($user, $ravenClient->lastData['user']);

        // check to see if its reset
        $handler->handle($recordWithNoContext);
        $this->assertNull($ravenClient->context->user);
    }

    public function testException()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        try {
            $this->methodThatThrowsAnException();
        } catch (\Exception $e) {
            $record = $this->getRecord(Logger::ERROR, $e->getMessage(), array('exception' => $e));
            $handler->handle($record);
        }

        $this->assertEquals($record['message'], $ravenClient->lastData['message']);
    }

    public function testHandleBatch()
    {
        $records = $this->getMultipleRecords();
        $records[] = $this->getRecord(Logger::WARNING, 'warning');
        $records[] = $this->getRecord(Logger::WARNING, 'warning');

        $logFormatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $logFormatter->expects($this->once())->method('formatBatch');

        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter->expects($this->once())->method('format')->with($this->callback(function ($record) {
            return $record['level'] == 400;
        }));

        $handler = $this->getHandler($this->getRavenClient());
        $handler->setBatchFormatter($logFormatter);
        $handler->setFormatter($formatter);
        $handler->handleBatch($records);
    }

    public function testHandleBatchDoNothingIfRecordsAreBelowLevel()
    {
        $records = array(
            $this->getRecord(Logger::DEBUG, 'debug message 1'),
            $this->getRecord(Logger::DEBUG, 'debug message 2'),
            $this->getRecord(Logger::INFO, 'information'),
        );

        $handler = $this->getMock('Monolog\Handler\RavenHandler', null, array($this->getRavenClient()));
        $handler->expects($this->never())->method('handle');
        $handler->setLevel(Logger::ERROR);
        $handler->handleBatch($records);
    }

    public function testGetSetBatchFormatter()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);

        $handler->setBatchFormatter($formatter = new LineFormatter());
        $this->assertSame($formatter, $handler->getBatchFormatter());
    }

    public function testRelease()
    {
        $ravenClient = $this->getRavenClient();
        $handler = $this->getHandler($ravenClient);
        $release = 'v42.42.42';
        $handler->setRelease($release);
        $record = $this->getRecord(Logger::INFO, 'test');
        $handler->handle($record);
        $this->assertEquals($release, $ravenClient->lastData['release']);

        $localRelease = 'v41.41.41';
        $record = $this->getRecord(Logger::INFO, 'test', array('release' => $localRelease));
        $handler->handle($record);
        $this->assertEquals($localRelease, $ravenClient->lastData['release']);
    }

    private function methodThatThrowsAnException()
    {
        throw new \Exception('This is an exception');
    }
}
