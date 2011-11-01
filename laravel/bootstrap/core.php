<?php namespace Laravel;

require 'constants.php';

require SYS_PATH.'arr'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'facades'.EXT;
require SYS_PATH.'container'.EXT;
require SYS_PATH.'autoloader'.EXT;

Config::load('application');
Config::load('container');
Config::load('session');

IoC::bootstrap();

$loader = new Autoloader;

spl_autoload_register(array('Laravel\\Autoloader', 'load'));

function e($value)
{
	return HTML::entities($value);
}

function __($key, $replacements = array(), $language = null)
{
	return Lang::line($key, $replacements, $language);
}