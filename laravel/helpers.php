<?php 

require 'functionregister'.EXT;

use Laravel\FunctionRegister;

function e($value)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function __($key, $replacements = array(), $language = null)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function dd($value)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function array_get($array, $key, $default = null)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function array_set(&$array, $key, $value)
{
	$func = FunctionRegister::get(__FUNCTION__);
	return $func($array, $key, $value);
}


function array_forget(&$array, $key)
{
	$func = FunctionRegister::get(__FUNCTION__);
	return $func($array, $key);
}


function array_first($array, $callback, $default = null)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function array_strip_slashes($array)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function array_divide($array)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function array_pluck($array, $key)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function array_only($array, $keys)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function array_except($array, $keys)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function eloquent_to_json($models)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function magic_quotes()
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function head($array)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function url($url = '', $https = false)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function asset($url, $https = false)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function action($action, $parameters = array())
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function route($name, $parameters = array())
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function starts_with($haystack, $needle)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function ends_with($haystack, $needle)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function str_contains($haystack, $needle)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function str_finish($value, $cap)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function str_object($value)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function root_namespace($class, $separator = '\\')
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function class_basename($class)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function value($value)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function with($object)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}


function has_php($version)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function view($view, $data = array())
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function render($view, $data = array())
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function render_each($partial, array $data, $iterator, $empty = 'raw|')
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function yield($section)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}

function get_cli_option($option, $default = null)
{
	return FunctionRegister::call(__FUNCTION__, func_get_args());
}