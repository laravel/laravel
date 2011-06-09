<?php namespace System;

class Str {

    /**
     * The default encoding.
     *
     * @var string
     */
    private static $encoding = 'UTF-8';

	/**
	 * Convert HTML characters to entities.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
        return htmlentities($value, ENT_QUOTES, static::$encoding, false);
	}

    /**
     * Convert a string to lowercase.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return function_exists('mb_strtolower') ? mb_strtolower($value, static::$encoding) : strtolower($value);
    }

    /**
     * Convert a string to uppercase.
     *
     * @param  string  $value
     * @return string
     */
    public static function upper($value)
    {
        return function_exists('mb_strtoupper') ? mb_strtoupper($value, static::$encoding) : strtoupper($value);
    }

    /**
     * Convert a string to title case (ucwords).
     *
     * @param  string  $value
     * @return string
     */
    public static function title($value)
    {
        return (function_exists('mb_convert_case')) ? mb_convert_case($value, MB_CASE_TITLE, static::$encoding) : ucwords(strtolower($value));
    }

    /**
     * Generate a random alpha-numeric string.
     *
     * @param  int     $length
     * @return string
     */
    public static function random($length = 16)
    {
        // -----------------------------------------------------
        // Split the character pool into an array.
        // -----------------------------------------------------
        $pool = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 1);

        // -----------------------------------------------------
        // Initialize the return value.
        // -----------------------------------------------------
        $value = '';

        // -----------------------------------------------------
        // Build the random string.
        // -----------------------------------------------------
        for ($i = 0; $i < $length; $i++)
        {
            $value .= $pool[mt_rand(0, 61)];
        }

        return $value;
    }

}