<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\Frame;
use Whoops\TestCase;
use Mockery as m;

class FrameTest extends TestCase
{
    /**
     * @return array
     */
    private function getFrameData()
    {
        return array(
            'file'     => __DIR__ . '/../../fixtures/frame.lines-test.php',
            'line'     => 0,
            'function' => 'test',
            'class'    => 'MyClass',
            'args'     => array(true, 'hello')
        );
    }

    /**
     * @param  array $data
     * @return Frame
     */
    private function getFrameInstance($data = null)
    {
        if($data === null) {
            $data = $this->getFrameData();
        }

        return new Frame($data);
    }

    /**
     * @covers Whoops\Exception\Frame::getFile
     */
    public function testGetFile()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getFile(), $data['file']);
    }

    /**
     * @covers Whoops\Exception\Frame::getLine
     */
    public function testGetLine()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getLine(), $data['line']);
    }

    /**
     * @covers Whoops\Exception\Frame::getClass
     */
    public function testGetClass()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getClass(), $data['class']);
    }

    /**
     * @covers Whoops\Exception\Frame::getFunction
     */
    public function testGetFunction()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getFunction(), $data['function']);
    }

    /**
     * @covers Whoops\Exception\Frame::getArgs
     */
    public function testGetArgs()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getArgs(), $data['args']);
    }

    /**
     * @covers Whoops\Exception\Frame::getFileContents
     */
    public function testGetFileContents()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getFileContents(), file_get_contents($data['file']));
    }

    /**
     * @covers Whoops\Exception\Frame::getFileLines
     */
    public function testGetFileLines()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $lines = explode("\n", $frame->getFileContents());
        $this->assertEquals($frame->getFileLines(), $lines);
    }

    /**
     * @covers Whoops\Exception\Frame::getFileLines
     */
    public function testGetFileLinesRange()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $lines = $frame->getFileLines(0, 3);

        $this->assertEquals($lines[0], '<?php');
        $this->assertEquals($lines[1], '// Line 2');
        $this->assertEquals($lines[2], '// Line 3');
    }

    /**
     * @covers Whoops\Exception\Frame::addComment
     * @covers Whoops\Exception\Frame::getComments
     */
    public function testGetComments()
    {
        $frame    = $this->getFrameInstance();
        $testComments = array(
            'Dang, yo!',
            'Errthangs broken!',
            'Dayumm!'
        );

        $frame->addComment($testComments[0]);
        $frame->addComment($testComments[1]);
        $frame->addComment($testComments[2]);

        $comments = $frame->getComments();

        $this->assertCount(3, $comments);

        $this->assertEquals($comments[0]['comment'], $testComments[0]);
        $this->assertEquals($comments[1]['comment'], $testComments[1]);
        $this->assertEquals($comments[2]['comment'], $testComments[2]);
    }

    /**
     * @covers Whoops\Exception\Frame::addComment
     * @covers Whoops\Exception\Frame::getComments
     */
    public function testGetFilteredComments()
    {
        $frame    = $this->getFrameInstance();
        $testComments = array(
            array('Dang, yo!', 'test'),
            array('Errthangs broken!', 'test'),
            'Dayumm!'
        );

        $frame->addComment($testComments[0][0], $testComments[0][1]);
        $frame->addComment($testComments[1][0], $testComments[1][1]);
        $frame->addComment($testComments[2][0], $testComments[2][1]);

        $comments = $frame->getComments('test');

        $this->assertCount(2, $comments);
        $this->assertEquals($comments[0]['comment'], $testComments[0][0]);
        $this->assertEquals($comments[1]['comment'], $testComments[1][0]);
    }

    /**
     * @covers Whoops\Exception\Frame::serialize
     * @covers Whoops\Exception\Frame::unserialize
     */
    public function testFrameIsSerializable()
    {
        $data            = $this->getFrameData();
        $frame           = $this->getFrameInstance();
        $commentText     = "Gee I hope this works";
        $commentContext  = "test";

        $frame->addComment($commentText, $commentContext);

        $serializedFrame = serialize($frame);
        $newFrame        = unserialize($serializedFrame);

        $this->assertInstanceOf('Whoops\\Exception\\Frame', $newFrame);
        $this->assertEquals($newFrame->getFile(), $data['file']);
        $this->assertEquals($newFrame->getLine(), $data['line']);

        $comments = $newFrame->getComments();
        $this->assertCount(1, $comments);
        $this->assertEquals($comments[0]["comment"], $commentText);
        $this->assertEquals($comments[0]["context"], $commentContext);
    }
}
