<?php

namespace Jeremeamia\SuperClosure\Test;

use Jeremeamia\SuperClosure\SerializableClosure;

/**
 * @covers \Jeremeamia\SuperClosure\SerializableClosure
 */
class SerializableClosureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializableClosure
     */
    public $serializableClosure;

    /**
     * @var \Closure
     */
    public $originalClosure;

    public function setup()
    {
        $base = 2;
        $exp = function ($power) use ($base) {
            return (int) pow($base, $power);
        };

        $this->originalClosure = $exp;
        $this->serializableClosure = new SerializableClosure($exp);
    }

    public function testClosureProxiesToTheOriginalClosureWhenInvoked()
    {
        $this->assertInstanceOf('\Closure', $this->serializableClosure->getClosure());
        $this->assertSame($this->originalClosure, $this->serializableClosure->getClosure());
        $this->assertEquals(
            call_user_func($this->originalClosure, 4),
            call_user_func($this->serializableClosure, 4)
        );
    }

    public function testClosureBehavesTheSameAfterSerializationProcess()
    {
        $originalReturnValue = call_user_func($this->serializableClosure, 4);
        $serializedClosure = serialize($this->serializableClosure);
        $unserializedClosure = unserialize($serializedClosure);
        $finalReturnValue = call_user_func($unserializedClosure, 4);

        $this->assertEquals($originalReturnValue, $finalReturnValue);
    }

    public function testCanSerializeRecursiveClosure()
    {
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            $this->markTestSkipped('Requires version 5.4+ of PHP');
        }

        $factorial = new SerializableClosure(function ($n) use (&$factorial) {
            return ($n <= 1) ? 1 : $n * $factorial($n - 1);
        });

        $this->assertEquals(120, call_user_func($factorial, 5));
    }

    public function testCanSerializeMultipleTimes()
    {
        $result = call_user_func($this->serializableClosure, 5);
        $this->assertEquals(32, $result);

        $serializedOnce = unserialize(serialize($this->serializableClosure));
        $this->assertEquals(32, call_user_func($serializedOnce, 5));
        $internalState = $this->readAttribute($serializedOnce, 'state');
        $this->assertCount(2, $internalState);

        $serializedAgain = unserialize(serialize($this->serializableClosure));
        $this->assertEquals(32, call_user_func($serializedAgain, 5));
        $this->assertEquals($internalState, $this->readAttribute($serializedAgain, 'state'));

        $serializedTwice = unserialize(serialize($serializedOnce));
        $this->assertEquals(32, call_user_func($serializedTwice, 5));
        $this->assertEquals($internalState, $this->readAttribute($serializedTwice, 'state'));
    }

    /**
     * CAVEAT #1: Serializing a closure will sever relationships with things passed by reference
     */
    public function testDoesNotMaintainsReferencesEvenWhenVariablesAreStillInScope()
    {
        $num = 0;
        $inc = new SerializableClosure(function () use (&$num) {
            $num++;
        });

        $inc();
        $inc();
        $this->assertEquals(2, $num, '$num should be incremented twice because by reference');

        $newInc = unserialize(serialize($inc));
        /** @var $newInc \Closure */
        $newInc();
        $this->assertEquals(2, $num, '$num should not be incremented again because the reference is lost');
    }

    /**
     * CAVEAT #2: You can't serialize a closure if there are two closures declared on one line
     */
    public function testCannotDetermineWhichClosureToUseIfTwoDeclaredOnTheSameLine()
    {
        $this->setExpectedException('Exception');

        $add = function ($a, $b) {return $a + $b;}; $sub = function ($a, $b) {return $a - $b;};
        $serialized = serialize(new SerializableClosure($sub));
    }
}
