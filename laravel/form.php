<?php namespace Laravel;

class Form {

	/**
	 * All of the label names that have been created.
	 *
	 * These names are stored so that input elements can automatically be assigned
	 * an ID based on the corresponding label name.
	 *
	 * @var array
	 */
	private static $labels = array();

	/**
	 * Open a HTML form.
	 *
	 * <code>
	 *		// Open a POST form for the current URI
	 *		echo Form::open();
	 *
	 *		// Open a POST form to a specified URI
	 *		echo Form::open('user/login');
	 *
	 *		// Open a PUT form to a specified URI
	 *		echo Form::open('user/profile', 'put');
	 * </code>
	 *
	 * Note: If PUT or DELETE is specified as the form method, a hidden input field will be generated
	 *       containing the request method. PUT and DELETE are not supported by HTML forms, so the
	 *       hidden field will allow us to "spoof" PUT and DELETE requests.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */
	public static function open($action = null, $method = 'POST', $attributes = array(), $https = false)
	{
		list($attributes['action'], $attributes['method']) = array(static::action($action, $https), static::method($method));

		if ( ! array_key_exists('accept-charset', $attributes))
		{
			$attributes['accept-charset'] = Config::get('application.encoding');			
		}

		$append = ($method == 'PUT' or $method == 'DELETE') ? static::hidden('REQUEST_METHOD', $method) : '';

		return '<form'.HTML::attributes($attributes).'>'.$append.PHP_EOL;
	}

	/**
	 * Determine the appropriate request method to use for a form.
	 *
	 * Since PUT and DELETE requests are spoofed using POST requests, we will substitute
	 * POST for any PUT or DELETE methods. Otherwise, the specified method will be used.
	 *
	 * @param  string  $method
	 * @return string
	 */
	private static function method($method)
	{
		return strtoupper(($method == 'PUT' or $method == 'DELETE') ? 'POST' : $method);
	}

	/**
	 * Determine the appropriate action parameter to use for a form.
	 *
	 * If no action is specified, the current request URI will be used.
	 *
	 * @param  string  $action
	 * @param  bool    $https
	 * @return string
	 */
	private static function action($action, $https)
	{
		return HTML::entities(URL::to(((is_null($action)) ? Request::uri() : $action), $https));
	}

	/**
	 * Open a HTML form with a HTTPS action URI.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */
	public static function open_secure($action = null, $method = 'POST', $attributes = array())
	{
		return static::open($action, $method, $attributes, true);
	}

	/**
	 * Open a HTML form that accepts file uploads.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */	
	public static function open_for_files($action = null, $method = 'POST', $attributes = array(), $https = false)
	{
		$attributes['enctype'] = 'multipart/form-data';

		return static::open($action, $method, $attributes, $https);
	}

	/**
	 * Open a HTML form that accepts file uploads with a HTTPS action URI.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */	
	public static function open_secure_for_files($action = null, $method = 'POST', $attributes = array())
	{
		return static::open_for_files($action, $method, $attributes, true);
	}

	/**
	 * Close a HTML form.
	 *
	 * @return string
	 */
	public static function close()
	{
		return '</form>';
	}

	/**
	 * Generate a hidden field containing the current CSRF token.
	 *
	 * If a session driver is not provided, the default session driver will be used.
	 *
	 * @param  Session\Driver  $driver
	 * @return string
	 */
	public static function token(Session\Driver $driver = null)
	{
		if (is_null($driver)) $driver = Session::driver();

		return static::input('hidden', 'csrf_token', static::raw_token($driver));
	}

	/**
	 * Retrieve the current CSRF token.
	 *
	 * If a session driver is not provided, the default session driver will be used.
	 *
	 * @param  Session\Driver  $driver
	 * @return string
	 */
	public static function raw_token(Session\Driver $driver = null)
	{
		if (is_null($driver)) $driver = Session::driver();

		return $driver->get('csrf_token');
	}

	/**
	 * Create a HTML label element.
	 *
	 * <code>
	 *		echo Form::label('email', 'E-Mail Address');
	 * </code>
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
	 * Create a HTML input element.
	 *
	 * If an ID attribute is not specified and a label has been generated matching the input
	 * element name, the label name will be used as the element ID.
	 *
	 * <code>
	 *		// Generate a text type input element
	 *		echo Form::input('text', 'email');
	 *
	 *		// Generate a hidden type input element with a specified value
	 *		echo Form::input('hidden', 'secret', 'This is a secret.');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public static function input($type, $name, $value = null, $attributes = array())
	{
		$id = static::id($name, $attributes);

		return '<input'.HTML::attributes(array_merge($attributes, compact('type', 'name', 'value', 'id'))).'>'.PHP_EOL;
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
	 * Create a HTML telephone input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public static function telephone($name, $value = null, $attributes = array())
	{
		return static::input('tel', $name, $value, $attributes);
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
	 * Create a HTML textarea element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function textarea($name, $value = '', $attributes = array())
	{
		$attributes = array_merge($attributes, array('id' => static::id($name, $attributes), 'name' => $name));

		if ( ! isset($attributes['rows'])) $attributes['rows'] = 10;

		if ( ! isset($attributes['cols'])) $attributes['cols'] = 50;

		return '<textarea'.HTML::attributes($attributes).'>'.HTML::entities($value).'</textarea>'.PHP_EOL;
	}

	/**
	 * Create a HTML select element.
	 *
	 * <code>
	 *		// Generate a drop-down with the "S" item selected
	 *		echo Form::select('sizes', array('L' => 'Large', 'S' => 'Small'), 'S');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @param  string  $selected
	 * @param  array   $attributes
	 * @return string
	 */	
	public static function select($name, $options = array(), $selected = null, $attributes = array())
	{
		$attributes = array_merge($attributes, array('id' => static::id($name, $attributes), 'name' => $name));

		$html = array();

		foreach ($options as $value => $display)
		{
			$option_attributes = array('value' => HTML::entities($value), 'selected' => ($value == $selected) ? 'selected' : null);

			$html[] = '<option'.HTML::attributes($option_attributes).'>'.HTML::entities($display).'</option>';
		}

		return '<select'.HTML::attributes($attributes).'>'.implode('', $html).'</select>'.PHP_EOL;
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
		$attributes = array_merge($attributes, array('id' => static::id($name, $attributes), 'checked' => ($checked) ? 'checked' : null));

		return static::input($type, $name, $value, $attributes);
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
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public static function image($url, $name = null, $attributes = array())
	{
		$attributes['src'] = URL::to_asset($url);

		return static::input('image', $name, null, $attributes);
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
	 * Determine the ID attribute for a form element.
	 *
	 * An explicitly specified ID in the attributes takes first precedence, then
	 * the label names will be checked for a label matching the element name.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return mixed
	 */
	private static function id($name, $attributes)
	{
		if (array_key_exists('id', $attributes)) return $attributes['id'];

		if (in_array($name, static::$labels)) return $name;
	}

}