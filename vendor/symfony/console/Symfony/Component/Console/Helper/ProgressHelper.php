<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Progress class provides helpers to display progress output.
 *
 * @author Chris Jones <leeked@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ProgressHelper extends Helper
{
    const FORMAT_QUIET         = ' %percent%%';
    const FORMAT_NORMAL        = ' %current%/%max% [%bar%] %percent%%';
    const FORMAT_VERBOSE       = ' %current%/%max% [%bar%] %percent%% Elapsed: %elapsed%';
    const FORMAT_QUIET_NOMAX   = ' %current%';
    const FORMAT_NORMAL_NOMAX  = ' %current% [%bar%]';
    const FORMAT_VERBOSE_NOMAX = ' %current% [%bar%] Elapsed: %elapsed%';

    // options
    private $barWidth     = 28;
    private $barChar      = '=';
    private $emptyBarChar = '-';
    private $progressChar = '>';
    private $format       = null;
    private $redrawFreq   = 1;

    private $lastMessagesLength;
    private $barCharOriginal;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Current step
     *
     * @var int
     */
    private $current;

    /**
     * Maximum number of steps
     *
     * @var int
     */
    private $max;

    /**
     * Start time of the progress bar
     *
     * @var int
     */
    private $startTime;

    /**
     * List of formatting variables
     *
     * @var array
     */
    private $defaultFormatVars = array(
        'current',
        'max',
        'bar',
        'percent',
        'elapsed',
    );

    /**
     * Available formatting variables
     *
     * @var array
     */
    private $formatVars;

    /**
     * Stored format part widths (used for padding)
     *
     * @var array
     */
    private $widths = array(
        'current' => 4,
        'max'     => 4,
        'percent' => 3,
        'elapsed' => 6,
    );

    /**
     * Various time formats
     *
     * @var array
     */
    private $timeFormats = array(
        array(0, '???'),
        array(2, '1 sec'),
        array(59, 'secs', 1),
        array(60, '1 min'),
        array(3600, 'mins', 60),
        array(5400, '1 hr'),
        array(86400, 'hrs', 3600),
        array(129600, '1 day'),
        array(604800, 'days', 86400),
    );

    /**
     * Sets the progress bar width.
     *
     * @param int $size The progress bar size
     */
    public function setBarWidth($size)
    {
        $this->barWidth = (int) $size;
    }

    /**
     * Sets the bar character.
     *
     * @param string $char A character
     */
    public function setBarCharacter($char)
    {
        $this->barChar = $char;
    }

    /**
     * Sets the empty bar character.
     *
     * @param string $char A character
     */
    public function setEmptyBarCharacter($char)
    {
        $this->emptyBarChar = $char;
    }

    /**
     * Sets the progress bar character.
     *
     * @param string $char A character
     */
    public function setProgressCharacter($char)
    {
        $this->progressChar = $char;
    }

    /**
     * Sets the progress bar format.
     *
     * @param string $format The format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * Sets the redraw frequency.
     *
     * @param int $freq The frequency in steps
     */
    public function setRedrawFrequency($freq)
    {
        $this->redrawFreq = (int) $freq;
    }

    /**
     * Starts the progress output.
     *
     * @param OutputInterface $output An Output instance
     * @param int|null        $max    Maximum steps
     */
    public function start(OutputInterface $output, $max = null)
    {
        $this->startTime = time();
        $this->current   = 0;
        $this->max       = (int) $max;
        $this->output    = $output;
        $this->lastMessagesLength = 0;
        $this->barCharOriginal = '';

        if (null === $this->format) {
            switch ($output->getVerbosity()) {
                case OutputInterface::VERBOSITY_QUIET:
                    $this->format = self::FORMAT_QUIET_NOMAX;
                    if ($this->max > 0) {
                        $this->format = self::FORMAT_QUIET;
                    }
                    break;
                case OutputInterface::VERBOSITY_VERBOSE:
                case OutputInterface::VERBOSITY_VERY_VERBOSE:
                case OutputInterface::VERBOSITY_DEBUG:
                    $this->format = self::FORMAT_VERBOSE_NOMAX;
                    if ($this->max > 0) {
                        $this->format = self::FORMAT_VERBOSE;
                    }
                    break;
                default:
                    $this->format = self::FORMAT_NORMAL_NOMAX;
                    if ($this->max > 0) {
                        $this->format = self::FORMAT_NORMAL;
                    }
                    break;
            }
        }

        $this->initialize();
    }

    /**
     * Advances the progress output X steps.
     *
     * @param int     $step   Number of steps to advance
     * @param bool    $redraw Whether to redraw or not
     *
     * @throws \LogicException
     */
    public function advance($step = 1, $redraw = false)
    {
        $this->setCurrent($this->current + $step, $redraw);
    }

    /**
     * Sets the current progress.
     *
     * @param int     $current The current progress
     * @param bool    $redraw  Whether to redraw or not
     *
     * @throws \LogicException
     */
    public function setCurrent($current, $redraw = false)
    {
        if (null === $this->startTime) {
            throw new \LogicException('You must start the progress bar before calling setCurrent().');
        }

        $current = (int) $current;

        if ($current < $this->current) {
            throw new \LogicException('You can\'t regress the progress bar');
        }

        if (0 === $this->current) {
            $redraw = true;
        }

        $prevPeriod = intval($this->current / $this->redrawFreq);

        $this->current = $current;

        $currPeriod = intval($this->current / $this->redrawFreq);
        if ($redraw || $prevPeriod !== $currPeriod || $this->max === $this->current) {
            $this->display();
        }
    }

    /**
     * Outputs the current progress string.
     *
     * @param bool    $finish Forces the end result
     *
     * @throws \LogicException
     */
    public function display($finish = false)
    {
        if (null === $this->startTime) {
            throw new \LogicException('You must start the progress bar before calling display().');
        }

        $message = $this->format;
        foreach ($this->generate($finish) as $name => $value) {
            $message = str_replace("%{$name}%", $value, $message);
        }
        $this->overwrite($this->output, $message);
    }

    /**
     * Removes the progress bar from the current line.
     *
     * This is useful if you wish to write some output
     * while a progress bar is running.
     * Call display() to show the progress bar again.
     */
    public function clear()
    {
        $this->overwrite($this->output, '');
    }

    /**
     * Finishes the progress output.
     */
    public function finish()
    {
        if (null === $this->startTime) {
            throw new \LogicException('You must start the progress bar before calling finish().');
        }

        if (null !== $this->startTime) {
            if (!$this->max) {
                $this->barChar = $this->barCharOriginal;
                $this->display(true);
            }
            $this->startTime = null;
            $this->output->writeln('');
            $this->output = null;
        }
    }

    /**
     * Initializes the progress helper.
     */
    private function initialize()
    {
        $this->formatVars = array();
        foreach ($this->defaultFormatVars as $var) {
            if (false !== strpos($this->format, "%{$var}%")) {
                $this->formatVars[$var] = true;
            }
        }

        if ($this->max > 0) {
            $this->widths['max']     = $this->strlen($this->max);
            $this->widths['current'] = $this->widths['max'];
        } else {
            $this->barCharOriginal = $this->barChar;
            $this->barChar         = $this->emptyBarChar;
        }
    }

    /**
     * Generates the array map of format variables to values.
     *
     * @param bool    $finish Forces the end result
     *
     * @return array Array of format vars and values
     */
    private function generate($finish = false)
    {
        $vars    = array();
        $percent = 0;
        if ($this->max > 0) {
            $percent = (float) $this->current / $this->max;
        }

        if (isset($this->formatVars['bar'])) {
            $completeBars = 0;

            if ($this->max > 0) {
                $completeBars = floor($percent * $this->barWidth);
            } else {
                if (!$finish) {
                    $completeBars = floor($this->current % $this->barWidth);
                } else {
                    $completeBars = $this->barWidth;
                }
            }

            $emptyBars = $this->barWidth - $completeBars - $this->strlen($this->progressChar);
            $bar = str_repeat($this->barChar, $completeBars);
            if ($completeBars < $this->barWidth) {
                $bar .= $this->progressChar;
                $bar .= str_repeat($this->emptyBarChar, $emptyBars);
            }

            $vars['bar'] = $bar;
        }

        if (isset($this->formatVars['elapsed'])) {
            $elapsed = time() - $this->startTime;
            $vars['elapsed'] = str_pad($this->humaneTime($elapsed), $this->widths['elapsed'], ' ', STR_PAD_LEFT);
        }

        if (isset($this->formatVars['current'])) {
            $vars['current'] = str_pad($this->current, $this->widths['current'], ' ', STR_PAD_LEFT);
        }

        if (isset($this->formatVars['max'])) {
            $vars['max'] = $this->max;
        }

        if (isset($this->formatVars['percent'])) {
            $vars['percent'] = str_pad(floor($percent * 100), $this->widths['percent'], ' ', STR_PAD_LEFT);
        }

        return $vars;
    }

    /**
     * Converts seconds into human-readable format.
     *
     * @param int     $secs Number of seconds
     *
     * @return string Time in readable format
     */
    private function humaneTime($secs)
    {
        $text = '';
        foreach ($this->timeFormats as $format) {
            if ($secs < $format[0]) {
                if (count($format) == 2) {
                    $text = $format[1];
                    break;
                } else {
                    $text = ceil($secs / $format[2]).' '.$format[1];
                    break;
                }
            }
        }

        return $text;
    }

    /**
     * Overwrites a previous message to the output.
     *
     * @param OutputInterface $output   An Output instance
     * @param string          $message  The message
     */
    private function overwrite(OutputInterface $output, $message)
    {
        $length = $this->strlen($message);

        // append whitespace to match the last line's length
        if (null !== $this->lastMessagesLength && $this->lastMessagesLength > $length) {
            $message = str_pad($message, $this->lastMessagesLength, "\x20", STR_PAD_RIGHT);
        }

        // carriage return
        $output->write("\x0D");
        $output->write($message);

        $this->lastMessagesLength = $this->strlen($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'progress';
    }
}
