<?php namespace Laravel;

class HTML {

	/**
	 * The encoding being used by the application.
	 *
	 * @var string
	 */
	protected $encoding;

	/**
	 * The URL generator instance.
	 *
	 * @var URL
	 */
	protected $url;

	/**
	 * Create a new HTML writer instance.
	 *
	 * @param  string  $encoding
	 * @return void
	 */
	public function __construct(URL $url, $encoding)
	{
		$this->url = $url;
		$this->encoding = $encoding;
	}

	/**
	 * Convert HTML characters to entities.
	 *
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, $this->encoding, false);
	}

	/**
	 * Generate a JavaScript reference.
	 *
	 * <code>
	 *		// Generate a link to a JavaScript file
	 *		echo HTML::script('js/jquery.js');
	 *
	 *		// Generate a link to a JavaScript file with attributes
	 *		echo HTML::script('js/jquery.js', array('defer'));
	 * </code>
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public function script($url, $attributes = array())
	{
		$url = $this->entities($this->url->to_asset($url));

		return '<script type="text/javascript" src="'.$url.'"'.$this->attributes($attributes).'></script>'.PHP_EOL;
	}

	/**
	 * Generate a CSS reference.
	 *
	 * If no media type is selected, "all" will be used.
	 *
	 * <code>
	 *		// Generate a link to a CSS file
	 *		echo HTML::style('css/common.css');
	 *
	 *		// Generate a link to a CSS file with attributes
	 *		echo HTML::style('css/common.css', array('media' => 'print'));
	 * </code>
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public function style($url, $attributes = array())
	{
		if ( ! array_key_exists('media', $attributes)) $attributes['media'] = 'all';

		$attributes = array_merge($attributes, array('rel' => 'stylesheet', 'type' => 'text/css'));

		return '<link href="'.$this->entities($this->url->to_asset($url)).'"'.$this->attributes($attributes).'>'.PHP_EOL;
	}

	/**
	 * Generate a HTML span.
	 *
	 * <code>
	 *		// Generate a HTML span element
	 *		echo HTML::span('This is inside a span element.');
	 *
	 *		// Generate a HTML span element with attributes
	 *		echo HTML::span('This is inside a span.', array('class' => 'text'));
	 * </code>
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public function span($value, $attributes = array())
	{
		return '<span'.$this->attributes($attributes).'>'.$this->entities($value).'</span>';
	}

	/**
	 * Generate a HTML link.
	 *
	 * <code>
	 *		// Generate a HTML link element
	 *		echo HTML::link('user/profile', 'User Profile');
	 *
	 *		// Generate a HTML link element with attributes
	 *		echo HTML::link('user/profile', 'User Profile', array('class' => 'profile'));
	 * </code>
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @param  bool    $asset
	 * @return string
	 */
	public function link($url, $title, $attributes = array(), $https = false, $asset = false)
	{
		$url = $this->entities($this->url->to($url, $https, $asset));

		return '<a href="'.$url.'"'.$this->attributes($attributes).'>'.$this->entities($title).'</a>';
	}

	/**
	 * Generate a HTTPS HTML link.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public function link_to_secure($url, $title, $attributes = array())
	{
		return $this->link($url, $title, $attributes, true);
	}

	/**
	 * Generate an HTML link to an asset.
	 *
	 * The application index page will not be added to asset links.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public function link_to_asset($url, $title, $attributes = array(), $https = false)
	{
		return $this->link($url, $title, $attributes, $https, true);
	}

	/**
	 * Generate an HTTPS HTML link to an asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public function link_to_secure_asset($url, $title, $attributes = array())
	{
		return $this->link_to_asset($url, $title, $attributes, true);
	}

	/**
	 * Generate an HTML link to a route.
	 *
	 * An array of parameters may be specified to fill in URI segment wildcards.
	 *
	 * <code>
	 *		// Generate a link to the "profile" route
	 *		echo HTML::link_to_route('profile', 'User Profile');
	 *
	 *		// Generate a link to a route that has wildcard segments
	 *		// Example: /user/profile/(:any)
	 *		echo HTML::link_to_route('profile', 'User Profile', array($username));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public function link_to_route($name, $title, $parameters = array(), $attributes = array(), $https = false)
	{
		return $this->link($this->url->to_route($name, $parameters, $https), $title, $attributes);
	}

	/**
	 * Generate an HTTPS HTML link to a route.
	 *
	 * @param  string  $name
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public function link_to_secure_route($name, $title, $parameters = array(), $attributes = array())
	{
		return $this->link_to_route($name, $title, $parameters, $attributes, true);
	}

	/**
	 * Generate an HTML mailto link.
	 *
	 * The E-Mail address will be obfuscated to protect it from spam bots.
	 *
	 * <code>
	 *		// Generate a HTML mailto link
	 *		echo HTML::mailto('example@gmail.com');
	 *
	 *		// Generate a HTML mailto link with a title
	 *		echo HTML::mailto('example@gmail.com', 'E-Mail Me!');
	 *
	 *		// Generate a HTML mailto link with attributes
	 *		echo HTML::mailto('example@gmail.com', 'E-Mail Me', array('class' => 'email'));
	 * </code>
	 *
	 * @param  string  $email
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public function mailto($email, $title = null, $attributes = array())
	{
		$email = $this->email($email);

		if (is_null($title)) $title = $email;

		$email = '&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$email;

		return '<a href="'.$email.'"'.$this->attributes($attributes).'>'.$this->entities($title).'</a>';
	}

	/**
	 * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
	 *
	 * @param  string  $email
	 * @return string
	 */
	public function email($email)
	{
		return str_replace('@', '&#64;', $this->obfuscate($email));
	}

