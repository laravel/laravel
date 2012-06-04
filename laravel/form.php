<?php namespace Laravel;

class Form {

	/**
	 * All of the label names that have been created.
	 *
	 * @var array
	 */
	public static $labels = array();

	/**
	 * The registered custom macros.
	 *
	 * @var array
	 */
	public static $macros = array();

	/**
	 * Registers a custom macro.
	 *
	 * @param  string   $name
	 * @param  Closure  $input
	 * @return void
	 */
	public static function macro($name, $macro)
	{
		static::$macros[$name] = $macro;
	}

	/**
	 * Open a HTML form.
	 *
	 * <code>
	 *		// Open a "POST" form to the current request URI
	 *		echo Form::open();
	 *
	 *		// Open a "POST" form to a given URI
	 *		echo Form::open('user/profile');
	 *
	 *		// Open a "PUT" form to a given URI
	 *		echo Form::open('user/profile', 'put');
	 *
	 *		// Open a form that has HTML attributes
	 *		echo Form::open('user/profile', 'post', array('class' => 'profile'));
	 * </code>
	 *
	 * @param  string   $action
	 * @param  string   $method
	 * @param  array    $attributes
	 * @param  bool     $https
	 * @return string
	 */
	public static function open($action = null, $method = 'POST', $attributes = array(), $https = null)
	{
		$method = strtoupper($method);

		$attributes['method'] =  static::method($method);

		$attributes['action'] = static::action($action, $https);

		// If a character encoding has not been specified in the attributes, we will
		// use the default encoding as specified in the application configuration
		// file for the "accept-charset" attribute.
		if ( ! array_key_exists('accept-charset', $attributes))
		{
			$attributes['accept-charset'] = Config::get('application.encoding');
		}

		$append = '';

		// Since PUT and DELETE methods are not actually supported by HTML forms,
		// we'll create a hidden input element that contains the request method
		// and set the actual request method variable to POST.
		if ($method == 'PUT' or $method == 'DELETE')
		{
			$append = static::hidden(Request::spoofer, $method);
		}

		return '<form'.HTML::attributes($attributes).'>'.$append;
	}

	/**
	 * Determine the appropriate request method to use for a form.
	 *
	 * @param  string  $method
	 * @return string
	 */
	protected static function method($method)
	{
		return ($method !== 'GET') ? 'POST' : $method;
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
		$uri = (is_null($action)) ? URI::current() : $action;

		return HTML::entities(URL::to($uri, $https));
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
	public static function open_for_files($action = null, $method = 'POST', $attributes = array(), $https = null)
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
		return static::input('hidden', Session::csrf_token, Session::token());
	}

	/**
	 * Create a HTML label element.
	 *
	 * <code>
	 *		// Create a label for the "email" input element
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

		$attributes = HTML::attributes($attributes);

		$value = HTML::entities($value);

		return '<label for="'.$name.'"'.$attributes.'>'.$value.'</label>';
	}

	/**
	 * Create a HTML input element.
	 *
	 * <code>
	 *		// Create a "text" input element named "email"
	 *		echo Form::input('text', 'email');
	 *
	 *		// Create an input element with a specified default value
	 *		echo Form::input('text', 'email', 'example@gmail.com');
	 * </code>
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function input($type, $name, $value = null, $attributes = array())
	{
		$name = (isset($attributes['name'])) ? $attributes['name'] : $name;

		$id = static::id($name, $attributes);

		$attributes = array_merge($attributes, compact('type', 'name', 'value', 'id'));

		return '<input'.HTML::attributes($attributes).'>';
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
		$attributes['name'] = $name;

		$attributes['id'] = static::id($name, $attributes);

		if ( ! isset($attributes['rows'])) $attributes['rows'] = 10;

		if ( ! isset($attributes['cols'])) $attributes['cols'] = 50;

		return '<textarea'.HTML::attributes($attributes).'>'.HTML::entities($value).'</textarea>';
	}

	/**
	 * Create a HTML select element.
	 *
	 * <code>
	 *		// Create a HTML select element filled with options
	 *		echo Form::select('sizes', array('S' => 'Small', 'L' => 'Large'));
	 *
	 *		// Create a select element with a default selected value
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
		$attributes['id'] = static::id($name, $attributes);

		$attributes['name'] = $name;

		$html = array();

		foreach ($options as $value => $display)
		{
			if (is_array($display))
			{
				$html[] = static::optgroup($display, $value, $selected);
			}
			else
			{
				$html[] = static::option($value, $display, $selected);
			}
		}

		return '<select'.HTML::attributes($attributes).'>'.implode('', $html).'</select>';
	}

	/**
	 * Create a HTML select element optgroup.
	 *
	 * @param  array   $options
	 * @param  string  $label
	 * @param  string  $selected
	 * @return string
	 */
	protected static function optgroup($options, $label, $selected)
	{
		$html = array();

		foreach ($options as $value => $display)
		{
			$html[] = static::option($value, $display, $selected);
		}

		return '<optgroup label="'.HTML::entities($label).'">'.implode('', $html).'</option>';
	}

	/**
	 * Create a HTML select element option.
	 *
	 * @param  string  $value
	 * @param  string  $display
	 * @param  string  $selected
	 * @return string
	 */
	protected static function option($value, $display, $selected)
	{
		if (is_array($selected))
		{
			$selected = (in_array($value, $selected)) ? 'selected' : null;
		}
		else
		{
			$selected = ((string) $value == (string) $selected) ? 'selected' : null;
		}

		$attributes = array('value' => HTML::entities($value), 'selected' => $selected);

		return '<option'.HTML::attributes($attributes).'>'.HTML::entities($display).'</option>';
	}

	/**
	 * Create a HTML checkbox input element.
	 *
	 * <code>
	 *		// Create a checkbox element
	 *		echo Form::checkbox('terms', 'yes');
	 *
	 *		// Create a checkbox that is selected by default
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
	 *		echo Form::radio('drinks', 'Milk');
	 *
	 *		// Create a radio button that is selected by default
	 *		echo Form::radio('drinks', 'Milk', true);
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
		if ($checked) $attributes['checked'] = 'checked';

		$attributes['id'] = static::id($name, $attributes);

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
	 * <code>
	 *		// Create an image input element
	 *		echo Form::image('img/submit.png');
	 * </code>
	 *
	 * @param  string  $url
	 * @param  string  $name
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
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function button($value, $attributes = array())
	{
		return '<button'.HTML::attributes($attributes).'>'.HTML::entities($value).'</button>';
	}

	/**
	 * Determine the ID attribute for a form element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return mixed
	 */
	protected static function id($name, $attributes)
	{
		// If an ID has been explicitly specified in the attributes, we will
		// use that ID. Otherwise, we will look for an ID in the array of
		// label names so labels and their elements have the same ID.
		if (array_key_exists('id', $attributes))
		{
			return $attributes['id'];
		}

		if (in_array($name, static::$labels))
		{
			return $name;
		}
	}

	/**
	 * Dynamically handle calls to custom macros.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		if (isset(static::$macros[$method]))
		{
			return call_user_func_array(static::$macros[$method], $parameters);
		}

		throw new \Exception("Method [$method] does not exist.");
	}

}
