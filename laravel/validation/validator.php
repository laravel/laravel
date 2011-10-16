<?php namespace Laravel\Validation;

use Closure;
use Laravel\Arr;
use Laravel\IoC;
use Laravel\Str;
use Laravel\Lang;
use Laravel\Input;
use Laravel\Database\Manager as DB;

class Validator {

	/**
	 * The database connection that should be used by the validator.
	 *
	 * @var Database\Connection
	 */
	public $connection;

	/**
	 * The array being validated.
	 *
	 * @var array
	 */
	public $attributes;

	/**
	 * The post-validation error messages.
	 *
	 * @var Messages
	 */
	public $errors;

	/**
	 * The validation rules.
	 *
	 * @var array
	 */
	protected $rules = array();

	/**
	 * The validation messages.
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * The language that should be used when retrieving error messages.
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * The size related validation rules.
	 *
	 * @var array
	 */
	protected $size_rules = array('size', 'between', 'min', 'max');

	/**
	 * The numeric related validation rules.
	 *
	 * @var array
	 */
	protected $numeric_rules = array('numeric', 'integer');

	/**
	 * The registered custom validators.
	 *
	 * @var array
	 */
	protected static $validators = array();

	/**
	 * Create a new validator instance.
	 *
	 * @param  array  $attributes
	 * @param  array  $rules
	 * @param  array  $messages
	 * @return void
	 */
	public function __construct($attributes, $rules, $messages = array())
	{
		foreach ($rules as $key => &$rule)
		{
			$rule = (is_string($rule)) ? explode('|', $rule) : $rule;
		}

		$this->rules = $rules;
		$this->messages = $messages;
		$this->attributes = $attributes;
	}

	/**
	 * Create a new validator instance.
	 *
	 * @param  array      $attributes
	 * @param  array      $rules
	 * @param  array      $messages
	 * @return Validator
	 */
	public static function make($attributes, $rules, $messages = array())
	{
		return new static($attributes, $rules, $messages);
	}

	/**
	 * Register a custom validator.
	 *
	 * @param  string   $name
	 * @param  Closure  $validator
	 * @return void
	 */
	public static function register($name, $validator)
	{
		static::$validators[$name] = $validator;
	}

	/**
	 * Validate the target array using the specified validation rules.
	 *
	 * @return bool
	 */
	public function invalid()
	{
		return ! $this->valid();
	}

	/**
	 * Validate the target array using the specified validation rules.
	 *
	 * @return bool
	 */
	public function valid()
	{
		$this->errors = new Messages;

		foreach ($this->rules as $attribute => $rules)
		{
			foreach ($rules as $rule) $this->check($attribute, $rule);
		}

		return count($this->errors->messages) == 0;
	}

	/**
	 * Evaluate an attribute against a validation rule.
	 *
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @return void
	 */
	protected function check($attribute, $rule)
	{
		list($rule, $parameters) = $this->parse($rule);

		// Verify that the attribute and rule combination is actually validatable before
		// attempting to call the validation rule. Unless the rule implicitly requires
		// the attribute to exist, we will not call any rules for attributes that are
		// not in the validator's attribute array.
		if ( ! $this->validatable($rule, $attribute, $value = Arr::get($this->attributes, $attribute))) return;

		if ( ! $this->{'validate_'.$rule}($attribute, $value, $parameters, $this))
		{
			$this->error($attribute, $rule, $parameters);
		}
	}

	/**
	 * Determine if an attribute is validatable.
	 *
	 * To be considered validatable, the attribute must either exist, or the rule being
	 * checked must implicitly validate "required", such as the "required" rule or the
	 * "accepted" rule. No other rules have implicit "required" validation.
	 *
	 * @param  string  $rule
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validatable($rule, $attribute, $value)
	{
		return ($this->validate_required($attribute, $value) or in_array($rule, array('required', 'accepted')));
	}

	/**
	 * Add an error message to the validator's collection of messages.
	 *
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return void
	 */
	protected function error($attribute, $rule, $parameters)
	{
		$message = $this->format($this->message($attribute, $rule), $attribute, $rule, $parameters);

		$this->errors->add($attribute, $message);
	}

