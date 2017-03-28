<?php
require BASE_PATH.'vendor/autoload'.EXT;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Mailchimp
{
	/**
	 * De API key die gebruikt moet worden.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * De root van de Mailchimp API.
	 *
	 * @var string
	 */
	private $endpoint = 'http://us1.api.mailchimp.com/3.0/';

	/**
	 * De Guzzle HTTP client die de requests afhandeld.
	 * 
	 * @var Client
	 */
	private $client;

	/**
	 * De toegestane methods naar de API.
	 *
	 * @var array
	 */
	private $allowed_methods = array('get', 'head', 'put', 'post', 'patch', 'delete');

	/**
	 * Extra opties zoals headers.
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Maak een nieuw Mailchimp API wrapper object aan.
	 *
	 * @param  string  $api_key
	 */
	public function __construct($api_key)
	{
		$this->api_key = $api_key;
		$this->client = new Client();

		$this->detectEndPoint();

		$this->options['headers'] = array(
			'Authorization' => 'apikey '.$this->api_key
		);
	}

	/**
	 * Maak een nieuw Mailchimp API wrapper object aan.
	 *
	 * @param  string  $api_key
	 * @return Mailchimp
	 */
	public static function make($api_key)
	{
		return new static($api_key);
	}

	/**
	 * Doe een request naar de Mailchimp API en return het resultaat.
	 *
	 * @param   string  $resource
	 * @param   array   $arguments
	 * @param   string  $method
	 * @return  array
	 */
	public function request($resource, $arguments = array(), $method = 'GET')
	{
		if (! $this->api_key) {
			throw new \Exception('Geen API key ingevuld');
		}

		return $this->makeRequest($resource, $arguments, strtolower($method));
	}

	/**
	 * Het eerste deel van de URL is variabel. (us1, us2, etc). In de API key
	 * staat echter de waarde die hier moet staan. Detecteer dit deel en
	 * update de endpoint met de echte waarde.
	 */
	public function detectEndPoint()
	{
		if (! strstr($this->api_key, '-')) {
			throw new InvalidArgumentException('API Key is niet goed gevormd. Ga naar Mailchimp.');
		}

		list(, $dc) = explode('-', $this->api_key);

		$this->endpoint = str_replace('us1', $dc, $this->endpoint);
	}

	/**
	 * Stel een nieuwe API key in.
	 *
	 * @param  string  $api_key
	 */
	public function setApiKey($api_key)
	{
		$this->api_key = $api_key;

		$this->detectEndPoint();
	}

	/**
	 * Bouw en voer een request uit naar de Mailchimp API en return het resultaat.
	 *
	 * @param  string  $resource
	 * @param  array   $arguments
	 * @param  string  $method
	 * @return array
	 */
	private function makeRequest($resource, $arguments, $method)
	{
		try {
			$options = $this->getOptions($method, $arguments);
			$response = $this->client->{$method}($this->endpoint.$resource, $options);

			return json_decode($response->getBody());
		} catch (ClientException $e) {
			throw new Exception($e->getResponse()->getBody());

		} catch (RequestException $e) {
			$response = $e->getResponse();

			if ($response instanceof ResponseInterface) {
				throw new Exception($e->getResponse()->getBody());
			}

			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Zorg dat de extra HTTP opties goed zijn ingesteld.
	 *
	 * @param  string  $method
	 * @param  array   $arguments
	 * @return array
	 */
	private function getOptions($method, $arguments)
	{
		if (count($arguments) < 1) {
			return $this->options;
		}

		if ($method == 'get') {
			$this->options['query'] = $arguments;
		} else {
			$this->options['json'] = $arguments;
		}

		return $this->options;
	}

	/**
	 * Magic Method om API calls met bepaalde request methods af te handelen.
	 */
	public function __call($method, $parameters)
	{
		if (count($parameters) < 1) {
			throw new InvalidArgumentException("Magic Method requests hebben een URI en optioneel een opties array nodig.");
		}

		if (! in_array($method, $this->allowed_methods)) {
			throw new BadMethodCallException("Method [$method] wordt niet ondersteund.");
		}

		$resource = $parameters[0];
		$options = isset($parameters[1]) ? $parameters[1] : array();

		return $this->request($resource, $options, $method);
	}
}