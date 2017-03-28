<?php

class Utils
{
	/**
	 * Debug een variabele door de output te laten zien en het script daarna te stoppen.
	 *
	 * @param  mixed  $input
	 */
	public static function debug($input)
	{
		if (is_object($input) or is_array($input)) {
			echo '<pre>'.print_r($input, true).'</pre>';
		} else {
			var_dump($input);
		}

		exit;
	}

	/***
	 * Converteer een array naar een collectie geschikt voor de Form::select method.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  string  $value
	 * @param  string  $default
	 * @return array
	 */
	public static function toSelectArray($array, $key, $value, $default = null)
	{
		// De default waarde is een tekst als 'selecteer waarde...'.
		$selectArray = array();
		if (! is_null($default)) {
			$selectArray[0] = $default;
		}

		foreach ($array as $item) {
			$selectArray[$item->$key] = '';
			
			if(strpos($value, '.')){
				$values = explode('.', $value);				
				foreach ($values as $inhoud){
					$selectArray[$item->$key] .= $item->$inhoud.' ';
				}
			} else {
				$selectArray[$item->$key] = $item->$value;
			}
		}

		return $selectArray;
	}
}