	/**
	 * Generate an HTML image element.
	 *
	 * <code>
	 *		// Generate a HTML image element
	 *		echo HTML::image('img/profile.jpg');
	 *
	 *		// Generate a HTML image element with Alt text
	 *		echo HTML::image('img/profile.jpg', 'Profile Photo');
	 *
	 *		// Generate a HTML image element with attributes
	 *		echo HTML::image('img/profile.jpg', 'Profile Photo', array('class' => 'profile'));
	 * </code>
	 *
	 * @param  string  $url
	 * @param  string  $alt
	 * @param  array   $attributes
	 * @return string
	 */
	public function image($url, $alt = '', $attributes = array())
	{
		$attributes['alt'] = $alt;

		return '<img src="'.$this->entities($this->url->to_asset($url)).'"'.$this->attributes($attributes).'>';
	}

	/**
	 * Generate an ordered list of items.
	 *
	 * <code>
	 *		// Generate an ordered list of items
	 *		echo HTML::ol(array('Small', 'Medium', 'Large'));
	 *
	 *		// Generate an ordered list of items with attributes
	 *		echo HTML::ol(array('Small', 'Medium', 'Large'), array('class' => 'sizes'));
	 * </code>
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public function ol($list, $attributes = array())
	{
		return $this->list_elements('ol', $list, $attributes);
	}

	/**
	 * Generate an un-ordered list of items.
	 *
	 * <code>
	 *		// Generate an un-ordered list of items
	 *		echo HTML::ul(array('Small', 'Medium', 'Large'));
	 *
	 *		// Generate an un-ordered list of items with attributes
	 *		echo HTML::ul(array('Small', 'Medium', 'Large'), array('class' => 'sizes'));
	 * </code>
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public function ul($list, $attributes = array())
	{
		return $this->list_elements('ul', $list, $attributes);
	}

	/**
	 * Generate an ordered or un-ordered list.
	 *
	 * @param  string  $type
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	private function list_elements($type, $list, $attributes = array())
	{
		$html = '';

		foreach ($list as $key => $value)
		{
			$html .= (is_array($value)) ? $this->list_elements($type, $value) : '<li>'.$this->entities($value).'</li>';
		}

		return '<'.$type.$this->attributes($attributes).'>'.$html.'</'.$type.'>';
	}

	/**
	 * Build a list of HTML attributes from an array.
	 *
	 * <code>
	 *		// Returns: class="profile" id="picture"
	 *		echo HTML::attributes(array('class' => 'profile', 'id' => 'picture'));
	 * </code>
	 *
	 * @param  array   $attributes
	 * @return string
	 */		
	public function attributes($attributes)
	{
		$html = array();

		foreach ($attributes as $key => $value)
		{
			// Assume numeric-keyed attributes to have the same key and value.
			// Example: required="required", autofocus="autofocus", etc.
			if (is_numeric($key)) $key = $value;

			if ( ! is_null($value))
			{
				$html[] = $key.'="'.$this->entities($value).'"';
			}
		}

		return (count($html) > 0) ? ' '.implode(' ', $html) : '';
	}

	/**
	 * Obfuscate a string to prevent spam-bots from sniffing it.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function obfuscate($value)
	{
		$safe = '';

		foreach (str_split($value) as $letter)
		{
			switch (rand(1, 3))
			{
				// Convert the letter to its entity representation.
				case 1:
					$safe .= '&#'.ord($letter).';';
					break;

				// Convert the letter to a Hex character code.
				case 2:
					$safe .= '&#x'.dechex(ord($letter)).';';
					break;

				// No encoding.
				case 3:
					$safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Magic Method for handling dynamic static methods.
	 *
	 * This method primarily handles dynamic calls to create links to named routes.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'link_to_secure_') === 0)
		{
			array_unshift($parameters, substr($method, 15));

			return call_user_func_array(array($this, 'link_to_secure_route'), $parameters);
		}

		if (strpos($method, 'link_to_') === 0)
		{
			array_unshift($parameters, substr($method, 8));

			return call_user_func_array(array($this, 'link_to_route'), $parameters);
		}

		throw new \Exception("Method [$method] is not defined on the HTML class.");
	}

}