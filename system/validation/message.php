<?php namespace System\Validation;

use System\Str;
use System\Lang;

class Message {

	/**
	 * Get the appropriate validation message for a rule attribute.
	 *
	 * @param  Rule    $rule
	 * @param  string  $attribute
	 * @return string
	 */
	public static function get($rule, $attribute)
	{
		if ($rule instanceof Rangable_Rule)
		{
			$message = static::get_rangable_message($rule);
		}
		elseif ($rule instanceof Rules\Upload_of)
		{
			$message = static::get_upload_of_message($rule);
		}
		else
		{
			$message = static::get_message($rule);
		}

		return static::prepare($rule, $attribute, $message);
	}

	/**
	 * Get the error message for a typical validation rule.
	 *
	 * @param  Rule    $rule
	 * @return string
	 */
	private static function get_message($rule)
	{
		// ---------------------------------------------------------
		// The built-in error messages are stored in the language
		// directory and are keyed by the class name of the rule
		// they are associated with.
		// ---------------------------------------------------------
		if (is_null($rule->error))
		{
			$class = explode('\\', get_class($rule));

			$rule->error = strtolower(end($class));
		}

		return (is_null($rule->message)) ? Lang::line('validation.'.$rule->error)->get() : $rule->message;
	}

	/**
	 * Get the error message for a Rangable rule.
	 *
	 * @param  Rule    $rule
	 * @return string
	 */
	private static function get_rangable_message($rule)
	{
		// ---------------------------------------------------------
		// Rangable rules sometimes set a "presence_of" error.
		//
		// This occurs when an attribute is null and the option to
		// allow null values has not been set.
		// ---------------------------------------------------------
		if ($rule->error == 'presence_of')
		{
			return static::get_message($rule);
		}

		// ---------------------------------------------------------
		// Slice "number_" or "string_" off of the error type.
		// ---------------------------------------------------------
		$error_type = substr($rule->error, 7);

		return (is_null($rule->$error_type)) ? Lang::line('validation.'.$rule->error)->get() : $rule->$error_type;
	}

	/**
	 * Get the error message for an Upload_Of rule.
	 *
	 * @param  Rule    $rule
	 * @return string
	 */
	private static function get_upload_of_message($rule)
	{
		// ---------------------------------------------------------
		// Upload_Of rules sometimes set a "presence_of" error.
		//
		// This occurs when the uploaded file didn't exist and the
		// "not_required" method was not called.
		// ---------------------------------------------------------
		if ($rule->error == 'presence_of')
		{
			return static::get_message($rule);
		}

		// ---------------------------------------------------------
		// Slice "file_" off of the error type.
		// ---------------------------------------------------------
		$error_type = substr($rule->error, 5);

		return (is_null($rule->$error_type)) ? Lang::line('validation.'.$rule->error)->get() : $rule->$error_type;
	}

	/**
	 * Prepare an error message for display. All place-holders will be replaced
	 * with their actual values.
	 *
	 * @param  Rule    $rule
	 * @param  string  $attribute
	 * @param  string  $message
	 * @return string
	 */
	private static function prepare($rule, $attribute, $message)
	{
		// ---------------------------------------------------------
		// The rangable rule messages have three place-holders that
		// must be replaced.
		//
		// :max  = The maximum size of the attribute.
		// :min  = The minimum size of the attribute.
		// :size = The exact size the attribute must be.
		// ---------------------------------------------------------
		if ($rule instanceof Rangable_Rule)
		{
			$message = str_replace(':max', $rule->maximum, $message);
			$message = str_replace(':min', $rule->minimum, $message);
			$message = str_replace(':size', $rule->size, $message);
		}
		// ---------------------------------------------------------
		// The Upload_Of rule message have two place-holders taht
		// must be replaced.
		//
		// :max   = The maximum file size of the upload (kilobytes).
		// :types = The allowed file types for the upload.
		// ---------------------------------------------------------
		elseif ($rule instanceof Rules\Upload_Of)
		{
			$message = str_replace(':max', $rule->maximum, $message);

			if (is_array($rule->types))
			{
				$message = str_replace(':types', implode(', ', $rule->types), $message);
			}
		}

		return str_replace(':attribute', Lang::line('attributes.'.$attribute)->get(str_replace('_', ' ', $attribute)), $message);
	}

}