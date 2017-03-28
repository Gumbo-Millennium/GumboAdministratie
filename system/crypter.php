<?php
namespace System;

class Crypter
{
	/**
	 * De encryption cipher.
	 *
	 * @var string
	 */
	public $cipher;

	/**
	 * De encryption mode.
	 *
	 * @var string
	 */
	public $mode;

	/**
	 * Maak een nieuwe Crypter instance.
	 *
	 * @param  string  $cipher
	 * @param  string  $mode
	 */
	public function __construct($cipher = 'rijndael-256', $mode = 'cbc')
	{
		$this->cipher = $cipher;
		$this->mode = $mode;
	}

	/**
	 * Maak een nieuwe Crypter instance.
	 *
	 * @param  string  $cipher
	 * @param  string  $mode
	 * @return Crypter
	 */
	public static function make($cipher = 'rijndael-256', $mode = 'cbc')
	{
		return new static($cipher, $mode);
	}

	/**
	 * Encrypt een waarde met de MCrypt library.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function encrypt($value)
	{
		$iv = mcrypt_create_iv($this->iv_size(), $this->randomizer());

		return base64_encode($iv.mcrypt_encrypt($this->cipher, $this->key(), $value, $this->mode, $iv));
	}

	/**
	 * Haal de random number bron op beschikbaar voor het OSx.
	 *
	 * @return int
	 */
	protected function randomizer()
	{
		if (defined('MCRYPT_DEV_URANDOM')) {
			return MCRYPT_DEV_URANDOM;

		} elseif (defined('MCRYPT_DEV_RANDOM')) {
			return MCRYPT_DEV_RANDOM;
		}

		return MCRYPT_RAND;
	}


	/**
	 * Decrypt een waarde met de MCrypt library.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function decrypt($value)
	{
		if (! is_string($value = base64_decode($value, true))) {
			throw new \Exception('Decryption fout. Input waarde is geen geldige base64 data.');
		}

		list($iv, $value) = array(substr($value, 0, $this->iv_size()), substr($value, $this->iv_size()));

		return rtrim(mcrypt_decrypt($this->cipher, $this->key(), $value, $this->mode, $iv), "\0");
	}

	/**
	 * Haal de applicatie key op uit het application configbestand.
	 *
	 * @return string
	 */
	private function key()
	{
		if (! is_null($key = Config::get('application.key')) and $key !== '') return $key;

		throw new \Exception("De encryption class kan niet gebruikt worden zonder een encryption key.");
	}

	/**
	 * Haal de input vector lengte op voor de cipher en mode.
	 *
	 * Verschillende vectors en modes gebruiken verschillende lengtes input vectors.
	 *
	 * @return int
	 */
	private function iv_size()
	{
		return mcrypt_get_iv_size($this->cipher, $this->mode);
	}
} 