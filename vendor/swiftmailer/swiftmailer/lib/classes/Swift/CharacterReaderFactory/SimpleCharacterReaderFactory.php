<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Standard factory for creating CharacterReaders.
 *
 * @author Chris Corbyn
 */
class Swift_CharacterReaderFactory_SimpleCharacterReaderFactory implements Swift_CharacterReaderFactory
{
    /**
     * A map of charset patterns to their implementation classes.
     *
     * @var array
     */
    private static $_map = array();

    /**
     * Factories which have already been loaded.
     *
     * @var Swift_CharacterReaderFactory[]
     */
    private static $_loaded = array();

    /**
     * Creates a new CharacterReaderFactory.
     */
    public function __construct()
    {
        $this->init();
    }

    public function __wakeup()
    {
        $this->init();
    }

    public function init()
    {
        if (count(self::$_map) > 0) {
            return;
        }

        $prefix = 'Swift_CharacterReader_';

        $singleByte = array(
            'class' => $prefix.'GenericFixedWidthReader',
            'constructor' => array(1),
            );

        $doubleByte = array(
            'class' => $prefix.'GenericFixedWidthReader',
            'constructor' => array(2),
            );

        $fourBytes = array(
            'class' => $prefix.'GenericFixedWidthReader',
            'constructor' => array(4),
            );

        // Utf-8
        self::$_map['utf-?8'] = array(
            'class' => $prefix.'Utf8Reader',
            'constructor' => array(),
            );

        //7-8 bit charsets
        self::$_map['(us-)?ascii'] = $singleByte;
        self::$_map['(iso|iec)-?8859-?[0-9]+'] = $singleByte;
        self::$_map['windows-?125[0-9]'] = $singleByte;
        self::$_map['cp-?[0-9]+'] = $singleByte;
        self::$_map['ansi'] = $singleByte;
        self::$_map['macintosh'] = $singleByte;
        self::$_map['koi-?7'] = $singleByte;
        self::$_map['koi-?8-?.+'] = $singleByte;
        self::$_map['mik'] = $singleByte;
        self::$_map['(cork|t1)'] = $singleByte;
        self::$_map['v?iscii'] = $singleByte;

        //16 bits
        self::$_map['(ucs-?2|utf-?16)'] = $doubleByte;

        //32 bits
        self::$_map['(ucs-?4|utf-?32)'] = $fourBytes;

        // Fallback
        self::$_map['.*'] = $singleByte;
    }

    /**
     * Returns a CharacterReader suitable for the charset applied.
     *
     * @param string $charset
     *
     * @return Swift_CharacterReader
     */
    public function getReaderFor($charset)
    {
        $charset = trim(strtolower($charset));
        foreach (self::$_map as $pattern => $spec) {
            $re = '/^'.$pattern.'$/D';
            if (preg_match($re, $charset)) {
                if (!array_key_exists($pattern, self::$_loaded)) {
                    $reflector = new ReflectionClass($spec['class']);
                    if ($reflector->getConstructor()) {
                        $reader = $reflector->newInstanceArgs($spec['constructor']);
                    } else {
                        $reader = $reflector->newInstance();
                    }
                    self::$_loaded[$pattern] = $reader;
                }

                return self::$_loaded[$pattern];
            }
        }
    }
}
