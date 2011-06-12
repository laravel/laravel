<?php namespace System;

class Form {

	/**
	 * Open a HTML form.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */	
	public static function open($action = null, $method = 'POST', $attributes = array())
	{
		// -------------------------------------------------------
		// If no action was given, use the current URI.
		// -------------------------------------------------------
		if (is_null($action))
		{
			$action = Request::uri();
		}

		$action = URL::to($action);

		$attributes['action'] = HTML::entities($action);
		$attributes['method'] = ($method == 'GET' or $method == 'POST') ? $method : 'POST';

		if ( ! array_key_exists('accept-charset', $attributes))
		{
			$attributes['accept-charset'] = Config::get('application.encoding');			
		}

		$html = '<form'.HTML::attributes($attributes).'>';

		// -------------------------------------------------------
		// If the method is PUT or DELETE, we'll need to spoof it
		// using a hidden input field. 
		//
		// For more information, see the Input library.
		// -------------------------------------------------------
		if ($method == 'PUT' or $method == 'DELETE')
		{
			$html .= PHP_EOL.static::hidden('request_method', $method);
		}

		return $html.PHP_EOL;
	}

	/**
	 * Generate a hidden field containing the current CSRF token.
	 *
	 * @return string
	 */
	public static function token()
	{
		return static::hidden('csrf_token', static::raw_token());
	}

	/**
	 * Retrieve the current CSRF token.
	 *
	 * @return string
	 */
	public static function raw_token()
	{
		if (Config::get('session.driver') == '')
		{
			throw new \Exception('Sessions must be enabled to retrieve a CSRF token.');			
		}

		return Session::get('csrf_token');
	}

	/**
	 * Create a HTML text input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public static function text($name, $value = null, $attributes = array())
	{
		return static::input('text', $name, $value, $attributes);
	}

	/**
	 * Create a HTML password input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */			
	public static function password($name, $attributes = array())
	{
		return static::input('password', $name, null, $attributes);
	}

	/**
	 * Create a HTML hidden input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */			
	public static function hidden($name, $value = null, $attributes = array())
	{
		return static::input('hidden', $name, $value, $attributes);
	}

	/**
	 * Create a HTML file input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */			
	public static function file($name, $attributes = array())
	{
		return static::input('file', $name, null, $attributes);
	}

	/**
	 * Create a HTML submit input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */
	public static function submit($value, $attributes = array())
	{
		return static::input('submit', null, $value, $attributes);
	}

	/**
	 * Create a HTML button element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function button($value, $attributes = array())
	{
		return '<button'.HTML::attributes($attributes).'>'.HTML::entities($value).'</button>'.PHP_EOL;
	}

	/**
	 * Create a HTML checkbox input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	public static function checkbox($name, $value = null, $checked = false, $attributes = array())
	{
		return static::checkable('checkbox', $name, $value, $checked, $attributes);
	}

	/**
	 * Create a HTML radio button input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	public static function radio($name, $value = null, $checked = false, $attributes = array())
	{
		return static::checkable('radio', $name, $value, $checked, $attributes);
	}

	/**
	 * Create a checkable input element.
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	private static function checkable($type, $name, $value, $checked, $attributes)
	{
		if ($checked === true)
		{
			$attributes['checked'] = 'checked';
		}

		return static::input($type, $name, $value, $attributes);
	}

	/**
	 * Create a HTML textarea element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function textarea($name, $value = '', $attributes = array())
	{
		$attributes['name'] = $name;

		// -------------------------------------------------------
		// Set the default number of rows.
		// -------------------------------------------------------
		if ( ! isset($attributes['rows']))
		{
			$attributes['rows'] = 10;
		}

		// -------------------------------------------------------
		// Set the default number of columns.
		// -------------------------------------------------------
		if ( ! isset($attributes['cols']))
		{
			$attributes['cols'] = 50;
		}

		return '<textarea'.HTML::attributes($attributes).'>'.HTML::entities($value).'</textarea>'.PHP_EOL;
	}

	/**
	 * Create a HTML select element.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @param  string  $selected
	 * @param  array   $attributes
	 * @return string
	 */	
	public static function select($name, $options = array(), $selected = null, $attributes = array())
	{
		$attributes['name'] = $name;

		$html_options = array();

		foreach ($options as $value => $display)
		{
			$option_attributes = array();

			$option_attributes['value'] = HTML::entities($value);
			$option_attributes['selected'] = ($value == $selected) ? 'selected' : null;

			$html_options[] = '<option'.HTML::attributes($option_attributes).'>'.HTML::entities($display).'</option>';
		}

		return '<select'.HTML::attributes($attributes).'>'.implode('', $html_options).'</select>'.PHP_EOL;
	}

	/**
	 * Create a HTML input element.
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  array   $attributes
	 * @return string
	 */		
	private static function input($type, $name, $value = null, $attributes = array())
	{
		$attributes['type'] = $type;
		$attributes['name'] = $name;
		$attributes['value'] = $value;

		return '<input'.HTML::attributes($attributes).' />'.PHP_EOL;
	}

}