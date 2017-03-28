<?php
namespace System\Exception;

use System\View;
use System\Config;
use System\Response;

class Handler
{
	/**
	 * De exception examiner voor de exception die afgehandeld wordt.
	 *
	 * @var Examiner
	 */
	public $exception;

	/**
	 * Maak een nieuwe exception handler instance.
	 *
	 * @param  \Exception  $e
	 */
	public function __construct($e)
	{
		$this->exception = new Examiner($e);
	}

	/**
	 * Maak een nieuwe exception handler instance.
	 *
	 * @param  \Exception  $e
	 * @return Handler
	 */
	public static function make($e)
	{
		return new static($e);
	}

	/**
	 * Handle de exception af en laat de error report zien.
	 *
	 * De exception wordt gelogd als error logging aanstaat.
	 *
	 * De outputbuffer wordt geleegd zodat niets naar de browser wordt gestuurd behalve
	 * het errorbericht. Dit voorkomt dat views die al gerenderd zijn worden weergegeven
	 * in een incomplete of foutieve staat.
	 *
	 * Nadat de exception is weergegeven, wordt de request gestopt.
	 */
	public function handle()
	{
		if (ob_get_level() > 0) ob_clean();

		if (Config::get('error.log')) $this->log();

		$this->get_response(Config::get('error.detail'))->send();

		exit(1);
	}

	/**
	 * Log de exception met de logger closure opgegeven in de error configuratie.
	 */
	private function log()
	{
		$parameters = array(
			$this->exception->severity(),
			$this->exception->message(),
			$this->exception->getTraceAsString(),
		);

		call_user_func_array(Config::get('error.logger'), $parameters);
	}

	/**
	 * Haal de error report response voor de exception op.
	 *
	 * @param  bool      $detailed
	 * @return Response
	 *
	 */
	private function get_response($detailed)
	{
		return ($detailed) ? $this->detailed_response() : Response::error('500');
	}

	/**
	 * Haal de gedetailleerde response op voor de exception.
	 *
	 * @return Response
	 */
	private function detailed_response()
	{
		$data = array(
			'severity' => $this->exception->severity(),
			'message'  => $this->exception->message(),
			'line'     => $this->exception->getLine(),
			'trace'    => $this->exception->getTraceAsString(),
			'contexts' => $this->exception->context(),
		);

		return Response::make(View::make('error.exception', $data), 500);
	}
}