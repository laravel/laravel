<?php namespace System;

class Str {

	/**
	 * Convert HTML characters to entities.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
        return htmlentities($value, ENT_QUOTES, Config::get('application.encoding'), false);
	}

    /**
     * Convert a string to lowercase.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return function_exists('mb_strtolower') ? mb_strtolower($value, Config::get('application.encoding')) : strtolower($value);
    }

    /**
     * Convert a string to uppercase.
     *
     * @param  string  $value
     * @return string
     */
    public static function upper($value)
    {
        return function_exists('mb_strtoupper') ? mb_strtoupper($value, Config::get('application.encoding')) : strtoupper($value);
    }

    /**
     * Convert a string to title case (ucwords).
     *
     * @param  string  $value
     * @return string
     */
    public static function title($value)
    {
        return (function_exists('mb_convert_case')) ? mb_convert_case($value, MB_CASE_TITLE, Config::get('application.encoding')) : ucwords(strtolower($value));
    }

    /**
     * Get the length of a string.
     *
     * @param  string  $value
     * @return int
     */
    public static function length($value)
    {
        return function_exists('mb_strlen') ? mb_strlen($value, Config::get('application.encoding')) : strlen($value);
    }

    /**
     * Generate a random alpha or alpha-numeric string.
     *
     * Supported types: 'alnum' and 'alpha'.
     *
     * @param  int     $length
     * @param  string  $type
     * @return string
     */
    public static function random($length = 16, $type = 'alnum')
    {
        $value = '';

        // -----------------------------------------------------
        // Get the proper character pool for the type.
        // -----------------------------------------------------
        switch ($type)
        {
            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;

            default:
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        // -----------------------------------------------------
        // Get the pool length and split the pool into an array.
        // -----------------------------------------------------
        $pool_length = strlen($pool) - 1;
        $pool = str_split($pool, 1);

        // -----------------------------------------------------
        // Build the random string to the specified length.
        // -----------------------------------------------------
        for ($i = 0; $i < $length; $i++)
        {
            $value .= $pool[mt_rand(0, $pool_length)];
        }

        return $value;
    }

}