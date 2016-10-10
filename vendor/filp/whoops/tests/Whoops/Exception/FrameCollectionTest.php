<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\FrameCollection;
use Whoops\TestCase;
use Mockery as m;

class FrameCollectionTest extends TestCase
{
    /**
     * Stupid little counter for tagging frames
     * with a unique but predictable id
     * @var int
     */
    private $frameIdCounter = 0;

    /**
     * @return array
     */
    public function getFrameData()
    {
        $id = ++$this->frameIdCounter;
        return array(
            'file'     => __DIR__ . '/../../fixtures/frame.lines-test.php',
            'line'     => $id,
            'function' => 'test-' . $id,
            'class'    => 'MyClass',
            'args'     => array(true, 'hello')
        );
    }

    /**
     * @param  int $total
     * @return array
     */
    public function getFrameDataList($total)
    {
        $total = max((int) $total, 1);
        $self  = $this;
        $frames = array_map(function() use($self) {
            return $self->getFrameData();
        }, range(1, $total));

        return $frames;
    }

    /**
     * @param  array $frames
     * @return Whoops\Exception\FrameCollection
     */
    private function getFrameCollectionInstance($frames = null)
    {
        if($frames === null) {
            $frames = $this->getFrameDataList(10);
        }

        return new FrameCollection($frames);
    }

    /**
     * @covers Whoops\Exception\FrameCollection::filter
     * @covers Whoops\Exception\FrameCollection::count
     */
    public function testFilterFrames()
    {
        $frames = $this->getFrameCollectionInstance();

        // Filter out all frames with a line number under 6
        $frames->filter(function($frame) {
            return $frame->getLine() <= 5;
        });

        $this->assertCount(5, $frames);
    }

    /**
     * @covers Whoops\Exception\FrameCollection::map
     */
    public function testMapFrames()
    {
        $frames = $this->getFrameCollectionInstance();

        // Filter out all frames with a line number under 6
        $frames->map(function($frame) {
            $frame->addComment("This is cool", "test");
            return $frame;
        });

        $this->assertCount(10, $frames);
    }


    /**
     * @covers Whoops\Exception\FrameCollection::map
     * @expectedException UnexpectedValueException
     */
    public function testMapFramesEnforceType()
    {
        $frames = $this->getFrameCollectionInstance();

        // Filter out all frames with a line number under 6
        $frames->map(function($frame) {
            return "bajango";
        });
    }

    /**
     * @covers Whoops\Exception\FrameCollection::getArray
     */
    public function testGetArray()
    {
        $frames = $this->getFrameCollectionInstance();
        $frames = $frames->getArray();

        $this->assertCount(10, $frames);
        foreach($frames as $frame) {
            $this->assertInstanceOf('Whoops\\Exception\\Frame', $frame);
        }
    }

    /**
     * @covers Whoops\Exception\FrameCollection::getIterator
     */
    public function testCollectionIsIterable()
    {
        $frames = $this->getFrameCollectionInstance();
        foreach($frames as $frame) {
            $this->assertInstanceOf('Whoops\\Exception\\Frame', $frame);
        }
    }

    /**
     * @covers Whoops\Exception\FrameCollection::serialize
     * @covers Whoops\Exception\FrameCollection::unserialize
     */
    public function testCollectionIsSerializable()
    {
        $frames           = $this->getFrameCollectionInstance();
        $serializedFrames = serialize($frames);
        $newFrames        = unserialize($serializedFrames);

        foreach($newFrames as $frame) {
            $this->assertInstanceOf('Whoops\\Exception\\Frame', $frame);
        }
    }
}
