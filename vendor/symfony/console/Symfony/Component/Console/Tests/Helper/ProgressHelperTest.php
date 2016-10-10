<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Helper;

use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\StreamOutput;

class ProgressHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testAdvance()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream());
        $progress->advance();

        rewind($output->getStream());
        $this->assertEquals($this->generateOutput('    1 [->--------------------------]'), stream_get_contents($output->getStream()));
    }

    public function testAdvanceWithStep()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream());
        $progress->advance(5);

        rewind($output->getStream());
        $this->assertEquals($this->generateOutput('    5 [----->----------------------]'), stream_get_contents($output->getStream()));
    }

    public function testAdvanceMultipleTimes()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream());
        $progress->advance(3);
        $progress->advance(2);

        rewind($output->getStream());
        $this->assertEquals($this->generateOutput('    3 [--->------------------------]').$this->generateOutput('    5 [----->----------------------]'), stream_get_contents($output->getStream()));
    }

    public function testCustomizations()
    {
        $progress = new ProgressHelper();
        $progress->setBarWidth(10);
        $progress->setBarCharacter('_');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('/');
        $progress->setFormat(' %current%/%max% [%bar%] %percent%%');
        $progress->start($output = $this->getOutputStream(), 10);
        $progress->advance();

        rewind($output->getStream());
        $this->assertEquals($this->generateOutput('  1/10 [_/        ]  10%'), stream_get_contents($output->getStream()));
    }

    public function testPercent()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream(), 50);
        $progress->display();
        $progress->advance();
        $progress->advance();

        rewind($output->getStream());
        $this->assertEquals($this->generateOutput('  0/50 [>---------------------------]   0%').$this->generateOutput('  1/50 [>---------------------------]   2%').$this->generateOutput('  2/50 [=>--------------------------]   4%'), stream_get_contents($output->getStream()));
    }

    public function testOverwriteWithShorterLine()
    {
        $progress = new ProgressHelper();
        $progress->setFormat(' %current%/%max% [%bar%] %percent%%');
        $progress->start($output = $this->getOutputStream(), 50);
        $progress->display();
        $progress->advance();

        // set shorter format
        $progress->setFormat(' %current%/%max% [%bar%]');
        $progress->advance();

        rewind($output->getStream());
        $this->assertEquals(
            $this->generateOutput('  0/50 [>---------------------------]   0%').
            $this->generateOutput('  1/50 [>---------------------------]   2%').
            $this->generateOutput('  2/50 [=>--------------------------]     '),
            stream_get_contents($output->getStream())
        );
    }

    public function testSetCurrentProgress()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream(), 50);
        $progress->display();
        $progress->advance();
        $progress->setCurrent(15);
        $progress->setCurrent(25);

        rewind($output->getStream());
        $this->assertEquals(
            $this->generateOutput('  0/50 [>---------------------------]   0%').
            $this->generateOutput('  1/50 [>---------------------------]   2%').
            $this->generateOutput(' 15/50 [========>-------------------]  30%').
            $this->generateOutput(' 25/50 [==============>-------------]  50%'),
            stream_get_contents($output->getStream())
        );
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage You must start the progress bar
     */
    public function testSetCurrentBeforeStarting()
    {
        $progress = new ProgressHelper();
        $progress->setCurrent(15);
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage You can't regress the progress bar
     */
    public function testRegressProgress()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream(), 50);
        $progress->setCurrent(15);
        $progress->setCurrent(10);
    }

    public function testRedrawFrequency()
    {
        $progress = $this->getMock('Symfony\Component\Console\Helper\ProgressHelper', array('display'));
        $progress->expects($this->exactly(4))
                 ->method('display');

        $progress->setRedrawFrequency(2);

        $progress->start($output = $this->getOutputStream(), 6);
        $progress->setCurrent(1);
        $progress->advance(2);
        $progress->advance(2);
        $progress->advance(1);
    }

    public function testMultiByteSupport()
    {
        if (!function_exists('mb_strlen') || (false === $encoding = mb_detect_encoding('■'))) {
            $this->markTestSkipped('The mbstring extension is needed for multi-byte support');
        }

        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream());
        $progress->setBarCharacter('■');
        $progress->advance(3);

        rewind($output->getStream());
        $this->assertEquals($this->generateOutput('    3 [■■■>------------------------]'), stream_get_contents($output->getStream()));
    }

    public function testClear()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream(), 50);
        $progress->setCurrent(25);
        $progress->clear();

        rewind($output->getStream());
        $this->assertEquals(
            $this->generateOutput(' 25/50 [==============>-------------]  50%').$this->generateOutput(''),
            stream_get_contents($output->getStream())
        );
    }

    public function testPercentNotHundredBeforeComplete()
    {
        $progress = new ProgressHelper();
        $progress->start($output = $this->getOutputStream(), 200);
        $progress->display();
        $progress->advance(199);
        $progress->advance();

        rewind($output->getStream());
        $this->assertEquals($this->generateOutput('   0/200 [>---------------------------]   0%').$this->generateOutput(' 199/200 [===========================>]  99%').$this->generateOutput(' 200/200 [============================] 100%'), stream_get_contents($output->getStream()));
    }

    protected function getOutputStream()
    {
        return new StreamOutput(fopen('php://memory', 'r+', false));
    }

    protected $lastMessagesLength;

    protected function generateOutput($expected)
    {
        $expectedout = $expected;

        if ($this->lastMessagesLength !== null) {
            $expectedout = str_pad($expected, $this->lastMessagesLength, "\x20", STR_PAD_RIGHT);
        }

        $this->lastMessagesLength = strlen($expectedout);

        return "\x0D".$expectedout;
    }
}
