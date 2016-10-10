<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\Frame;
use UnexpectedValueException;
use IteratorAggregate;
use ArrayIterator;
use Serializable;
use Countable;

/**
 * Exposes a fluent interface for dealing with an ordered list
 * of stack-trace frames.
 */
class FrameCollection implements IteratorAggregate, Serializable, Countable
{
    /**
     * @var array[]
     */
    private $frames;

    /**
     * @param array $frames
     */
    public function __construct(array $frames)
    {
        $this->frames = array_map(function($frame) {
            return new Frame($frame);
        }, $frames);
    }

    /**
     * Filters frames using a callable, returns the same FrameCollection
     * 
     * @param  callable $callable
     * @return FrameCollection
     */
    public function filter($callable)
    {
        $this->frames = array_filter($this->frames, $callable);
        return $this;        
    }

    /**
     * Map the collection of frames
     * 
     * @param  callable $callable
     * @return FrameCollection
     */
    public function map($callable)
    {
        // Contain the map within a higher-order callable
        // that enforces type-correctness for the $callable
        $this->frames = array_map(function($frame) use($callable) {
            $frame = call_user_func($callable, $frame);

            if(!$frame instanceof Frame) {
                throw new UnexpectedValueException(
                    "Callable to " . __METHOD__ . " must return a Frame object"
                );
            }

            return $frame;
        }, $this->frames);

        return $this;
    }

    /**
     * Returns an array with all frames, does not affect
     * the internal array.
     * 
     * @todo   If this gets any more complex than this,
     *         have getIterator use this method.
     * @see    FrameCollection::getIterator
     * @return array
     */
    public function getArray()
    {
        return $this->frames;
    }

    /**
     * @see IteratorAggregate::getIterator
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->frames);
    }

    /**
     * @see Countable::count
     * @return int
     */
    public function count()
    {
        return count($this->frames);
    }

    /**
     * @see Serializable::serialize
     * @return string
     */
    public function serialize()
    {
        return serialize($this->frames);
    }

    /**
     * @see Serializable::unserialize
     * @param string $serializedFrames
     */
    public function unserialize($serializedFrames)
    {
        $this->frames = unserialize($serializedFrames);
    }
}
