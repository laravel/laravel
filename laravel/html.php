<?php namespace Laravel;

class HTML {

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
		return htmlentities($value, ENT_QUOTES, Config::$items['application']['encoding'], false);
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
		$url = static::entities(URL::to_asset($url));

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

		foreach ($defaults as $attribute => $default)
		{
			if ( ! array_key_exists($attribute, $attributes))
			{
				$attributes[$attribute] = $default;
			}
		}

		return '<link href="'.static::entities(URL::to_asset($url)).'"'.static::attributes($attributes).'>'.PHP_EOL;
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
	 * @param  bool    $asset
	 * @return string
	 */
	public static function link($url, $title, $attributes = array(), $https = false, $asset = false)
	{
		$url = static::entities(URL::to($url, $https, $asset));

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
	public static function link_to_secure($url, $title, $attributes = array())
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
	 * @return string
	 */
	public static function link_to_asset($url, $title, $attributes = array(), $https = false)
	{
		return static::link($url, $title, $attributes, $https, true);
	}

	/**
	 * Generate an HTTPS HTML link to an asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_secure_asset($url, $title, $attributes = array())
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
	public static function link_to_route($name, $title, $parameters = array(), $attributes = array(), $https = false)
	{
		return static::link(URL::to_route($name, $parameters, $https), $title, $attributes);
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
	public static function link_to_secure_route($name, $title, $parameters = array(), $attributes = array())
	{
		return static::link_to_route($name, $title, $parameters, $attributes, true);
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

		return '<img src="'.static::entities(URL::to_asset($url)).'"'.static::attributes($attributes).'>';
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

		foreach ($list as $key => $value)
		{
			if (is_array($value))
			{
				$html .= static::listing($type, $value);
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
	 * Numeric-keyed attributes will be assigned the same key and value to handle
	 * attributes such as "autofocus" and "required".
	 *
	 * @param  array   $attributes
	 * @return string
	 */
	public static function attributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value)
		{
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
	 *
	 * <code>
	 *		// Generate a link to the "profile" named route
	 *		echo HTML::link_to_profile('Profile');
	 *
	 *		// Generate a link to the "profile" route and add some parameters
	 *		echo HTML::link_to_profile('Profile', array('taylor'));
	 *
	 *		// Generate a link to the "profile" named route using HTTPS
	 *		echo HTML::link_to_secure_profile('Profile');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		if (strpos($method, 'link_to_secure_') === 0)
		{
			array_unshift($parameters, substr($method, 15));

			return forward_static_call_array('HTML::link_to_secure_route', $parameters);
		}

		if (strpos($method, 'link_to_') === 0)
		{
			array_unshift($parameters, substr($method, 8));

			return forward_static_call_array('HTML::link_to_route', $parameters);
		}

		throw new \Exception("Method [$method] is not defined on the HTML class.");
	}

}