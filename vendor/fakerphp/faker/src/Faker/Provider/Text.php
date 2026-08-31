<?php

namespace Faker\Provider;

abstract class Text extends Base
{
    protected static $baseText = '';
    protected static $separator = ' ';
    protected static $separatorLen = 1;
    protected $explodedText;
    protected $consecutiveWords = [];
    protected static $textStartsWithUppercase = true;

    /**
     * Generate a text string by the Markov chain algorithm.
     *
     * Depending on the $maxNbChars, returns a random valid looking text. The algorithm
     * generates a weighted table with the specified number of words as the index and the
     * possible following words as the value.
     *
     * @example 'Alice, swallowing down her flamingo, and began by taking the little golden key'
     *
     * @param int $maxNbChars Maximum number of characters the text should contain (minimum: 10)
     * @param int $indexSize  Determines how many words are considered for the generation of the next word.
     *                        The minimum is 1, and it produces a higher level of randomness, although the
     *                        generated text usually doesn't make sense. Higher index sizes (up to 5)
     *                        produce more correct text, at the price of less randomness.
     *
     * @return string
     */
    public function realText($maxNbChars = 200, $indexSize = 2)
    {
        return $this->realTextBetween((int) round($maxNbChars * 0.8), $maxNbChars, $indexSize);
    }

    /**
     * Generate a text string by the Markov chain algorithm.
     *
     * Depending on the $maxNbChars, returns a random valid looking text. The algorithm
     * generates a weighted table with the specified number of words as the index and the
     * possible following words as the value.
     *
     * @example 'Alice, swallowing down her flamingo, and began by taking the little golden key'
     *
     * @param int $minNbChars Minimum number of characters the text should contain (maximum: 8)
     * @param int $maxNbChars Maximum number of characters the text should contain (minimum: 10)
     * @param int $indexSize  Determines how many words are considered for the generation of the next word.
     *                        The minimum is 1, and it produces a higher level of randomness, although the
     *                        generated text usually doesn't make sense. Higher index sizes (up to 5)
     *                        produce more correct text, at the price of less randomness.
     *
     * @return string
     */
    public function realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2)
    {
        if ($minNbChars < 1) {
            throw new \InvalidArgumentException('minNbChars must be at least 1');
        }

        if ($maxNbChars < 10) {
            throw new \InvalidArgumentException('maxNbChars must be at least 10');
        }

        if ($indexSize < 1) {
            throw new \InvalidArgumentException('indexSize must be at least 1');
        }

        if ($indexSize > 5) {
            throw new \InvalidArgumentException('indexSize must be at most 5');
        }

        if ($minNbChars >= $maxNbChars) {
            throw new \InvalidArgumentException('minNbChars must be smaller than maxNbChars');
        }

        $words = $this->getConsecutiveWords($indexSize);
        $iterations = 0;

        do {
            ++$iterations;

            if ($iterations >= 100) {
                throw new \OverflowException(sprintf('Maximum retries of %d reached without finding a valid real text', $iterations));
            }

            $result = $this->generateText($maxNbChars, $words);
        } while (static::strlen($result) <= $minNbChars);

        return $result;
    }

    /**
     * @param int   $maxNbChars
     * @param array $words
     *
     * @return string
     */
    protected function generateText($maxNbChars, $words)
    {
        $result = [];
        $resultLength = 0;
        // take a random starting point
        $next = static::randomKey($words);

        while ($resultLength < $maxNbChars && isset($words[$next])) {
            // fetch a random word to append
            $word = static::randomElement($words[$next]);

            // calculate next index
            $currentWords = static::explode($next);
            $currentWords[] = $word;
            array_shift($currentWords);
            $next = static::implode($currentWords);

            // ensure text starts with an uppercase letter
            if ($resultLength == 0 && !static::validStart($word)) {
                continue;
            }

            // append the element
            $result[] = $word;
            $resultLength += static::strlen($word) + static::$separatorLen;
        }

        // remove the element that caused the text to overflow
        array_pop($result);

        // build result
        $result = static::implode($result);

        return static::appendEnd($result);
    }

    protected function getConsecutiveWords($indexSize)
    {
        if (!isset($this->consecutiveWords[$indexSize])) {
            $parts = $this->getExplodedText();
            $words = [];
            $index = [];

            for ($i = 0; $i < $indexSize; ++$i) {
                $index[] = array_shift($parts);
            }

            for ($i = 0, $count = count($parts); $i < $count; ++$i) {
                $stringIndex = static::implode($index);

                if (!isset($words[$stringIndex])) {
                    $words[$stringIndex] = [];
                }
                $word = $parts[$i];
                $words[$stringIndex][] = $word;
                array_shift($index);
                $index[] = $word;
            }
            // cache look up words for performance
            $this->consecutiveWords[$indexSize] = $words;
        }

        return $this->consecutiveWords[$indexSize];
    }

    protected function getExplodedText()
    {
        if ($this->explodedText === null) {
            $this->explodedText = static::explode(preg_replace('/\s+/u', ' ', static::$baseText));
        }

        return $this->explodedText;
    }

    protected static function explode($text)
    {
        return explode(static::$separator, $text);
    }

    protected static function implode($words)
    {
        return implode(static::$separator, $words);
    }

    protected static function strlen($text)
    {
        return function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    }

    protected static function validStart($word)
    {
        $isValid = true;

        if (static::$textStartsWithUppercase) {
            $isValid = preg_match('/^\p{Lu}/u', $word);
        }

        return $isValid;
    }

    protected static function appendEnd($text)
    {
        return preg_replace("/([ ,-:;\x{2013}\x{2014}]+$)/us", '', $text) . '.';
    }
}
