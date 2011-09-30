<?php

function fe($function)
{
	return function_exists($function);
}

function e($value)
{
	return HTML::entities($value);
}

function __($key, $replacements = array(), $language = null)
{
	return Lang::line($key, $replacements, $language);
}