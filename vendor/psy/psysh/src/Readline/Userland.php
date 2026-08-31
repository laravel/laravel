<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline;

use Psy\Exception\BreakException;
use Psy\Readline\Hoa\Console as HoaConsole;
use Psy\Readline\Hoa\ConsoleCursor as HoaConsoleCursor;
use Psy\Readline\Hoa\ConsoleInput as HoaConsoleInput;
use Psy\Readline\Hoa\ConsoleOutput as HoaConsoleOutput;
use Psy\Readline\Hoa\ConsoleTput as HoaConsoleTput;
use Psy\Readline\Hoa\Readline as HoaReadline;
use Psy\Readline\Hoa\Ustring as HoaUstring;

/**
 * Userland Readline implementation.
 */
class Userland implements Readline
{
    private HoaReadline $hoaReadline;
    private ?string $lastPrompt = null;
    private HoaConsoleTput $tput;
    private HoaConsoleInput $input;
    private HoaConsoleOutput $output;

    public static function isSupported(): bool
    {
        static::bootstrapHoa();

        return HoaUstring::checkMbString() && HoaConsoleTput::isSupported();
    }

    /**
     * {@inheritdoc}
     */
    public static function supportsBracketedPaste(): bool
    {
        return false;
    }

    /**
     * Doesn't (currently) support history file, size or erase dupes configs.
     */
    public function __construct($historyFile = null, $historySize = 0, $eraseDups = false)
    {
        static::bootstrapHoa(true);

        $this->hoaReadline = new HoaReadline();
        $this->hoaReadline->addMapping('\C-l', function () {
            $this->redisplay();

            return HoaReadline::STATE_NO_ECHO;
        });

        $this->tput = new HoaConsoleTput();
        HoaConsole::setTput($this->tput);

        $this->input = new HoaConsoleInput();
        HoaConsole::setInput($this->input);

        $this->output = new HoaConsoleOutput();
        HoaConsole::setOutput($this->output);
    }

    /**
     * Bootstrap some things that Hoa used to do itself.
     */
    public static function bootstrapHoa(bool $withTerminalResize = false)
    {
        // A side effect registers hoa:// stream wrapper
        \class_exists('Psy\Readline\Hoa\ProtocolWrapper');

        // A side effect registers hoa://Library/Stream
        \class_exists('Psy\Readline\Hoa\Stream');

        // A side effect binds terminal resize
        $withTerminalResize && \class_exists('Psy\Readline\Hoa\ConsoleWindow');
    }

    /**
     * {@inheritdoc}
     */
    public function addHistory(string $line): bool
    {
        $this->hoaReadline->addHistory($line);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clearHistory(): bool
    {
        $this->hoaReadline->clearHistory();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function listHistory(): array
    {
        $i = 0;
        $list = [];
        while (($item = $this->hoaReadline->getHistory($i++)) !== null) {
            $list[] = $item;
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function readHistory(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BreakException if user hits Ctrl+D
     *
     * @return string
     */
    public function readline(?string $prompt = null)
    {
        $this->lastPrompt = $prompt;

        return $this->hoaReadline->readLine($prompt);
    }

    /**
     * {@inheritdoc}
     */
    public function redisplay()
    {
        $currentLine = $this->hoaReadline->getLine();
        HoaConsoleCursor::clear('all');
        echo $this->lastPrompt, $currentLine;
    }

    /**
     * {@inheritdoc}
     */
    public function writeHistory(): bool
    {
        return true;
    }
}
