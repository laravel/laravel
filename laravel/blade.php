<?php namespace Laravel;

class Blade {

	/**
	 * Compiles the specified file containing Blade pseudo-code into valid PHP.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function compile($path)
	{
		$value = file_get_contents($path);

		return static::closings(static::openings(static::echos($value)));
	}

	/**
	 * Rewrites Blade echo statements into PHP echo statements.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function echos($value)
	{
		return preg_replace('/\{\{(.+?)\}\}/', '<?php echo $1; ?>', $value);
	}

	/**
	 * Rewrites Blade structure openings into PHP structure openings.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function openings($value)
	{
		return preg_replace('/@(if|elseif|foreach|for|while)(\s*\(.*?\))\:/', '<?php $1$2: ?>', $value);
	}

	/**
	 * Rewrites Blade structure closings into PHP structure closings.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function closings($value)
	{
		$value = preg_replace('/(\s*)@(else)(.*?)\:/', '$1<?php $2$3: ?>', $value);
		$value = preg_replace('/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/', '$1<?php $2; ?> $3', $value);

		return $value;
	}

}