	/**
	 * Validate that a required attribute exists in the attributes array.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_required($attribute, $value)
	{
		return ! (is_null($value) or (is_string($value) and trim($value) === ''));
	}

	/**
	 * Validate that an attribute has a matching confirmation attribute.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_confirmed($attribute, $value)
	{
		$confirmation = $this->attributes[$attribute.'_confirmation'];

		return array_key_exists($attribute.'_confirmation', $this->attributes) and $value == $confirmation;
	}

	/**
	 * Validate that an attribute was "accepted".
	 *
	 * This validation rule implies the attribute is "required".
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_accepted($attribute, $value)
	{
		return $this->validate_required($attribute) and ($value == 'yes' or $value == '1');
	}

	/**
	 * Validate that an attribute is numeric.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_numeric($attribute, $value)
	{
		return is_numeric($value);
	}

	/**
	 * Validate that an attribute is an integer.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_integer($attribute, $value)
	{
		return filter_var($value, FILTER_VALIDATE_INT) !== false;
	}

	/**
	 * Validate the size of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_size($attribute, $value, $parameters)
	{
		return $this->get_size($attribute, $value) == $parameters[0];
	}

	/**
	 * Validate the size of an attribute is between a set of values.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_between($attribute, $value, $parameters)
	{
		return $this->get_size($attribute, $value) >= $parameters[0] and $this->get_size($attribute, $value) <= $parameters[1];
	}

	/**
	 * Validate the size of an attribute is greater than a minimum value.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_min($attribute, $value, $parameters)
	{
		return $this->get_size($attribute, $value) >= $parameters[0];
	}

	/**
	 * Validate the size of an attribute is less than a maximum value.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_max($attribute, $value, $parameters)
	{
		return $this->get_size($attribute, $value) <= $parameters[0];
	}

	/**
	 * Get the size of an attribute.
	 *
	 * This method will determine if the attribute is a number, string, or file and
	 * return the proper size accordingly. If it is a number, then number itself is
	 * the size; if it is a file, the size is kilobytes in the size; if it is a
	 * string, the length is the size.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return mixed
	 */
	protected function get_size($attribute, $value)
	{
		if (is_numeric($value) and $this->has_rule($attribute, $this->numeric_rules))
		{
			return $this->attributes[$attribute];
		}
		elseif (array_key_exists($attribute, Input::file()))
		{
			return $value['size'] / 1024;
		}
		else
		{
			return Str::length(trim($value));
		}
	}

	/**
	 * Validate an attribute is contained within a list of values.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_in($attribute, $value, $parameters)
	{
		return in_array($value, $parameters);
	}

	/**
	 * Validate an attribute is not contained within a list of values.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_not_in($attribute, $value, $parameters)
	{
		return ! in_array($value, $parameters);
	}

	/**
	 * Validate the uniqueness of an attribute value on a given database table.
	 *
	 * If a database column is not specified, the attribute name will be used.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_unique($attribute, $value, $parameters)
	{
		if ( ! isset($parameters[1])) $parameters[1] = $attribute;

		if (is_null($this->connection)) $this->connection = DB::connection();

		return $this->connection->table($parameters[0])->where($parameters[1], '=', $value)->count() == 0;
	}

	/**
	 * Validate than an attribute is a valid e-mail address.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_email($attribute, $value)
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * Validate than an attribute is a valid URL.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_url($attribute, $value)
	{
		return filter_var($value, FILTER_VALIDATE_URL) !== false;
	}

	/**
	 * Validate that an attribute is an active URL.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_active_url($attribute, $value)
	{
		$url = str_replace(array('http://', 'https://', 'ftp://'), '', Str::lower($value));
		
		return checkdnsrr($url);
	}

	/**
	 * Validate the MIME type of a file is an image MIME type.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_image($attribute, $value)
	{
		return $this->validate_mimes($attribute, array('jpg', 'png', 'gif', 'bmp'));
	}

	/**
	 * Validate than an attribute contains only alphabetic characters.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_alpha($attribute, $value)
	{
		return preg_match('/^([a-z])+$/i', $value);
	}

	/**
	 * Validate than an attribute contains only alpha-numeric characters.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_alpha_num($attribute, $value)
	{
		return preg_match('/^([a-z0-9])+$/i', $value);
	}

	/**
	 * Validate than an attribute contains only alpha-numeric characters, dashes, and underscores.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_alpha_dash($attribute, $value)
	{
		return preg_match('/^([-a-z0-9_-])+$/i', $value);	
	}

	/**
	 * Validate the MIME type of a file upload attribute is in a set of MIME types.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_mimes($attribute, $parameters)
	{
		foreach ($parameters as $extension)
		{
			if (File::is($extension, $this->attributes[$attribute]['tmp_name'])) return true;
		}

		return false;
	}

	/**
	 * Get the proper error message for an attribute and rule.
	 *
	 * Developer specified attribute specific rules take first priority.
	 * Developer specified error rules take second priority. If the error
	 * message has not been specified by the developer, the default will
	 * be used from the validation language file.
	 *
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @return string
	 */
	protected function message($attribute, $rule)
	{
		// First we'll check for developer specified, attribute specific messages. These messages
		// take first priority if they have been specified. They allow the fine-grained tuning
		// of error messages for each rule.
		if (array_key_exists($attribute.'_'.$rule, $this->messages))
		{
			return $this->messages[$attribute.'_'.$rule];
		}
		// Next we'll check for developer specified, rule specific messages. These allow the
		// developer to override the error message for an entire rule, regardless of the
		// attribute being validated by that rule.
		elseif (array_key_exists($rule, $this->messages))
		{
			return $this->messages[$rule];
		}
		// If the 
		elseif (in_array($rule, $this->size_rules) and ! $this->has_rule($attribute, $this->numeric_rules))
		{
			$line = (array_key_exists($attribute, Input::file())) ? "file" : "string";

			return Lang::line("validation.{$rule}.{$line}")->get($this->language);
		}
		else
		{
			return Lang::line("validation.{$rule}")->get($this->language);
		}
	}

