<?php namespace Laravel; use Closure;

class Validator {

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
	 * The database connection that should be used by the validator.
	 *
	 * @var Database\Connection
	 */
	protected $db;

	/**
	 * The bundle for which the validation is being run.
	 *
	 * @var string
	 */
	protected $bundle = DEFAULT_BUNDLE;

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
	public function passes()
	{
		return $this->valid();
	}

	/**
	 * Validate the target array using the specified validation rules.
	 *
	 * @return bool
	 */
	public function fails()
	{
		return $this->invalid();
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

		$value = array_get($this->attributes, $attribute);

		// Before running the validator, we need to verify that the attribute and rule
		// combination is actually validatable. Only the "accepted" rule implies that
		// the attribute is "required", so if the attribute does not exist, the other
		// rules will not be run for the attribute.
		$validatable = $this->validatable($rule, $attribute, $value);

		if ($validatable and ! $this->{'validate_'.$rule}($attribute, $value, $parameters, $this))
		{
			$this->error($attribute, $rule, $parameters);
		}
	}

	/**
	 * Determine if an attribute is validatable.
	 *
	 * To be considered validatable, the attribute must either exist, or the rule
	 * being checked must implicitly validate "required", such as the "required"
	 * rule or the "accepted" rule.
	 *
	 * @param  string  $rule
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validatable($rule, $attribute, $value)
	{
		return $this->validate_required($attribute, $value) or $this->implicit($rule);
	}

	/**
	 * Determine if a given rule implies that the attribute is required.
	 *
	 * @param  string  $rule
	 * @return bool
	 */
	protected function implicit($rule)
	{
		return $rule == 'required' or $rule == 'accepted';
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
		$message = $this->replace($this->message($attribute, $rule), $attribute, $rule, $parameters);

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
		if (is_null($value))
		{
			return false;
		}
		elseif (is_string($value) and trim($value) === '')
		{
			return false;
		}
		elseif ( ! is_null(Input::file($attribute)) and $value['tmp_name'] == '')
		{
			return false;
		}

		return true;
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
		return $this->validate_same($attribute, $value, array($attribute.'_confirmation'));
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
		return $this->validate_required($attribute, $value) and ($value == 'yes' or $value == '1');
	}

