<?php
namespace System;

class Lang
{
	/**
	 * Alle geladen taalregels.
	 *
	 * De array is gekeyed als [$language.$file]
	 *
	 * @var array
	 */
	public static $lines = array();

	/**
	 * De key van de regel dat opgevraagd wordt.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * De placeholder vervangers.
	 *
	 * @var array
	 */
	public $replacements = array();

	/**
	 * Maak een nieuwe Lang instance.
	 *
	 * Taalregels worden opgehaald met de "dot" notatie. Dus, vragen voor de
	 * "messages.required" taalregel zal de "required" regel returnen van
	 * het "messages" taalbestand.
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 */
	public function __construct($key, $replacements = array())
	{
		$this->key = $key;
		$this->replacements = $replacements;
	}

	/**
	 * Maak een Lang instance voor een taalregel.
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @return Lang
	 */
	public static function line($key, $replacements = array())
	{
		return new static($key, $replacements);
	}

	/**
	 * Haal de taalregel op.
	 *
	 * @param  string  $language
	 * @param  mixed   $default
	 * @return string
	 */
	public function get($language = null, $default = null)
	{
		if (is_null($language)) $language = Config::get('application.language');

		list($module, $file, $line) = $this->parse($this->key, $language);

		$this->load($module, $file, $language);

		if (! isset(static::$lines[$module][$language.$file][$line])) {
			return is_callable($default) ? call_user_func($default) : $default;
		}

		$line = static::$lines[$module][$language.$file][$line];

		foreach ($this->replacements as $key => $value) {
			$line = str_replace(':'.$key, $value, $line);
		}

		return $line;
	}

	/**
	 * Parse een taal key.
	 *
	 * @param  string  $key
	 * @param  string  $language
	 * @return array
	 */
	private function parse($key, $language)
	{
		$module = (strpos($key, '::') !== false) ? substr($key, 0, strpos($key, ':')) : 'application';

		if ($module != 'application') {
			$key = substr($key, strpos($key, ':') + 2);
		}

		if (count($segments = explode('.', $key)) > 1) {
			return array($module, $segments[0], $segments[1]);
		}

		throw new \Exception("Ongeldige taalregel [$key]. Er moet een specifieke regel opgegeven worden.");
	}

	/**
	 * Laad een taalbestand.
	 *
	 * @param  string  $module
	 * @param  string  $file
	 * @param  string  $language
	 */
	private function load($module, $file, $language)
	{
		if (isset(static::$lines[$module][$language.$file])) return;

		$path = ($module === 'application') ? LANG_PATH : MODULE_PATH.$module.'/lang/';

		if (file_exists($path = $path.$language.'/'.$file.EXT)) {
			static::$lines[$module][$language.$file] = require $path;
		}
	}

	/**
	 * Haal de string inhoud op van de taalregel.
	 */
	public function __toString()
	{
		return $this->get();
	}


} 