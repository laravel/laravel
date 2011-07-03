<?php namespace System;

class Form {

	/**
	 * Stores labels names.
	 *
	 * @var array
	 */
	private static $labels = array();

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
		if (is_null($action))
		{
			$action = Request::uri();
		}

		$attributes['action'] = HTML::entities(URL::to($action));

		// -------------------------------------------------------
		// If the request method is PUT or DELETE, we'll default
		// the request method to POST.
		// -------------------------------------------------------
		$attributes['method'] = ($method == 'GET' or $method == 'POST') ? $method : 'POST';

		// -------------------------------------------------------
		// Set the default character set if it hasn't already been
		// set in the attributes.
		// -------------------------------------------------------
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
			$html .= PHP_EOL.static::hidden('REQUEST_METHOD', $method);
		}

		return $html.PHP_EOL;
	}

	/**
	 * Open a HTML form that accepts file uploads.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */	
	public static function open_multipart($action = null, $method = 'POST', $attributes = array())
	{
		$attributes['enctype'] = 'multipart/form-data';
		return static::open($action, $method, $attributes);
	}

	/**
	 * Close a HTML form.
	 *
	 * @return string
	 */
	public static function close()
	{
		return '</form>'.PHP_EOL;
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
		// -------------------------------------------------------
		// CSRF tokens are stored in the session, so we need to
		// make sure a driver has been specified.
		// -------------------------------------------------------
		if (Config::get('session.driver') == '')
		{
			throw new \Exception('Sessions must be enabled to retrieve a CSRF token.');			
		}

		return Session::get('csrf_token');
	}

	/**
	 * Create a HTML label element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public static function label($name, $value, $attributes = array())
	{
		static::$labels[] = $name;
		return '<label for="'.$name.'"'.HTML::attributes($attributes).'>'.HTML::entities($value).'</label>'.PHP_EOL;
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
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */			
	public static function hidden($name, $value = null, $attributes = array())
	{
		return static::input('hidden', $name, $value, $attributes);
	}

	/**
	 * Create a HTML email input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function email($name, $value = null, $attributes = array())
	{
		return static::input('email', $name, $value, $attributes);
	}

	/**
	 * Create a HTML URL input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function url($name, $value = null, $attributes = array())
	{
		return static::input('url', $name, $value, $attributes);
	}

	/**
	 * Create a HTML search input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function search($name, $value = null, $attributes = array())
	{
		return static::input('search', $name, $value, $attributes);
	}

	/**
	 * Create a HTML color input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function color($name, $value = null, $attributes = array())
	{
		return static::input('color', $name, $value, $attributes);
	}

	/**
	 * Create a HTML number input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function number($name, $value = null, $attributes = array())
	{
		return static::input('number', $name, $value, $attributes);
	}

	/**
	 * Create a HTML range input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function range($name, $value = null, $attributes = array())
	{
		return static::input('range', $name, $value, $attributes);
	}

	/**
	 * Create a HTML telephone input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function tel($name, $value = null, $attributes = array())
	{
		return static::input('tel', $name, $value, $attributes);
	}

	/**
	 * Create a HTML date input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function date($name, $value = null, $attributes = array())
	{
		return static::input('date', $name, $value, $attributes);
	}

	/**
	 * Create a HTML time input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function time($name, $value = null, $attributes = array())
	{
		return static::input('time', $name, $value, $attributes);
	}

	/**
	 * Create a HTML datetime input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function datetime($name, $value = null, $attributes = array())
	{
		return static::input('datetime', $name, $value, $attributes);
	}

	/**
	 * Create a HTML local datetime input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function datetime_local($name, $value = null, $attributes = array())
	{
		return static::input('datetime-local', $name, $value, $attributes);
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
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function submit($value, $attributes = array())
	{
		return static::input('submit', null, $value, $attributes);
	}

	/**
	 * Create a HTML reset input element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function reset($value, $attributes = array())
	{
		return static::input('reset', null, $value, $attributes);
	}

	/**
	 * Create a HTML image input element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function image($value, $attributes = array())
	{
		return static::input('image', null, $value, $attributes);
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

		$attributes['id'] = static::id($name, $attributes);

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
		$attributes['id'] = static::id($name, $attributes);

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
		$attributes['id'] = static::id($name, $attributes);

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
	public static function input($type, $name, $value = null, $attributes = array())
	{
		$attributes['type'] = $type;
		$attributes['name'] = $name;
		$attributes['value'] = $value;
		$attributes['id'] = static::id($name, $attributes);

		return '<input'.HTML::attributes($attributes).'>'.PHP_EOL;
	}

	/**
	 * Determine the ID attribute for a form element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return mixed
	 */
	private static function id($name, $attributes)
	{
		// -------------------------------------------------------
		// If an ID attribute was already explicitly specified
		// for the element, just use that.
		// -------------------------------------------------------
		if (array_key_exists('id', $attributes))
		{
			return $attributes['id'];
		}

		// -------------------------------------------------------
		// If a label element was created with a value matching
		// the name of the form element, use the name as the ID.
		// -------------------------------------------------------
		if (in_array($name, static::$labels))
		{
			return $name;
		}
	}

}