<?php
namespace System;

class Messages
{
	/**
	 * Alle berichten.
	 *
	 * @var array
	 */
	public $messages;

	/**
	 * Maak een nieuwe Messages instance.
	 *
	 * @param  array  $messages
	 */
	public function __construct($messages = array())
	{
		$this->messages = $messages;
	}

	/**
	 * Voeg een bericht toe aan de collector.
	 *
	 * Kopieën van berichten worde niet toegevoegd.
	 *
	 * @param  string  $key
	 * @param  string  $message
	 */
	public function add($key, $message)
	{
		if (! isset($this->messages[$key]) or array_search($message, $this->messages[$key]) === false) {
			$this->messages[$key][] = $message;
		}
	}

	/**
	 * Bepaal of er berichten bestaat voor een key.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return $this->first($key) !== '';
	}

	/**
	 * Haal het eerste bericht op voor een key.
	 *
	 * @param  string  $key
	 * @param  string  $format
	 * @return string
	 */
	public function first($key, $format = ':message')
	{
		return (count($messages = $this->get($key, $format)) > 0) ? $messages[0] : '';
	}

	/**
	 * Haal alle berichten op voor een key.
	 *
	 * Als er geen key gespecificeerd is worden alle berichten gereturnt.
	 *
	 * @param  string  $key
	 * @param  string  $format
	 * @return array
	 */
	public function get($key = null, $format = ':message')
	{
		if (is_null($key)) return $this->all($format);

		return (array_key_exists($key, $this->messages)) ? $this->format($this->messages[$key], $format) : array();
	}

	/**
	 * Haal alle errorberichten op.
	 *
	 * @param  string  $format
	 * @return array
	 */
	public function all($format = ':message')
	{
		$all = array();

		foreach ($this->messages as $messages) {
			$all = array_merge($all, $this->format($messages, $format));
		}

		return $all;
	}

	/**
	 * Formatteer een array van berichten.
	 *
	 * @param  array   $messages
	 * @param  string  $format
	 * @return array
	 */
	private function format($messages, $format)
	{
		foreach ($messages as $key => &$message) {
			$message = str_replace(':message', $message, $format);
		}

		return $messages;
	}
} 