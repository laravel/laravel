<?php

/*
 * This file is part of SwiftMailer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Utility Class allowing users to simply check expressions again Swift Grammar.
 *
 * @author  Xavier De Cock <xdecock@gmail.com>
 */
class Swift_Validate
{
    /**
     * Grammar Object.
     *
     * @var Swift_Mime_Grammar
     */
    private static $grammar = null;

    /**
     * Checks if an e-mail address matches the current grammars.
     *
     * @param string $email
     *
     * @return bool
     */
    public static function email($email)
    {
        if (self::$grammar === null) {
            self::$grammar = Swift_DependencyContainer::getInstance()
                ->lookup('mime.grammar');
        }

        return (bool) preg_match(
                '/^'.self::$grammar->getDefinition('addr-spec').'$/D',
                $email
            );
    }
}
