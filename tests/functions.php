<?php
/*
 * Spoof functions
 *
 * Adding these functions to the Laravel namespace prevents the PHP defaults
 * from being called and bypasses many headers already sent errors.
 */
namespace Laravel;

/**
 * The return value for headers_sent()
 *
 * @var bool
 * @internal
 */
$headers_sent = false;

/**
 * Set the return value for headers_sent()
 *
 * @param  bool  $value
 * @return void
 */
function set_headers_sent($value = false)
{
	global $headers_sent;
	$headers_sent = $value;
}

/**
 * Spoof headers_sent() function.
 *
 * @see \headers_sent()
 * @return bool
 */
function headers_sent()
{
	global $headers_sent;
	return $headers_sent;
}

/**
 * Spoof setcookie() function.
 *
 * @see \setcookie()
 * @param  string  $name
 * @param  string  $value
 * @param  int     $expire
 * @param  string  $path
 * @param  string  $domain
 * @param  bool    $secure
 * @param  bool    $httponly
 * @return bool
 *
 * @todo Gather the setcookies somewhere for inspection?
 */
function setcookie($name, $value = '', $expire = 0, $path = '', $domain = '', $secure = false, $httponly = false)
{
	return true;
}

/**
 * Spoof header() function.
 *
 * @see \header()
 * @return void
 *
 * @todo Gather the headers somewhere for inspection?
 */
function header($name, $replace = true, $http_response_code = 0)
{}