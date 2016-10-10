<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Transaction;

use PredisTestCase;
use Predis\Client;

/**
 *
 */
class AbortedMultiExecExceptionTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testExceptionClass()
    {
        $client = new Client();
        $transaction = new MultiExecContext($client);
        $exception = new AbortedMultiExecException($transaction, 'ABORTED');

        $this->assertInstanceOf('Predis\PredisException', $exception);
        $this->assertSame('ABORTED', $exception->getMessage());
        $this->assertSame($transaction, $exception->getTransaction());
    }
}
