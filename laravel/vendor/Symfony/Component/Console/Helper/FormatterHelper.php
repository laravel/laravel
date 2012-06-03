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

/**
 * The Formatter class provides helpers to format messages.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FormatterHelper extends Helper
{
    /**
     * Formats a message within a section.
     *
     * @param string  $section The section name
     * @param string  $message The message
     * @param string  $style   The style to apply to the section
     */
    public function formatSection($section, $message, $style = 'info')
    {
        return sprintf('<%s>[%s]</%s> %s', $style, $section, $style, $message);
    }

    /**
     * Formats a message as a block of text.
     *
     * @param string|array $messages The message to write in the block
     * @param string       $style    The style to apply to the whole block
     * @param Boolean      $large    Whether to return a large block
     *
     * @return string The formatter message
     */
    public function formatBlock($messages, $style, $large = false)
    {
        $messages = (array) $messages;

        $len = 0;
        $lines = array();
        foreach ($messages as $message) {
            $lines[] = sprintf($large ? '  %s  ' : ' %s ', $message);
            $len = max($this->strlen($message) + ($large ? 4 : 2), $len);
        }

        $messages = $large ? array(str_repeat(' ', $len)) : array();
        foreach ($lines as $line) {
            $messages[] = $line.str_repeat(' ', $len - $this->strlen($line));
        }
        if ($large) {
            $messages[] = str_repeat(' ', $len);
        }

        foreach ($messages as &$message) {
            $message = sprintf('<%s>%s</%s>', $style, $message, $style);
        }

        return implode("\n", $messages);
    }

    /**
     * Returns the length of a string, using mb_strlen if it is available.
     *
     * @param string $string The string to check its length
     *
     * @return integer The length of the string
     */
    private function strlen($string)
    {
        if (!function_exists('mb_strlen')) {
            return strlen($string);
        }

        if (false === $encoding = mb_detect_encoding($string)) {
            return strlen($string);
        }

        return mb_strlen($string, $encoding);
    }

    /**
     * Returns the helper's canonical name.
     *
     * @return string The canonical name of the helper
     */
    public function getName()
    {
        return 'formatter';
    }
}
