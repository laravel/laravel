<?php
/**
* Authority
*
* Authority is an authorization library for CodeIgniter 2+ and PHPActiveRecord
* This library is inspired by, and largely based off, Ryan Bates' CanCan gem
* for Ruby on Rails. It is not a 1:1 port, but the essentials are available.
* Please check out his work at http://github.com/ryanb/cancan/
*
* @package Authority
* @version 0.0.3
* @author Matthew Machuga
* @license MIT License
* @copyright 2011 Matthew Machuga
* @link http://github.com/machuga
*
**/

require 'ability.php';
require 'rule.php';

use Laravel\Auth;

class Authority extends Authority\Ability {

	protected static function current_user()
	{
		return Auth::user() ?: new User;
	}

	public static function initialize($user)
	{
		$config = Config::get('authority');
		call_user_func($config['initialize'], $user);
	}

}