	/**
	 * Replace all error message place-holders with actual values.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function format($message, $attribute, $rule, $parameters)
	{
		// First we will get the language line for the attribute being validated.
		// Storing attribute names in a validation file allows the easily replacement
		// of attribute names (email) with more reader friendly versions (E-Mail).
		$display = Lang::line('validation.attributes.'.$attribute)->get($this->language, str_replace('_', ' ', $attribute));

		$message = str_replace(':attribute', $display, $message);

		// The "size" family of rules all have place-holders for the values applicable
		// to their function. For example, the "max" rule has a ":max" place-holder.
		if (in_array($rule, $this->size_rules))
		{
			$max = ($rule == 'between') ? $parameters[1] : $parameters[0];

			$message = str_replace(array(':size', ':min', ':max'), array($parameters[0], $parameters[0], $max), $message);
		}
		// The "inclusion" rules, which are rules that check if a value is within
		// a list of values, all have a place-holder to display the allowed values.
		elseif (in_array($rule, array('in', 'not_in', 'mimes')))
		{
			$message = str_replace(':values', implode(', ', $parameters), $message);
		}

		return $message;
	}

	/**
	 * Determine if an attribute has a rule assigned to it.
	 *
	 * @param  string  $attribute
	 * @param  array   $rules
	 * @return bool
	 */
	protected function has_rule($attribute, $rules)
	{
		foreach ($this->rules[$attribute] as $rule)
		{
			list($rule, $parameters) = $this->parse($rule);

			if (in_array($rule, $rules)) return true;
		}

		return false;
	}

	/**
	 * Extract the rule name and parameters from a rule.
	 *
	 * @param  string  $rule
	 * @return array
	 */
	protected function parse($rule)
	{
		// The format for specifying validation rules and parameters follows
		// a {rule}:{parameters} convention. For instance, "max:3" specifies
		// that the value may only be 3 characters in length.
		$parameters = (($colon = strpos($rule, ':')) !== false) ? explode(',', substr($rule, $colon + 1)) : array();

		return array(is_numeric($colon) ? substr($rule, 0, $colon) : $rule, $parameters);
	}

	/**
	 * Set the language that should be used when retrieving error messages.
	 *
	 * @param  string     $language
	 * @return Validator
	 */
	public function speaks($language)
	{
		$this->language = $language;
		return $this;
	}

	/**
	 * Set the database connection that should be used by the validator.
	 *
	 * @param  Database\Connection  $connection
	 * @return Validator
	 */
	public function connection(\Laravel\Database\Connection $connection)
	{
		$this->connection = $connection;
		return $this;
	}

	/**
	 * Dynamically handle calls to custom registered validators.
	 */
	public function __call($method, $parameters)
	{
		// First we will slice the "validate_" prefix off of the validator
		// since custom validators are not registered with such a prefix.
		// Then, if a custom validator exists, we will call it.
		if (isset(static::$validators[$method = substr($method, 9)]))
		{
			return call_user_func_array(static::$validators[$method], $parameters);
		}

		throw new \Exception("Call to undefined method [$method] on Validator instance.");
	}

}