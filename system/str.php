<?php
namespace System;

class Str
{
	/**
	 * Debug een variabele door de output te laten zien en het script daarna te stoppen.
	 *
	 * @param  mixed  $input
	 */
	public static function debug($input)
	{
		if (is_object($input) || is_array($input)) {
			print '<pre>';
			print_r($input);
			print '</pre>';
		} else {
			var_dump($input);
		}
		exit;
	}
	
	/**
	 * Converteer HTML karakters en entities.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, Config::get('application.encoding'), false);
	}

	/**
	 * Converteer een string naar lowercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function lower($value)
	{
		return function_exists('mb_strtolower') ? mb_strtolower($value, Config::get('application.encoding')) : strtolower($value);
	}

	/**
	 * Converteer een string naar uppercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function upper($value)
	{
		return function_exists('mb_strtoupper') ? mb_strtoupper($value, Config::get('application.encoding')) : strtoupper($value);
	}

	/**
	 * Converteer een string naar title case (ucwords).
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function title($value)
	{
		return (function_exists('mb_convert_case')) ? mb_convert_case($value, MB_CASE_TITLE, Config::get('application.encoding')) : ucwords(strtolower($value));
	}

	/**
	 * Haal de lengte van een string op.
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function length($value)
	{
		return function_exists('mb_strlen') ? mb_strlen($value, Config::get('application.encoding')) : strlen($value);
	}

	/**
	 * Genereer een willekeurige alpha of alpha-numeric string.
	 *
	 * Ondersteunde types: 'alpha_num' en 'alpha'.
	 *
	 * @param  int     $length
	 * @param  string  $type
	 * @return string
	 */
	public static function random($length = 16, $type = 'alpha_num')
	{
		$value = '';

		$pool_length = strlen($pool = static::pool($type)) - 1;

		for ($i = 0; $i < $length; $i++) {
			$value .= $pool[mt_rand(0, $pool_length)];
		}

		return $value;
	}

	/**
	 * Converteer een string naar 7-bit ASCII.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function ascii($value)
	{
	    $foreign = Config::get('ascii');

		$value = preg_replace(array_keys($foreign), array_values($foreign), $value);

		return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
	}

	/**
	 * Haal een karakterpool op.
	 *
	 * @param  string  $type
	 * @return string
	 */
	private static function pool($type = 'alpha_num')
	{
		switch ($type) {
			case 'alpha_num':
				return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			default:
				return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
	}
	
	public static function reverseDate($in)
	{
		$exploded = explode('-', $in);
		
		//Bepalen of iets opgehaald of opgeslagen wordt. 
		if(count($exploded) == 3){
			if(checkdate($exploded[1], $exploded[1], $exploded[2])){
				return join('-', array_reverse(explode('-', $in)));
			} else {
				return NULL;
			}	
		} else {
			return NULL;
		}
		
		return join('-', array_reverse(explode('-', $in)));
	}

	public static function reverseDatetime($in)
	{
		$segments = explode(' ', $in);

		$reversed_date = implode('-', array_reverse(explode('-', $segments[0])));

		return implode(' ', array($reversed_date, $segments[1]));
	}

	/**
	 * Converteer een getal naar een geldnotatie zoals � 5,50
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function money($value)
	{
		return '&euro; '.number_format($value, 2, ',', '.');
	}
} 