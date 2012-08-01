<?php namespace Laravel;

class HTML {

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
	 * Convert HTML characters to entities.
	 *
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, Config::get('application.encoding'), false);
	}

	/**
	 * Convert entities to HTML characters.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function decode($value)
	{
		return html_entity_decode($value, ENT_QUOTES, Config::get('application.encoding'));
	}

	/**
	 * Convert HTML special characters.
	 *
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function specialchars($value)
	{
		return htmlspecialchars($value, ENT_QUOTES, Config::get('application.encoding'), false);
	}

	/**
	 * Generate a link to a JavaScript file.
	 *
	 * <code>
	 *		// Generate a link to a JavaScript file
	 *		echo HTML::script('js/jquery.js');
	 *
	 *		// Generate a link to a JavaScript file and add some attributes
	 *		echo HTML::script('js/jquery.js', array('defer'));
	 * </code>
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public static function script($url, $attributes = array())
	{
		$url = URL::to_asset($url);

		return '<script src="'.$url.'"'.static::attributes($attributes).'></script>'.PHP_EOL;
	}

	/**
	 * Generate a link to a CSS file.
	 *
	 * If no media type is selected, "all" will be used.
	 *
	 * <code>
	 *		// Generate a link to a CSS file
	 *		echo HTML::style('css/common.css');
	 *
	 *		// Generate a link to a CSS file and add some attributes
	 *		echo HTML::style('css/common.css', array('media' => 'print'));
	 * </code>
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public static function style($url, $attributes = array())
	{
		$defaults = array('media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet');

		$attributes = $attributes + $defaults;

		$url = URL::to_asset($url);

		return '<link href="'.$url.'"'.static::attributes($attributes).'>'.PHP_EOL;
	}

	/**
	 * Generate a HTML span.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function span($value, $attributes = array())
	{
		return '<span'.static::attributes($attributes).'>'.static::entities($value).'</span>';
	}

	/**
	 * Generate a HTML link.
	 *
	 * <code>
	 *		// Generate a link to a location within the application
	 *		echo HTML::link('user/profile', 'User Profile');
	 *
	 *		// Generate a link to a location outside of the application
	 *		echo HTML::link('http://google.com', 'Google');
	 * </code>
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */
	public static function link($url, $title = null, $attributes = array(), $https = null)
	{
		$url = URL::to($url, $https);

		if (is_null($title)) $title = $url;

		return '<a href="'.$url.'"'.static::attributes($attributes).'>'.static::entities($title).'</a>';
	}

	/**
	 * Generate a HTTPS HTML link.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_secure($url, $title = null, $attributes = array())
	{
		return static::link($url, $title, $attributes, true);
	}

	/**
	 * Generate an HTML link to an asset.
	 *
	 * The application index page will not be added to asset links.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */
	public static function link_to_asset($url, $title = null, $attributes = array(), $https = null)
	{
		$url = URL::to_asset($url, $https);

		return '<a href="'.$url.'"'.static::attributes($attributes).'>'.static::entities($title).'</a>';
	}

	/**
	 * Generate an HTTPS HTML link to an asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_secure_asset($url, $title = null, $attributes = array())
	{
		return static::link_to_asset($url, $title, $attributes, true);
	}

	/**
	 * Generate an HTML link to a route.
	 *
	 * An array of parameters may be specified to fill in URI segment wildcards.
	 *
	 * <code>
	 *		// Generate a link to the "profile" named route
	 *		echo HTML::link_to_route('profile', 'Profile');
	 *
	 *		// Generate a link to the "profile" route and add some parameters
	 *		echo HTML::link_to_route('profile', 'Profile', array('taylor'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_route($name, $title = null, $parameters = array(), $attributes = array())
	{
		return static::link(URL::to_route($name, $parameters), $title, $attributes);
	}

	/**
	 * Generate an HTML link to a controller action.
	 *
	 * An array of parameters may be specified to fill in URI segment wildcards.
	 *
	 * <code>
	 *		// Generate a link to the "home@index" action
	 *		echo HTML::link_to_action('home@index', 'Home');
	 *
	 *		// Generate a link to the "user@profile" route and add some parameters
	 *		echo HTML::link_to_action('user@profile', 'Profile', array('taylor'));
	 * </code>
	 *
	 * @param  string  $action
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_action($action, $title = null, $parameters = array(), $attributes = array())
	{
		return static::link(URL::to_action($action, $parameters), $title, $attributes);
	}

	/**
	 * Generate an HTML mailto link.
	 *
	 * The E-Mail address will be obfuscated to protect it from spam bots.
	 *
	 * @param  string  $email
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public static function mailto($email, $title = null, $attributes = array())
	{
		$email = static::email($email);

		if (is_null($title)) $title = $email;

		$email = '&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$email;

		return '<a href="'.$email.'"'.static::attributes($attributes).'>'.static::entities($title).'</a>';
	}

	/**
	 * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
	 *
	 * @param  string  $email
	 * @return string
	 */
	public static function email($email)
	{
		return str_replace('@', '&#64;', static::obfuscate($email));
	}

	/**
	 * Generate an HTML image element.
	 *
	 * @param  string  $url
	 * @param  string  $alt
	 * @param  array   $attributes
	 * @return string
	 */
	public static function image($url, $alt = '', $attributes = array())
	{
		$attributes['alt'] = $alt;

		return '<img src="'.URL::to_asset($url).'"'.static::attributes($attributes).'>';
	}

	/**
	 * Generate an ordered list of items.
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public static function ol($list, $attributes = array())
	{
		return static::listing('ol', $list, $attributes);
	}

	/**
	 * Generate an un-ordered list of items.
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public static function ul($list, $attributes = array())
	{
		return static::listing('ul', $list, $attributes);
	}

	/**
	 * Generate an ordered or un-ordered list.
	 *
	 * @param  string  $type
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	private static function listing($type, $list, $attributes = array())
	{
		$html = '';

		if (count($list) == 0) return $html;

		foreach ($list as $key => $value)
		{
			// If the value is an array, we will recurse the function so that we can
			// produce a nested list within the list being built. Of course, nested
			// lists may exist within nested lists, etc.
			if (is_array($value))
			{
				if (is_int($key))
				{
					$html .= static::listing($type, $value);
				}
				else
				{
					$html .= '<li>'.$key.static::listing($type, $value).'</li>';
				}
			}
			else
			{
				$html .= '<li>'.static::entities($value).'</li>';
			}
		}

		return '<'.$type.static::attributes($attributes).'>'.$html.'</'.$type.'>';
	}

	/**
	 * Build a list of HTML attributes from an array.
	 *
	 * @param  array   $attributes
	 * @return string
	 */
	public static function attributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value)
		{
			// For numeric keys, we will assume that the key and the value are the
			// same, as this will convert HTML attributes such as "required" that
			// may be specified as required="required", etc.
			if (is_numeric($key)) $key = $value;

			if ( ! is_null($value))
			{
				$html[] = $key.'="'.static::entities($value).'"';
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
	protected static function obfuscate($value)
	{
		$safe = '';

		foreach (str_split($value) as $letter)
		{
			// To properly obfuscate the value, we will randomly convert each
			// letter to its entity or hexadecimal representation, keeping a
			// bot from sniffing the randomly obfuscated letters.
			switch (rand(1, 3))
			{
				case 1:
					$safe .= '&#'.ord($letter).';';
					break;

				case 2:
					$safe .= '&#x'.dechex(ord($letter)).';';
					break;

				case 3:
					$safe .= $letter;
			}
		}

		return $safe;
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
