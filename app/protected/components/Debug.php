<?php
/**
 * Debug is a helper class that allows dumping variables in a human readable HTML format
 * 
 */
class Debug
{
	/**
	 * Prints or returns the content of a variable in a readable format
	 * @param object $variable [optional]
	 * @param object $return [optional]
	 * @return if $return is true, the formatted variable
	 * @static 
	 */
	public static function message($variable = null, $return = false)
	{
		$variable = str_replace(' ','&nbsp;',print_r($variable, TRUE));
		$variable = str_replace("\n\r","\n",$variable);
		$variable = str_replace("\n",'<br />',$variable);
		$variable = str_replace('[','<strong style="color:black">[',$variable);
		$variable = str_replace(']',']</strong>',$variable);
		$variable = str_replace('=>','<span style="color:green">=></span>',$variable);
		$variable = str_replace('(','<span style="color:red">(</span>',$variable);
		$variable = str_replace(')','<span style="color:red">)</span>',$variable);
		
		// format final result
		$variable = '<div style="font-family:Verdana, Arial, Helvetica, Sans Serif;font-size:0.8em; color: blue">' . $variable . '</div>';
		
		if ($return)
		{
			return $variable;
		}
		// otherwise
		echo $variable;
	}
}