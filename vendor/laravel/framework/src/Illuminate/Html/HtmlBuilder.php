<?php namespace Illuminate\Html;

use Illuminate\Routing\UrlGenerator;

class HtmlBuilder {

	/**
	 * The URL generator instance.
	 *
	 * @var \Illuminate\Routing\UrlGenerator
	 */
	protected $url;

	/**
	 * The registered html macros.
	 *
	 * @var array
	 */
	protected $macros;

	/**
	 * Create a new HTML builder instance.
	 *
	 * @param  \Illuminate\Routing\UrlGenerator  $url
	 * @return void
	 */
	public function __construct(UrlGenerator $url = null)
	{
		$this->url = $url;
	}

	/**
	 * Register a custom HTML macro.
	 *
	 * @param  string    $name
	 * @param  callable  $macro
	 * @return void
	 */
	public function macro($name, $macro)
	{
		$this->macros[$name] = $macro;
	}

	/**
	 * Convert an HTML string to entities.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}

	/**
	 * Convert entities to HTML characters.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function decode($value)
	{
		return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Generate a link to a JavaScript file.
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return string
	 */
	public function script($url, $attributes = array(), $secure = null)
	{
		$attributes['src'] = $this->url->asset($url, $secure);

		return '<script'.$this->attributes($attributes).'></script>'.PHP_EOL;
	}

	/**
	 * Generate a link to a CSS file.
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return string
	 */
	public function style($url, $attributes = array(), $secure = null)
	{
		$defaults = array('media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet');

		$attributes = $attributes + $defaults;

		$attributes['href'] = $this->url->asset($url, $secure);

		return '<link'.$this->attributes($attributes).'>'.PHP_EOL;
	}

	/**
	 * Generate an HTML image element.
	 *
	 * @param  string  $url
	 * @param  string  $alt
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return string
	 */
	public function image($url, $alt = null, $attributes = array(), $secure = null)
	{
		$attributes['alt'] = $alt;

		return '<img src="'.$this->url->asset($url, $secure).'"'.$this->attributes($attributes).'>';
	}

	/**
	 * Generate a HTML link.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return string
	 */
	public function link($url, $title = null, $attributes = array(), $secure = null)
	{
		$url = $this->url->to($url, array(), $secure);

		if (is_null($title) || $title === false) $title = $url;

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
	public function secureLink($url, $title = null, $attributes = array())
	{
		return $this->link($url, $title, $attributes, true);
	}

	/**
	 * Generate a HTML link to an asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return string
	 */
	public function linkAsset($url, $title = null, $attributes = array(), $secure = null)
	{
		$url = $this->url->asset($url, $secure);

		return $this->link($url, $title ?: $url, $attributes, $secure);
	}

	/**
	 * Generate a HTTPS HTML link to an asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public function linkSecureAsset($url, $title = null, $attributes = array())
	{
		return $this->linkAsset($url, $title, $attributes, true);
	}

	/**
	 * Generate a HTML link to a named route.
	 *
	 * @param  string  $name
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public function linkRoute($name, $title = null, $parameters = array(), $attributes = array())
	{
		return $this->link($this->url->route($name, $parameters), $title, $attributes);
	}

	/**
	 * Generate a HTML link to a controller action.
	 *
	 * @param  string  $action
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public function linkAction($action, $title = null, $parameters = array(), $attributes = array())
	{
		return $this->link($this->url->action($action, $parameters), $title, $attributes);
	}

	/**
	 * Generate a HTML link to an email address.
	 *
	 * @param  string  $email
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public function mailto($email, $title = null, $attributes = array())
	{
		$email = $this->email($email);

		$title = $title ?: $email;

		$email = $this->obfuscate('mailto:') . $email;

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
	 * Generate an ordered list of items.
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public function ol($list, $attributes = array())
	{
		return $this->listing('ol', $list, $attributes);
	}

	/**
	 * Generate an un-ordered list of items.
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public function ul($list, $attributes = array())
	{
		return $this->listing('ul', $list, $attributes);
	}

	/**
	 * Create a listing HTML element.
	 *
	 * @param  string  $type
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	protected function listing($type, $list, $attributes = array())
	{
		$html = '';

		if (count($list) == 0) return $html;

		// Essentially we will just spin through the list and build the list of the HTML
		// elements from the array. We will also handled nested lists in case that is
		// present in the array. Then we will build out the final listing elements.
		foreach ($list as $key => $value)
		{
			$html .= $this->listingElement($key, $type, $value);
		}

		$attributes = $this->attributes($attributes);

		return "<{$type}{$attributes}>{$html}</{$type}>";
	}

	/**
	 * Create the HTML for a listing element.
	 *
	 * @param  mixed    $key
	 * @param  string  $type
	 * @param  string  $value
	 * @return string
	 */
	protected function listingElement($key, $type, $value)
	{
		if (is_array($value))
		{
			return $this->nestedListing($key, $type, $value);
		}
		else
		{
			return '<li>'.e($value).'</li>';
		}
	}

	/**
	 * Create the HTML for a nested listing attribute.
	 *
	 * @param  mixed    $key
	 * @param  string  $type
	 * @param  string  $value
	 * @return string
	 */
	protected function nestedListing($key, $type, $value)
	{
		if (is_int($key))
		{
			return $this->listing($type, $value);
		}
		else
		{
			return '<li>'.$key.$this->listing($type, $value).'</li>';
		}
	}

	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @param  array  $attributes
	 * @return string
	 */
	public function attributes($attributes)
	{
		$html = array();

		// For numeric keys we will assume that the key and the value are the same
		// as this will convert HTML attributes such as "required" to a correct
		// form like required="required" instead of using incorrect numerics.
		foreach ((array) $attributes as $key => $value)
		{
			$element = $this->attributeElement($key, $value);

			if ( ! is_null($element)) $html[] = $element;
		}

		return count($html) > 0 ? ' '.implode(' ', $html) : '';
	}

	/**
	 * Build a single attribute element.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @return string
	 */
	protected function attributeElement($key, $value)
	{
		if (is_numeric($key)) $key = $value;

		if ( ! is_null($value)) return $key.'="'.e($value).'"';
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
			if (ord($letter) > 128) return $letter;

			// To properly obfuscate the value, we will randomly convert each letter to
			// its entity or hexadecimal representation, keeping a bot from sniffing
			// the randomly obfuscated letters out of the string on the responses.
			switch (rand(1, 3))
			{
				case 1:
					$safe .= '&#'.ord($letter).';'; break;

				case 2:
					$safe .= '&#x'.dechex(ord($letter)).';'; break;

				case 3:
					$safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Dynamically handle calls to the html class.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (isset($this->macros[$method]))
		{
			return call_user_func_array($this->macros[$method], $parameters);
		}

		throw new \BadMethodCallException("Method {$method} does not exist.");
	}

}
