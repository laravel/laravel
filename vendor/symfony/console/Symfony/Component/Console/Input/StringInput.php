<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Input;

/**
 * StringInput represents an input provided as a string.
 *
 * Usage:
 *
 *     $input = new StringInput('foo --bar="foobar"');
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class StringInput extends ArgvInput
{
    const REGEX_STRING = '([^\s]+?)(?:\s|(?<!\\\\)"|(?<!\\\\)\'|$)';
    const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')';

    /**
     * Constructor.
     *
     * @param string          $input      An array of parameters from the CLI (in the argv format)
     * @param InputDefinition $definition A InputDefinition instance
     *
     * @deprecated The second argument is deprecated as it does not work (will be removed in 3.0), use 'bind' method instead
     *
     * @api
     */
    public function __construct($input, InputDefinition $definition = null)
    {
        parent::__construct(array(), null);

        $this->setTokens($this->tokenize($input));

        if (null !== $definition) {
            $this->bind($definition);
        }
    }

    /**
     * Tokenizes a string.
     *
     * @param string $input The input to tokenize
     *
     * @return array An array of tokens
     *
     * @throws \InvalidArgumentException When unable to parse input (should never happen)
     */
    private function tokenize($input)
    {
        $tokens = array();
        $length = strlen($input);
        $cursor = 0;
        while ($cursor < $length) {
            if (preg_match('/\s+/A', $input, $match, null, $cursor)) {
            } elseif (preg_match('/([^="\'\s]+?)(=?)('.self::REGEX_QUOTED_STRING.'+)/A', $input, $match, null, $cursor)) {
                $tokens[] = $match[1].$match[2].stripcslashes(str_replace(array('"\'', '\'"', '\'\'', '""'), '', substr($match[3], 1, strlen($match[3]) - 2)));
            } elseif (preg_match('/'.self::REGEX_QUOTED_STRING.'/A', $input, $match, null, $cursor)) {
                $tokens[] = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
            } elseif (preg_match('/'.self::REGEX_STRING.'/A', $input, $match, null, $cursor)) {
                $tokens[] = stripcslashes($match[1]);
            } else {
                // should never happen
                // @codeCoverageIgnoreStart
                throw new \InvalidArgumentException(sprintf('Unable to parse input near "... %s ..."', substr($input, $cursor, 10)));
                // @codeCoverageIgnoreEnd
            }

            $cursor += strlen($match[0]);
        }

        return $tokens;
    }
}
