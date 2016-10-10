<?php

/**
 * PHPExcel_CachedObjectStorageFactory
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_CachedObjectStorageFactory
{
    const cache_in_memory               = 'Memory';
    const cache_in_memory_gzip          = 'MemoryGZip';
    const cache_in_memory_serialized    = 'MemorySerialized';
    const cache_igbinary                = 'Igbinary';
    const cache_to_discISAM             = 'DiscISAM';
    const cache_to_apc                  = 'APC';
    const cache_to_memcache             = 'Memcache';
    const cache_to_phpTemp              = 'PHPTemp';
    const cache_to_wincache             = 'Wincache';
    const cache_to_sqlite               = 'SQLite';
    const cache_to_sqlite3              = 'SQLite3';

    /**
     * Name of the method used for cell cacheing
     *
     * @var string
     */
    private static $cacheStorageMethod = null;

    /**
     * Name of the class used for cell cacheing
     *
     * @var string
     */
    private static $cacheStorageClass = null;

    /**
     * List of all possible cache storage methods
     *
     * @var string[]
     */
    private static $storageMethods = array(
        self::cache_in_memory,
        self::cache_in_memory_gzip,
        self::cache_in_memory_serialized,
        self::cache_igbinary,
        self::cache_to_phpTemp,
        self::cache_to_discISAM,
        self::cache_to_apc,
        self::cache_to_memcache,
        self::cache_to_wincache,
        self::cache_to_sqlite,
        self::cache_to_sqlite3,
    );

    /**
     * Default arguments for each cache storage method
     *
     * @var array of mixed array
     */
    private static $storageMethodDefaultParameters = array(
        self::cache_in_memory               => array(
                                                    ),
        self::cache_in_memory_gzip          => array(
                                                    ),
        self::cache_in_memory_serialized    => array(
                                                    ),
        self::cache_igbinary                => array(
                                                    ),
        self::cache_to_phpTemp              => array( 'memoryCacheSize' => '1MB'
                                                    ),
        self::cache_to_discISAM             => array( 'dir'             => null
                                                    ),
        self::cache_to_apc                  => array( 'cacheTime'       => 600
                                                    ),
        self::cache_to_memcache             => array( 'memcacheServer'  => 'localhost',
                                                      'memcachePort'    => 11211,
                                                      'cacheTime'       => 600
                                                    ),
        self::cache_to_wincache             => array( 'cacheTime'       => 600
                                                    ),
        self::cache_to_sqlite               => array(
                                                    ),
        self::cache_to_sqlite3              => array(
                                                    ),
    );

    /**
     * Arguments for the active cache storage method
     *
     * @var array of mixed array
     */
    private static $storageMethodParameters = array();

    /**
     * Return the current cache storage method
     *
     * @return string|null
     **/
    public static function getCacheStorageMethod()
    {
        return self::$cacheStorageMethod;
    }

    /**
     * Return the current cache storage class
     *
     * @return PHPExcel_CachedObjectStorage_ICache|null
     **/
    public static function getCacheStorageClass()
    {
        return self::$cacheStorageClass;
    }

    /**
     * Return the list of all possible cache storage methods
     *
     * @return string[]
     **/
    public static function getAllCacheStorageMethods()
    {
        return self::$storageMethods;
    }

    /**
     * Return the list of all available cache storage methods
     *
     * @return string[]
     **/
    public static function getCacheStorageMethods()
    {
        $activeMethods = array();
        foreach (self::$storageMethods as $storageMethod) {
            $cacheStorageClass = 'PHPExcel_CachedObjectStorage_' . $storageMethod;
            if (call_user_func(array($cacheStorageClass, 'cacheMethodIsAvailable'))) {
                $activeMethods[] = $storageMethod;
            }
        }
        return $activeMethods;
    }

    /**
     * Identify the cache storage method to use
     *
     * @param    string            $method        Name of the method to use for cell cacheing
     * @param    array of mixed    $arguments    Additional arguments to pass to the cell caching class
     *                                        when instantiating
     * @return boolean
     **/
    public static function initialize($method = self::cache_in_memory, $arguments = array())
    {
        if (!in_array($method, self::$storageMethods)) {
            return false;
        }

        $cacheStorageClass = 'PHPExcel_CachedObjectStorage_'.$method;
        if (!call_user_func(array( $cacheStorageClass,
                                   'cacheMethodIsAvailable'))) {
            return false;
        }

        self::$storageMethodParameters[$method] = self::$storageMethodDefaultParameters[$method];
        foreach ($arguments as $k => $v) {
            if (array_key_exists($k, self::$storageMethodParameters[$method])) {
                self::$storageMethodParameters[$method][$k] = $v;
            }
        }

        if (self::$cacheStorageMethod === null) {
            self::$cacheStorageClass = 'PHPExcel_CachedObjectStorage_' . $method;
            self::$cacheStorageMethod = $method;
        }
        return true;
    }

    /**
     * Initialise the cache storage
     *
     * @param    PHPExcel_Worksheet     $parent        Enable cell caching for this worksheet
     * @return    PHPExcel_CachedObjectStorage_ICache
     **/
    public static function getInstance(PHPExcel_Worksheet $parent)
    {
        $cacheMethodIsAvailable = true;
        if (self::$cacheStorageMethod === null) {
            $cacheMethodIsAvailable = self::initialize();
        }

        if ($cacheMethodIsAvailable) {
            $instance = new self::$cacheStorageClass(
                $parent,
                self::$storageMethodParameters[self::$cacheStorageMethod]
            );
            if ($instance !== null) {
                return $instance;
            }
        }

        return false;
    }

    /**
     * Clear the cache storage
     *
     **/
    public static function finalize()
    {
        self::$cacheStorageMethod = null;
        self::$cacheStorageClass = null;
        self::$storageMethodParameters = array();
    }
}
