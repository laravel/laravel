<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use ReturnTypeWillChange;
use Serializable;
use UnexpectedValueException;

/**
 * Exposes a fluent interface for dealing with an ordered list
 * of stack-trace frames.
 */
class FrameCollection implements ArrayAccess, IteratorAggregate, Serializable, Countable
{
    /**
     * @var array[]
     */
    private $frames;

    public function __construct(array $frames)
    {
        $this->frames = array_map(function ($frame) {
            return new Frame($frame);
        }, $frames);
    }

    /**
     * Filters frames using a callable, returns the same FrameCollection
     *
     * @param  callable        $callable
     * @return FrameCollection
     */
    public function filter($callable)
    {
        $this->frames = array_values(array_filter($this->frames, $callable));
        return $this;
    }

    /**
     * Map the collection of frames
     *
     * @param  callable        $callable
     * @return FrameCollection
     */
    public function map($callable)
    {
        // Contain the map within a higher-order callable
        // that enforces type-correctness for the $callable
        $this->frames = array_map(function ($frame) use ($callable) {
            $frame = call_user_func($callable, $frame);

            if (!$frame instanceof Frame) {
                throw new UnexpectedValueException(
                    "Callable to " . __CLASS__ . "::map must return a Frame object"
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
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->frames);
    }

    /**
     * @see ArrayAccess::offsetExists
     * @param int $offset
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->frames[$offset]);
    }

    /**
     * @see ArrayAccess::offsetGet
     * @param int $offset
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->frames[$offset];
    }

    /**
     * @see ArrayAccess::offsetSet
     * @param int $offset
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new \Exception(__CLASS__ . ' is read only');
    }

    /**
     * @see ArrayAccess::offsetUnset
     * @param int $offset
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new \Exception(__CLASS__ . ' is read only');
    }

    /**
     * @see Countable::count
     * @return int
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->frames);
    }

    /**
     * Count the frames that belongs to the application.
     *
     * @return int
     */
    public function countIsApplication()
    {
        return count(array_filter($this->frames, function (Frame $f) {
            return $f->isApplication();
        }));
    }

    /**
     * @see Serializable::serialize
     * @return string
     */
    #[ReturnTypeWillChange]
    public function serialize()
    {
        return serialize($this->frames);
    }

    /**
     * @see Serializable::unserialize
     * @param string $serializedFrames
     */
    #[ReturnTypeWillChange]
    public function unserialize($serializedFrames)
    {
        $this->frames = unserialize($serializedFrames);
    }

    public function __serialize()
    {
        return $this->frames;
    }

    public function __unserialize(array $serializedFrames)
    {
        $this->frames = $serializedFrames;
    }

    /**
     * @param Frame[] $frames Array of Frame instances, usually from $e->getPrevious()
     */
    public function prependFrames(array $frames)
    {
        $this->frames = array_merge($frames, $this->frames);
    }

    /**
     * Gets the innermost part of stack trace that is not the same as that of outer exception
     *
     * @param  FrameCollection $parentFrames Outer exception frames to compare tail against
     * @return Frame[]
     */
    public function topDiff(FrameCollection $parentFrames)
    {
        $diff = $this->frames;

        $parentFrames = $parentFrames->getArray();
        $p = count($parentFrames)-1;

        for ($i = count($diff)-1; $i >= 0 && $p >= 0; $i--) {
            /** @var Frame $tailFrame */
            $tailFrame = $diff[$i];
            if ($tailFrame->equals($parentFrames[$p])) {
                unset($diff[$i]);
            }
            $p--;
        }
        return $diff;
    }
}
