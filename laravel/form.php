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
	protected static $labels = array();

	/**
	 * Open a HTML form.
	 *
	 * Note: If PUT or DELETE is specified as the form method, a hidden input field will be generated
	 *       containing the request method. PUT and DELETE are not supported by HTML forms, so the
	 *       hidden field will allow us to "spoof" PUT and DELETE requests.
	 *
	 * <code>
	 *		// Open a POST form to the current URI
	 *		echo Form::open();
	 *
	 *		// Open a POST form to a given URI
	 *		echo Form::open('user/profile');
	 *
	 *		// Open a PUT form to a given URI and add form attributes
	 *		echo Form::open('user/profile', 'put', array('class' => 'profile'));
	 * </code>
	 *
	 * @param  string   $action
	 * @param  string   $method
	 * @param  array    $attributes
	 * @param  bool     $https
	 * @return string
	 */
	public static function open($action = null, $method = 'POST', $attributes = array(), $https = false)
	{
		list($attributes['action'], $attributes['method']) = array(static::action($action, $https), static::method($method));

		if ( ! array_key_exists('accept-charset', $attributes))
		{
			$attributes['accept-charset'] = Config::get('application.encoding');
		}

		$append = ($method == 'PUT' or $method == 'DELETE') ? static::hidden(Request::spoofer, $method) : '';

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
	protected static function method($method)
	{
		return strtoupper(($method == 'PUT' or $method == 'DELETE') ? 'POST' : $method);
	}

	/**
	 * Determine the appropriate action parameter to use for a form.
	 *
	 * If no action is specified, the current request URI will be used.
	 *
	 * @param  string   $action
	 * @param  bool     $https
	 * @return string
	 */
	protected static function action($action, $https)
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
	 * @return string
	 */
	public static function token()
	{
		return static::input('hidden', 'csrf_token', static::raw_token());
	}

	/**
	 * Get the CSRF token for the current session.
	 *
	 * @return string
	 */
	public static function raw_token()
	{
		if (Config::get('session.driver') == '')
		{
			throw new \Exception("A session driver must be specified before using CSRF tokens.");			
		}

		return IoC::container()->resolve('laravel.session')->get('csrf_token');
	}

	/**
	 * Create a HTML label element.
	 *
	 * <code>
	 *		// Create a form label
	 *		echo Form::label('email', 'E-Mail Address');
	 *
	 *		// Create a form label with attributes
	 *		echo Form::label('email', 'E-Mail Address', array('class' => 'login'));
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
	 *		// Create a "text" type input element
	 *		echo Form::input('text', 'email');
	 *
	 *		// Create an input element with a specified value
	 *		echo Form::input('text', 'email', 'example@gmail.com');
	 *
	 *		// Create an input element with attributes
	 *		echo Form::input('text', 'email', 'example@gmail.com', array('class' => 'login'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public static function input($type, $name, $value = null, $attributes = array())
	{
		$name = (isset($attributes['name'])) ? $attributes['name'] : $name;

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
	 * <code>
	 *		// Create a textarea element
	 *		echo Form::textarea('comment');
	 *
	 *		// Create a textarea with specified rows and columns
	 *		echo Form::textarea('comment', '', array('rows' => 10, 'columns' => 50));
	 * </code>
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
	 *		// Create a selection element
	 *		echo Form::select('sizes', array('S' => 'Small', 'L' => 'Large'));
	 *
	 *		// Create a selection element with a given option pre-selected
	 *		echo Form::select('sizes', array('S' => 'Small', 'L' => 'Large'), 'L');
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
	 * <code>
	 *		// Create a checkbox element
	 *		echo Form::checkbox('terms');
	 *
	 *		// Create a checkbox element that is checked by default
	 *		echo Form::checkbox('terms', 'yes', true);
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	public static function checkbox($name, $value = 1, $checked = false, $attributes = array())
	{
		return static::checkable('checkbox', $name, $value, $checked, $attributes);
	}

	/**
	 * Create a HTML radio button input element.
	 *
	 * <code>
	 *		// Create a radio button element
	 *		echo Form::radio('apple');
	 *
	 *		// Create a radio button element that is selected by default
	 *		echo Form::radio('microsoft', 'pc', true);
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	public static function radio($name, $value = null, $checked = false, $attributes = array())
	{
		if (is_null($value)) $value = $name;

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
	protected static function checkable($type, $name, $value, $checked, $attributes)
	{
		$attributes = array_merge($attributes, array('id' => static::id($name, $attributes), 'checked' => ($checked) ? 'checked' : null));

		return static::input($type, $name, $value, $attributes);
	}

	/**
	 * Create a HTML submit input element.
	 *
	 * <code>
	 *		// Create a submit input element
	 *		echo Form::submit('Login!');
	 *
	 *		// Create a submit input element with attributes
	 *		echo Form::submit('Login!', array('class' => 'login'));
	 * </code>
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
	 * <code>
	 *		// Create an image input element
	 *		echo Form::image('img/login.jpg');
	 * </code>
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
	 * <code>
	 *		// Create a button input element
	 *		echo Form::button('Login!');
	 *
	 *		// Create a button input element with attributes
	 *		echo Form::button('Login!', array('class' => 'login'));
	 * </code>
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
	protected static function id($name, $attributes)
	{
		if (array_key_exists('id', $attributes)) return $attributes['id'];

		if (in_array($name, static::$labels)) return $name;
	}

}