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
     * Generate a random alpha-numeric string.
     *
     * @param  int     $length
     * @return string
     */
    public static function random($length = 16)
    {
        $pool = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 1);

        $value = '';

        for ($i = 0; $i < $length; $i++)
        {
            $value .= $pool[mt_rand(0, 61)];
        }

        return $value;
    }

}