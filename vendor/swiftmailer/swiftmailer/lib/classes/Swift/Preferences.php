<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Changes some global preference settings in Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Preferences
{
    /** Singleton instance */
    private static $_instance = null;

    /** Constructor not to be used */
    private function __construct()
    {
    }

    /**
     * Gets the instance of Preferences.
     *
     * @return Swift_Preferences
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Set the default charset used.
     *
     * @param string $charset
     *
     * @return Swift_Preferences
     */
    public function setCharset($charset)
    {
        Swift_DependencyContainer::getInstance()
            ->register('properties.charset')->asValue($charset);

        return $this;
    }

    /**
     * Set the directory where temporary files can be saved.
     *
     * @param string $dir
     *
     * @return Swift_Preferences
     */
    public function setTempDir($dir)
    {
        Swift_DependencyContainer::getInstance()
            ->register('tempdir')->asValue($dir);

        return $this;
    }

    /**
     * Set the type of cache to use (i.e. "disk" or "array").
     *
     * @param string $type
     *
     * @return Swift_Preferences
     */
    public function setCacheType($type)
    {
        Swift_DependencyContainer::getInstance()
            ->register('cache')->asAliasOf(sprintf('cache.%s', $type));

        return $this;
    }

    /**
     * Set the QuotedPrintable dot escaper preference.
     *
     * @param bool $dotEscape
     *
     * @return Swift_Preferences
     */
    public function setQPDotEscape($dotEscape)
    {
        $dotEscape = !empty($dotEscape);
        Swift_DependencyContainer::getInstance()
            ->register('mime.qpcontentencoder')
            ->asNewInstanceOf('Swift_Mime_ContentEncoder_QpContentEncoder')
            ->withDependencies(array('mime.charstream', 'mime.bytecanonicalizer'))
            ->addConstructorValue($dotEscape);

        return $this;
    }
}
