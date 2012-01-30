<?php namespace Laravel;

class Debug 
{
	
	/**
	 * This function provides a nicer debug output
	 * 
	 * @param mixed $var
	 * @param string $text
	 */
	public static function dump($var, $text = 'Dump')
	{
		echo '<div style="font-size: 13px;background: #EEE !important; border:1px solid #666; color: #000 !important; padding:10px;">';
		echo '<h1 style="border-bottom: 1px solid #CCC; padding: 0 0 5px 0; margin: 0 0 5px 0; font: bold 120% sans-serif;">'.$text.'</h1>';
		echo '<pre style="overflow:auto;font-size:100%;">';
	
		var_dump($var);

		echo "</pre>";
		echo "</div>";
	}
	
}
