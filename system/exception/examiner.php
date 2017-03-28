<?php
namespace System\Exception;

use System\File;

class Examiner
{
	/**
	 * De exception die bekeken wordt.
	 *
	 * @var \Exception
	 */
	public $exception;

	/**
	 * Voor mensen leesbare error levels en beschrijvingen.
	 *
	 * @var array
	 */
	private $levels = array(
		0                  => 'Error',
		E_ERROR            => 'Error',
		E_WARNING          => 'Warning',
		E_PARSE            => 'Parsing Error',
		E_NOTICE           => 'Notice',
		E_CORE_ERROR       => 'Core Error',
		E_CORE_WARNING     => 'Core Warning',
		E_COMPILE_ERROR    => 'Compile Error',
		E_COMPILE_WARNING  => 'Compile Warning',
		E_USER_ERROR       => 'User Error',
		E_USER_WARNING     => 'User Warning',
		E_USER_NOTICE      => 'User Notice',
		E_STRICT           => 'Runtime Notice',
	);

	/**
	 * Maak een nieuwe exception examiner instance.
	 *
	 * @param  \Exception  $e
	 */
	public function __construct($e)
	{
		$this->exception = $e;
	}

	/**
	 * Haal de voor mensen leesbare versie van de exception errorcode op.
	 *
	 * @return string
	 */
	public function severity()
	{
		if (array_key_exists($this->exception->getCode(), $this->levels)) {
			return $this->levels[$this->exception->getCode()];
		}

		return $this->exception->getCode();
	}

	/**
	 * Haal de exception errorbericht op, formatte voor gebruik door Aurora.
	 *
	 * De exception file paden worden ingekort en de bestandsnaam en regelnummer worden
	 * toegevoegd aan het exception bericht.
	 *
	 * @return string
	 */
	public function message()
	{
		$file = str_replace(array(APP_PATH, SYS_PATH), array('APP_PATH/', 'SYS_PATH/'), $this->exception->getFile());

		return rtrim($this->exception->getMessage(), '.').' in '.$file.' op regel '.$this->exception->getLine().'.';
	}

	/**
	 * Haal de code om de regel waar de exception plaatsvond op.
	 *
	 * @return array
	 */
	public function context()
	{
		return File::snapshot($this->exception->getFile(), $this->exception->getLine());
	}

	/**
	 * Magic Method om function calls naar de exception te sturen.
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->exception, $method), $parameters);
	}
}