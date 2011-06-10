<?php namespace System;

class Text {

    /**
     * Limit the words in a string. Word integrity will be preserved.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public static function words($value, $limit, $end = '&#8230;')
    {
        if (trim($value) == '')
        {
            return $value;
        }

        // -----------------------------------------------------
        // Limit the words in the string.
        // -----------------------------------------------------
        preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/', $value, $matches);

        // -----------------------------------------------------
        // If the string did not exceed the limit, we won't
        // need an ending character.
        // -----------------------------------------------------
        if (strlen($value) == strlen($matches[0]))
        {
            $end = '';
        }

        return rtrim($matches[0]).$end;
    }

    /**
     * Limit the number of characters in a string. Word integrity will be preserved.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public static function characters($value, $limit, $end = '&#8230;')
    {
        if (strlen($value) < $limit)
        {
            return $value;
        }

        // -----------------------------------------------------
        // Replace new lines and whitespace in the string.
        // -----------------------------------------------------
        $value = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $value));

        if (strlen($value) <= $limit)
        {
            return $value;
        }

        $out = '';

        // -----------------------------------------------------
        // The string exceeds the character limit. Add each word
        // to the output individually until we reach the limit.
        // -----------------------------------------------------
        foreach (explode(' ', trim($value)) as $val)
        {
            $out .= $val.' ';

            if (strlen($out) >= $limit)
            {
                $out = trim($out);

                return (strlen($out) == strlen($value)) ? $out : $out.$end;
            }
        }
    }

    /**
     * Censor a string.
     *
     * @param  string  $value
     * @param  array   $censored
     * @param  string  $replacement
     * @return string
     */
    public static function censor($value, $censored, $replacement = '####')
    {
        $value = ' '.$value.' ';

        // -----------------------------------------------------
        // Assume the word will be book-ended by the following.
        // -----------------------------------------------------
        $delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

        // -----------------------------------------------------
        // Replace the censored words.
        // -----------------------------------------------------
        foreach ($censored as $word)
        {
            if ($replacement != '')
            {
                $value = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($word, '/')).")({$delim})/i", "\\1{$replacement}\\3", $value);
            }
            else
            {
                $value = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($word, '/')).")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $value);
            }
        }

        return trim($value);        
    }

}