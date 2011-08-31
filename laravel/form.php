<?php namespace Laravel;

class Form {

	/**
	 * The request instance.
	 *
	 * @var Request
	 */
	private $request;

	/**
	 * The HTML writer instance.
	 *
	 * @var HTML
	 */
	private $html;

	/**
	 * The URL generator instance.
	 *
	 * @var URL
	 */
	private $url;

	/**
	 * All of the label names that have been created.
	 *
	 * These names are stored so that input elements can automatically be assigned
	 * an ID based on the corresponding label name.
	 *
	 * @var array
	 */
	private $labels = array();

	/**
	 * Create a new form writer instance.
	 *
	 * @param  Request  $request
	 * @param  HTML     $html
	 * @param  URL      $url
	 * @return void
	 */
	public function __construct(Request $request, HTML $html, URL $url)
	{
		$this->url = $url;
		$this->html = $html;
		$this->request = $request;
	}

	/**
	 * Open a HTML form.
	 *
	 * Note: If PUT or DELETE is specified as the form method, a hidden input field will be generated
	 *       containing the request method. PUT and DELETE are not supported by HTML forms, so the
	 *       hidden field will allow us to "spoof" PUT and DELETE requests.
	 *
	 * @param  string   $action
	 * @param  string   $method
	 * @param  array    $attributes
	 * @param  bool     $https
	 * @return string
	 */
	public function open($action = null, $method = 'POST', $attributes = array(), $https = false)
	{
		list($attributes['action'], $attributes['method']) = array($this->action($action, $https), $this->method($method));

		if ( ! array_key_exists('accept-charset', $attributes))
		{
			$attributes['accept-charset'] = Config::get('application.encoding');			
		}

		$append = ($method == 'PUT' or $method == 'DELETE') ? $this->hidden('REQUEST_METHOD', $method) : '';

		return '<form'.$this->html->attributes($attributes).'>'.$append.PHP_EOL;
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
	private function method($method)
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
	private function action($action, $https)
	{
		return $this->html->entities($this->url->to(((is_null($action)) ? $this->request->uri() : $action), $https));
	}

	/**
	 * Open a HTML form with a HTTPS action URI.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */
	public function open_secure($action = null, $method = 'POST', $attributes = array())
	{
		return $this->open($action, $method, $attributes, true);
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
	public function open_for_files($action = null, $method = 'POST', $attributes = array(), $https = false)
	{
		$attributes['enctype'] = 'multipart/form-data';

		return $this->open($action, $method, $attributes, $https);
	}

	/**
	 * Open a HTML form that accepts file uploads with a HTTPS action URI.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */	
	public function open_secure_for_files($action = null, $method = 'POST', $attributes = array())
	{
		return $this->open_for_files($action, $method, $attributes, true);
	}

	/**
	 * Close a HTML form.
	 *
	 * @return string
	 */
	public function close()
	{
		return '</form>';
	}

	/**
	 * Generate a hidden field containing the current CSRF token.
	 *
	 * @return string
	 */
	public function token()
	{
		return $this->input('hidden', 'csrf_token', $this->raw_token());
	}

	/**
	 * Get the CSRF token for the current session.
	 *
	 * @return string
	 */
	public function raw_token()
	{
		return IoC::container()->resolve('laravel.session')->get('csrf_token');
	}

	/**
	 * Create a HTML label element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public function label($name, $value, $attributes = array())
	{
		$this->labels[] = $name;

		return '<label for="'.$name.'"'.$this->html->attributes($attributes).'>'.$this->html->entities($value).'</label>'.PHP_EOL;
	}

	/**
	 * Create a HTML input element.
	 *
	 * If an ID attribute is not specified and a label has been generated matching the input
	 * element name, the label name will be used as the element ID.
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public function input($type, $name, $value = null, $attributes = array())
	{
		$id = $this->id($name, $attributes);

		return '<input'.$this->html->attributes(array_merge($attributes, compact('type', 'name', 'value', 'id'))).'>'.PHP_EOL;
	}

	/**
	 * Create a HTML text input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function text($name, $value = null, $attributes = array())
	{
		return $this->input('text', $name, $value, $attributes);
	}

	/**
	 * Create a HTML password input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */		
	public function password($name, $attributes = array())
	{
		return $this->input('password', $name, null, $attributes);
	}

	/**
	 * Create a HTML hidden input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function hidden($name, $value = null, $attributes = array())
	{
		return $this->input('hidden', $name, $value, $attributes);
	}

	/**
	 * Create a HTML search input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public function search($name, $value = null, $attributes = array())
	{
		return $this->input('search', $name, $value, $attributes);
	}

	/**
	 * Create a HTML email input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public function email($name, $value = null, $attributes = array())
	{
		return $this->input('email', $name, $value, $attributes);
	}

	/**
	 * Create a HTML telephone input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function telephone($name, $value = null, $attributes = array())
	{
		return $this->input('tel', $name, $value, $attributes);
	}

	/**
	 * Create a HTML URL input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public function url($name, $value = null, $attributes = array())
	{
		return $this->input('url', $name, $value, $attributes);
	}

	/**
	 * Create a HTML number input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */		
	public function number($name, $value = null, $attributes = array())
	{
		return $this->input('number', $name, $value, $attributes);
	}

	/**
	 * Create a HTML file input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */			
	public function file($name, $attributes = array())
	{
		return $this->input('file', $name, null, $attributes);
	}

	/**
	 * Create a HTML textarea element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function textarea($name, $value = '', $attributes = array())
	{
		$attributes = array_merge($attributes, array('id' => $this->id($name, $attributes), 'name' => $name));

		if ( ! isset($attributes['rows'])) $attributes['rows'] = 10;

		if ( ! isset($attributes['cols'])) $attributes['cols'] = 50;

		return '<textarea'.$this->html->attributes($attributes).'>'.$this->html->entities($value).'</textarea>'.PHP_EOL;
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
	public function select($name, $options = array(), $selected = null, $attributes = array())
	{
		$attributes = array_merge($attributes, array('id' => $this->id($name, $attributes), 'name' => $name));

		$html = array();

		foreach ($options as $value => $display)
		{
			$option_attributes = array('value' => $this->html->entities($value), 'selected' => ($value == $selected) ? 'selected' : null);

			$html[] = '<option'.$this->html->attributes($option_attributes).'>'.$this->html->entities($display).'</option>';
		}

		return '<select'.$this->html->attributes($attributes).'>'.implode('', $html).'</select>'.PHP_EOL;
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
	public function checkbox($name, $value = null, $checked = false, $attributes = array())
	{
		return $this->checkable('checkbox', $name, $value, $checked, $attributes);
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
	public function radio($name, $value = null, $checked = false, $attributes = array())
	{
		return $this->checkable('radio', $name, $value, $checked, $attributes);
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
	private function checkable($type, $name, $value, $checked, $attributes)
	{
		$attributes = array_merge($attributes, array('id' => $this->id($name, $attributes), 'checked' => ($checked) ? 'checked' : null));

		return $this->input($type, $name, $value, $attributes);
	}

	/**
	 * Create a HTML submit input element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function submit($value, $attributes = array())
	{
		return $this->input('submit', null, $value, $attributes);
	}

	/**
	 * Create a HTML reset input element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function reset($value, $attributes = array())
	{
		return $this->input('reset', null, $value, $attributes);
	}

	/**
	 * Create a HTML image input element.
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public function image($url, $name = null, $attributes = array())
	{
		$attributes['src'] = $this->url->to_asset($url);

		return $this->input('image', $name, null, $attributes);
	}

	/**
	 * Create a HTML button element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function button($value, $attributes = array())
	{
		return '<button'.$this->html->attributes($attributes).'>'.$this->html->entities($value).'</button>'.PHP_EOL;
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
	private function id($name, $attributes)
	{
		if (array_key_exists('id', $attributes)) return $attributes['id'];

		if (in_array($name, $this->labels)) return $name;
	}

}