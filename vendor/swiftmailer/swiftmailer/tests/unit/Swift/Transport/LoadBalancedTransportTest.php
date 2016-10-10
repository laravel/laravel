<?php

class Swift_Transport_LoadBalancedTransportTest extends \SwiftMailerTestCase
{
    public function testEachTransportIsUsedInTurn()
    {
        $message1 = $this->getMockery('Swift_Mime_Message');
        $message2 = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $testCase = $this;
        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message1, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $testCase) {
               if ($connectionState1) {
                   return 1;
               }
               $testCase->fail();
           });
        $t1->shouldReceive('send')
           ->never()
           ->with($message2, \Mockery::any());

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message2, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $testCase) {
               if ($connectionState2) {
                   return 1;
               }
               $testCase->fail();
           });
        $t2->shouldReceive('send')
           ->never()
           ->with($message1, \Mockery::any());

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $this->assertEquals(1, $transport->send($message1));
        $this->assertEquals(1, $transport->send($message2));
    }

    public function testTransportsAreReusedInRotatingFashion()
    {
        $message1 = $this->getMockery('Swift_Mime_Message');
        $message2 = $this->getMockery('Swift_Mime_Message');
        $message3 = $this->getMockery('Swift_Mime_Message');
        $message4 = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $testCase = $this;
        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message1, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $testCase) {
               if ($connectionState1) {
                   return 1;
               }
               $testCase->fail();
           });
        $t1->shouldReceive('send')
           ->never()
           ->with($message2, \Mockery::any());
        $t1->shouldReceive('send')
           ->once()
           ->with($message3, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $testCase) {
               if ($connectionState1) {
                   return 1;
               }
               $testCase->fail();
           });
        $t1->shouldReceive('send')
           ->never()
           ->with($message4, \Mockery::any());

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message2, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $testCase) {
               if ($connectionState2) {
                   return 1;
               }
               $testCase->fail();
           });
        $t2->shouldReceive('send')
           ->never()
           ->with($message1, \Mockery::any());
        $t2->shouldReceive('send')
           ->once()
           ->with($message4, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $testCase) {
               if ($connectionState2) {
                   return 1;
               }
               $testCase->fail();
           });
        $t2->shouldReceive('send')
           ->never()
           ->with($message3, \Mockery::any());

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();

        $this->assertEquals(1, $transport->send($message1));
        $this->assertEquals(1, $transport->send($message2));
        $this->assertEquals(1, $transport->send($message3));
        $this->assertEquals(1, $transport->send($message4));
    }

    public function testMessageCanBeTriedOnNextTransportIfExceptionThrown()
    {
        $e = new Swift_TransportException('b0rken');

        $message = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $testCase = $this;
        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $e, $testCase) {
               if ($connectionState1) {
                   throw $e;
               }
               $testCase->fail();
           });

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $testCase) {
               if ($connectionState2) {
                   return 1;
               }
               $testCase->fail();
           });

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $this->assertEquals(1, $transport->send($message));
    }

    public function testMessageIsTriedOnNextTransportIfZeroReturned()
    {
        $message = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1) {
               if ($connectionState1) {
                   return 0;
               }

               return 1;
           });

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2) {
               if ($connectionState2) {
                   return 1;
               }

               return 0;
           });

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $this->assertEquals(1, $transport->send($message));
    }

    public function testZeroIsReturnedIfAllTransportsReturnZero()
    {
        $message = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1) {
               if ($connectionState1) {
                   return 0;
               }

               return 1;
           });

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2) {
               if ($connectionState2) {
                   return 0;
               }

               return 1;
           });

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $this->assertEquals(0, $transport->send($message));
    }

    public function testTransportsWhichThrowExceptionsAreNotRetried()
    {
        $e = new Swift_TransportException('maur b0rken');

        $message1 = $this->getMockery('Swift_Mime_Message');
        $message2 = $this->getMockery('Swift_Mime_Message');
        $message3 = $this->getMockery('Swift_Mime_Message');
        $message4 = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $testCase = $this;
        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message1, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $e, $testCase) {
               if ($connectionState1) {
                   throw $e;
               }
               $testCase->fail();
           });
        $t1->shouldReceive('send')
           ->never()
           ->with($message2, \Mockery::any());
        $t1->shouldReceive('send')
           ->never()
           ->with($message3, \Mockery::any());
        $t1->shouldReceive('send')
           ->never()
           ->with($message4, \Mockery::any());

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->times(4)
           ->with(\Mockery::anyOf($message1, $message3, $message3, $message4), \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $testCase) {
               if ($connectionState2) {
                   return 1;
               }
               $testCase->fail();
           });

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $this->assertEquals(1, $transport->send($message1));
        $this->assertEquals(1, $transport->send($message2));
        $this->assertEquals(1, $transport->send($message3));
        $this->assertEquals(1, $transport->send($message4));
    }

    public function testExceptionIsThrownIfAllTransportsDie()
    {
        $e = new Swift_TransportException('b0rken');

        $message = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $e) {
               if ($connectionState1) {
                   throw $e;
               }
           });

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $e) {
               if ($connectionState2) {
                   throw $e;
               }
           });

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        try {
            $transport->send($message);
            $this->fail('All transports failed so Exception should be thrown');
        } catch (Exception $e) {
        }
    }

    public function testStoppingTransportStopsAllDelegates()
    {
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = true;
        $connectionState2 = true;

        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('stop')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if ($connectionState1) {
                   $connectionState1 = false;
               }
           });

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('stop')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if ($connectionState2) {
                   $connectionState2 = false;
               }
           });

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $transport->stop();
    }

    public function testTransportShowsAsNotStartedIfAllDelegatesDead()
    {
        $e = new Swift_TransportException('b0rken');

        $message = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $e) {
               if ($connectionState1) {
                   throw $e;
               }
           });

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $e) {
               if ($connectionState2) {
                   throw $e;
               }
           });

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $this->assertTrue($transport->isStarted());
        try {
            $transport->send($message);
            $this->fail('All transports failed so Exception should be thrown');
        } catch (Exception $e) {
            $this->assertFalse($transport->isStarted());
        }
    }

    public function testRestartingTransportRestartsDeadDelegates()
    {
        $e = new Swift_TransportException('b0rken');

        $message1 = $this->getMockery('Swift_Mime_Message');
        $message2 = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');
        $connectionState1 = false;
        $connectionState2 = false;

        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState1) {
               return $connectionState1;
           });
        $t1->shouldReceive('start')
           ->twice()
           ->andReturnUsing(function () use (&$connectionState1) {
               if (!$connectionState1) {
                   $connectionState1 = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message1, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $e) {
               if ($connectionState1) {
                   $connectionState1 = false;
                   throw $e;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message2, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState1, $e) {
               if ($connectionState1) {
                   return 10;
               }
           });

        $t2->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState2) {
               return $connectionState2;
           });
        $t2->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState2) {
               if (!$connectionState2) {
                   $connectionState2 = true;
               }
           });
        $t2->shouldReceive('send')
           ->once()
           ->with($message1, \Mockery::any())
           ->andReturnUsing(function () use (&$connectionState2, $e) {
               if ($connectionState2) {
                   throw $e;
               }
           });
        $t2->shouldReceive('send')
           ->never()
           ->with($message2, \Mockery::any());

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->start();
        $this->assertTrue($transport->isStarted());
        try {
            $transport->send($message1);
            $this->fail('All transports failed so Exception should be thrown');
        } catch (Exception $e) {
            $this->assertFalse($transport->isStarted());
        }
        //Restart and re-try
        $transport->start();
        $this->assertTrue($transport->isStarted());
        $this->assertEquals(10, $transport->send($message2));
    }

    public function testFailureReferenceIsPassedToDelegates()
    {
        $failures = array();
        $testCase = $this;

        $message = $this->getMockery('Swift_Mime_Message');
        $t1 = $this->getMockery('Swift_Transport');
        $connectionState = false;

        $t1->shouldReceive('isStarted')
           ->zeroOrMoreTimes()
           ->andReturnUsing(function () use (&$connectionState) {
               return $connectionState;
           });
        $t1->shouldReceive('start')
           ->once()
           ->andReturnUsing(function () use (&$connectionState) {
               if (!$connectionState) {
                   $connectionState = true;
               }
           });
        $t1->shouldReceive('send')
           ->once()
           ->with($message, \Mockery::on(function (&$var) use (&$failures, $testCase) {
                return $testCase->varsAreReferences($var, $failures);
           }))
           ->andReturnUsing(function () use (&$connectionState) {
               if ($connectionState) {
                   return 1;
               }
           });

        $transport = $this->_getTransport(array($t1));
        $transport->start();
        $transport->send($message, $failures);
    }

    public function testRegisterPluginDelegatesToLoadedTransports()
    {
        $plugin = $this->_createPlugin();

        $t1 = $this->getMockery('Swift_Transport');
        $t2 = $this->getMockery('Swift_Transport');

        $t1->shouldReceive('registerPlugin')
           ->once()
           ->with($plugin);
        $t2->shouldReceive('registerPlugin')
           ->once()
           ->with($plugin);

        $transport = $this->_getTransport(array($t1, $t2));
        $transport->registerPlugin($plugin);
    }

    /**
     * Adapted from Yay_Matchers_ReferenceMatcher.
     */
    public function varsAreReferences(&$ref1, &$ref2)
    {
        if (is_object($ref2)) {
            return ($ref1 === $ref2);
        }
        if ($ref1 !== $ref2) {
            return false;
        }

        $copy = $ref2;
        $randomString = uniqid('yay');
        $ref2 = $randomString;
        $isRef = ($ref1 === $ref2);
        $ref2 = $copy;

        return $isRef;
    }

    // -- Private helpers

    private function _getTransport(array $transports)
    {
        $transport = new Swift_Transport_LoadBalancedTransport();
        $transport->setTransports($transports);

        return $transport;
    }

    private function _createPlugin()
    {
        return $this->getMockery('Swift_Events_EventListener');
    }
}