	/**
	 * Validate that an attribute is the same as another attribute.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_same($attribute, $value, $parameters)
	{
		$other = $parameters[0];

		return isset($this->attributes[$other]) and $value == $this->attributes[$other];
	}

	/**
	 * Validate that an attribute is different from another attribute.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_different($attribute, $value, $parameters)
	{
		$other = $parameters[0];

		return isset($this->attributes[$other]) and $value != $this->attributes[$other];
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
		return $this->size($attribute, $value) == $parameters[0];
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
		$size = $this->size($attribute, $value);

		return $size >= $parameters[0] and $size <= $parameters[1];
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
		return $this->size($attribute, $value) >= $parameters[0];
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
		return $this->size($attribute, $value) <= $parameters[0];
	}

	/**
	 * Get the size of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return mixed
	 */
	protected function size($attribute, $value)
	{
	 	// This method will determine if the attribute is a number, string, or file and
	 	// return the proper size accordingly. If it is a number, then number itself is
	 	// the size; if it is a file, the size is kilobytes in the size; if it is a
	 	// string, the length is the size.
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
	 * If a database column is not specified, the attribute will be used.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_unique($attribute, $value, $parameters)
	{
		// We allow the table column to be specified just in case the column does
		// not have the same name as the attribute. It must be within the second
		// parameter position, right after the database table name.
		if (isset($parameters[1]))
		{
			$attribute = $parameters[1];
		}

		$query = $this->db()->table($parameters[0])->where($attribute, '=', $value);

		// We also allow an ID to be specified that will not be included in the
		// uniqueness check. This makes updating columns easier since it is
		// fine for the given ID to exist in the table.
		if (isset($parameters[2]))
		{
			$id = (isset($parameters[3])) ? $parameters[3] : 'id';

			$query->where($id, '<>', $parameters[2]);
		}

		return $query->count() == 0;
	}

	/**
	 * Validate the existence of an attribute value in a database table.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_exists($attribute, $value, $parameters)
	{
		if (isset($parameters[1])) $attribute = $parameters[1];

		// Grab the number of elements we are looking for. If the given value is
		// in array, we'll count all of the values in the array, otherwise we
		// can just make sure the count is greater or equal to one.
		$count = (is_array($value)) ? count($value) : 1;

		$query = $this->db()->table($parameters[0]);

		// If the given value is an array, we will check for the existence of
		// all the values in the database, otherwise we'll check for the
		// presence of the single given value in the database.
		if (is_array($value))
		{
			$query = $query->where_in($attribute, $value);
		}
		else
		{
			$query = $query->where($attribute, '=', $value);
		}

		return $query->count() >= $count;
	}

	/**
	 * Validate that an attribute is a valid IP.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_ip($attribute, $value)
	{
		return filter_var($value, FILTER_VALIDATE_IP) !== false;
	}

	/**
	 * Validate that an attribute is a valid e-mail address.
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
	 * Validate that an attribute is a valid URL.
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
		return $this->validate_mimes($attribute, $value, array('jpg', 'png', 'gif', 'bmp'));
	}

	/**
	 * Validate that an attribute contains only alphabetic characters.
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
	 * Validate that an attribute contains only alpha-numeric characters.
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
	 * Validate that an attribute contains only alpha-numeric characters, dashes, and underscores.
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
	 * Validate that an attribute passes a regular expression check.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $value
	 * @return bool
	 */
	protected function validate_match($attribute, $value, $parameters)
	{
		return preg_match($parameters[0], $value);
	}

	/**
	 * Validate the MIME type of a file upload attribute is in a set of MIME types.
	 *
	 * @param  string  $attribute
	 * @param  array   $value
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_mimes($attribute, $value, $parameters)
	{
		if ( ! is_array($value) or array_get($value, 'tmp_name', '') == '') return true;

		foreach ($parameters as $extension)
		{
			if (File::is($extension, $value['tmp_name']))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the proper error message for an attribute and rule.
	 *
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @return string
	 */
	protected function message($attribute, $rule)
	{
		$bundle = Bundle::prefix($this->bundle);

		// First we'll check for developer specified, attribute specific messages.
		// These messages take first priority. They allow the fine-grained tuning
		// of error messages for each rule.
		$custom = $attribute.'_'.$rule;

		if (array_key_exists($custom, $this->messages))
		{
			return $this->messages[$custom];
		}
		elseif (Lang::has($custom = "validation.custom.{$custom}", $this->language))
		{
			return Lang::line($custom)->get($this->language);
		}

		// Next we'll check for developer specified, rule specific error messages.
		// These allow the developer to override the error message for an entire
		// rule, regardless of the attribute being validated by that rule.
		elseif (array_key_exists($rule, $this->messages))
		{
			return $this->messages[$rule];
		}

		// If the rule being validated is a "size" rule, we will need to gather
		// the specific size message for the type of attribute being validated,
		// either a number, file, or string.
		elseif (in_array($rule, $this->size_rules))
		{
			return $this->size_message($bundle, $attribute, $rule);
		}

		// If no developer specified messages have been set, and no other special
		// messages apply to the rule, we will just pull the default validation
		// message from the validation language file.
		else
		{
			$line = "{$bundle}validation.{$rule}";

			return Lang::line($line)->get($this->language);
		}
	}

	/**
	 * Get the proper error message for an attribute and size rule.
	 *
	 * @param  string  $bundle
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @return string
	 */
	protected function size_message($bundle, $attribute, $rule)
	{
		// There are three different types of size validations. The attribute
		// may be either a number, file, or a string, so we'll check a few
		// things to figure out which one it is.
		if ($this->has_rule($attribute, $this->numeric_rules))
		{
			$line = 'numeric';
		}
		// We assume that attributes present in the $_FILES array are files,
		// which makes sense. If the attribute doesn't have numeric rules
		// and isn't as file, it's a string.
		elseif (array_key_exists($attribute, Input::file()))
		{
			$line = 'file';
		}
		else
		{
			$line = 'string';
		}

		return Lang::line("{$bundle}validation.{$rule}.{$line}")->get($this->language);	
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
	protected function replace($message, $attribute, $rule, $parameters)
	{
		$message = str_replace(':attribute', $this->attribute($attribute), $message);

		if (method_exists($this, $replacer = 'replace_'.$rule))
		{
			$message = $this->$replacer($message, $attribute, $rule, $parameters);
		}

		return $message;
	}

	/**
	 * Replace all place-holders for the between rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_between($message, $attribute, $rule, $parameters)
	{
		return str_replace(array(':min', ':max'), $parameters, $message);
	}

	/**
	 * Replace all place-holders for the size rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_size($message, $attribute, $rule, $parameters)
	{
		return str_replace(':size', $parameters[0], $message);
	}

	/**
	 * Replace all place-holders for the min rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_min($message, $attribute, $rule, $parameters)
	{
		return str_replace(':min', $parameters[0], $message);
	}

	/**
	 * Replace all place-holders for the max rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_max($message, $attribute, $rule, $parameters)
	{
		return str_replace(':max', $parameters[0], $message);
	}

	/**
	 * Replace all place-holders for the in rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_in($message, $attribute, $rule, $parameters)
	{
		return str_replace(':values', implode(', ', $parameters), $message);
	}

	/**
	 * Replace all place-holders for the not_in rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_not_in($message, $attribute, $rule, $parameters)
	{
		return str_replace(':values', implode(', ', $parameters), $message);
	}

	/**
	 * Replace all place-holders for the not_in rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_mimes($message, $attribute, $rule, $parameters)
	{
		return str_replace(':values', implode(', ', $parameters), $message);
	}

	/**
	 * Replace all place-holders for the same rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_same($message, $attribute, $rule, $parameters)
	{
		return str_replace(':other', $parameters[0], $message);
	}

	/**
	 * Replace all place-holders for the different rule.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function replace_different($message, $attribute, $rule, $parameters)
	{
		return str_replace(':other', $parameters[0], $message);
	}

	/**
	 * Get the displayable name for a given attribute.
	 *
	 * @param  string  $attribute
	 * @return string
	 */
	protected function attribute($attribute)
	{
		$bundle = Bundle::prefix($this->bundle);

		// More reader friendly versions of the attribute names may be stored
		// in the validation language file, allowing a more readable version
		// of the attribute name in the message.
		$line = "{$bundle}validation.attributes.{$attribute}";

		$display = Lang::line($line)->get($this->language);

		// If no language line has been specified for the attribute, all of
		// the underscores are removed from the attribute name and that
		// will be used as the attribtue name.
		if (is_null($display))
		{
			return str_replace('_', ' ', $attribute);
		}

		return $display;
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
		$parameters = array();

		// The format for specifying validation rules and parameters follows a 
		// {rule}:{parameters} formatting convention. For instance, the rule
		// "max:3" specifies that the value may only be 3 characters long.
		if (($colon = strpos($rule, ':')) !== false)
		{
			$parameters = str_getcsv(substr($rule, $colon + 1));
		}

		return array(is_numeric($colon) ? substr($rule, 0, $colon) : $rule, $parameters);
	}

	/**
	 * Set the bundle that the validator is running for.
	 *
	 * The bundle determines which bundle the language lines will be loaded from.
	 *
	 * @param  string     $bundle
	 * @return Validator
	 */
	public function bundle($bundle)
	{
		$this->bundle = $bundle;
		return $this;
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
	public function connection(Database\Connection $connection)
	{
		$this->db = $connection;
		return $this;
	}

	/**
	 * Get the database connection for the Validator.
	 *
	 * @return Database\Connection
	 */
	protected function db()
	{
		if ( ! is_null($this->db)) return $this->db;

		return $this->db = Database::connection();
	}

	/**
	 * Dynamically handle calls to custom registered validators.
	 */
	public function __call($method, $parameters)
	{
		// First we will slice the "validate_" prefix off of the validator since
		// custom validators aren't registered with such a prefix, then we can
		// just call the method with the given parameters.
		if (isset(static::$validators[$method = substr($method, 9)]))
		{
			return call_user_func_array(static::$validators[$method], $parameters);
		}

		throw new \Exception("Method [$method] does not exist.");
	}

}