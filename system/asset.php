<?php
namespace System;

use System\File;
use System\HTML;

class Asset
{
	/**
	 * Alle geïnstantieerde asset containers.
	 *
	 * Asset containers worden gemaakt via de container method, en zijn als singletons.
	 *
	 * @var array
	 */
	public static $containers = array();

	/**
	 * Haal een asset container instance op.
	 *
	 * Als er geen containernaam opgegeven is, wordt de default container gereturnt.
	 * Containers bieden een eenvoudige manier om assets te groeperen terwijl er een
	 * schone API onderhouden wordt.
	 *
	 * @param  string           $container
	 * @return Asset_Container
	 */
	public static function container($container = 'default')
	{
		if (! isset(static::$containers[$container])) {
			static::$containers[$container] = new Asset_Container($container);
		}

		return static::$containers[$container];
	}

	/**
	 * Magic Method voor het aanroepen van methods op de default Asset container.
	 * Dit zorgt voor een makkelijke API om met de default container te werken.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::container(), $method), $parameters);
	}
}

class Asset_Container
{
	/**
	 * De naam van de Asset container.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * ALle geregistreerde assets.
	 *
	 * @var array
	 */
	public $assets = array();

	/**
	 * Maak een nieuwe Asset container instance.
	 *
	 * @param  string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Voeg een asset toe aan de container.
	 *
	 * De extension van de asset source wordt gebruikt om het type asset te bepalen dat
	 * geregistreerd wordt (CSS of JavaScript). Als er een niet-standaard extentie gebruikt
	 * wordt, kun je de style of script methods gebruiken om assets te registreren.
	 *
	 * Je kunt ook asset dependencies registreren. Dit zal de class vertellen om alleen naar de
	 * geregistreerde assets te linken wanneer er een afhankelijkheid gelinkt wordt.
	 * Bijvoorbeeld, als je wil dat jQuery UI afhankelijk wil maken van jQuery.
	 *
	 * @param  string $name
	 * @param  string $source
	 * @param  array  $dependencies
	 * @param  array  $attributes
	 * @return mixed
	 */
	public function add($name, $source, $dependencies = array(), $attributes = array())
	{
		$type = (File::extension($source) == 'css') ? 'style' : 'script';

		return call_user_func(array($this, $type), $name, $source, $dependencies, $attributes);
	}

	/**
	 * Voeg CSS toe aan de geregistreerde assets.
	 *
	 * @param  string $name
	 * @param  string $source
	 * @param  array  $dependencies
	 * @param  array  $attributes
	 * @see    add
	 */
	public function style($name, $source, $dependencies = array(), $attributes = array())
	{
		if (! array_key_exists('media', $attributes)) {
			$attributes['media'] = 'all';
		}

		$this->register('style', $name, $source, $dependencies, $attributes);
	}

	/**
	 * Voeg JavaScript toe aan de geregistreerde assets.
	 *
	 * @param string $name
	 * @param string $source
	 * @param array  $dependencies
	 * @param array  $attributes
	 * @see   add
	 */
	public function script($name, $source, $dependencies = array(), $attributes = array())
	{
		$this->register('script', $name, $source, $dependencies, $attributes);
	}

	/**
	 * Voeg een asset toe aan de geregistreerde assets.
	 *
	 * @param  string $type
	 * @param  string $name
	 * @param  string $source
	 * @param  array  $dependencies
	 * @param  array  $attributes
	 */
	private function register($type, $name, $source, $dependencies, $attributes)
	{
		$dependencies = (array) $dependencies;

		$this->assets[$type][$name] = compact('source', 'dependencies', 'attributes');
	}

	/**
	 * Haal de links naar alle geregistreerde CSS assets op.
	 *
	 * @return string
	 */
	public function styles()
	{
		return $this->get_group('style');
	}

	/**
	 * Haal de links naar alle geregistreerde JavaScript assets op.
	 *
	 * @return string
	 */
	public function scripts()
	{
		return $this->get_group('script');
	}

	/**
	 * Haal alle geregistreerde assets op van een opgegeven groep.
	 *
	 * @param  string $group
	 * @return string
	 */
	private function get_group($group)
	{
		if (! isset($this->assets[$group]) or count ($this->assets[$group]) == 0) return '';

		$assets = '';

		foreach ($this->arrange($this->assets[$group]) as $name => $data) {
			$assets .= $this->get_asset($group, $name);
		}

		return $assets;
	}

	/**
	 * Haal de link naar een enkele geregistreerde CSS asset op.
	 *
	 * @param  string $name
	 * @return string
	 */
	public function get_style($name)
	{
		return $this->get_asset('style', $name);
	}

	/**
	 * Haal de link naar een enkele geregistreerde JavaScript asset op.
	 *
	 * @param  string $name
	 * @return string
	 */
	public function get_script($name)
	{
		return $this->get_asset('script', $name);
	}

	/**
	 * Haal een geregistreerde asset op.
	 *
	 * @param  string $group
	 * @param  string $name
	 * @return string
	 */
	private function get_asset($group, $name)
	{
		if (! isset($this->assets[$group][$name])) return '';

		$asset = $this->assets[$group][$name];

		return HTML::$group($asset['source'], $asset['attributes']);
	}

	/**
	 * Sorteer en haal assets op gebasseerd op hun dependencies.
	 *
	 * @param  array  $assets
	 * @return array
	 */
	private function arrange($assets)
	{
		list($original, $sorted) = array($assets, array());

		while (count($assets) > 0) {

			foreach ($assets as $asset => $value) {
				$this->evaluate_asset($asset, $value, $original, $sorted, $assets);
			}
		}

		return $sorted;
	}

	/**
	 * Evalueer een asset en zijn dependencies.
	 *
	 * @param  string  $asset
	 * @param  string  $value
	 * @param  array   $original
	 * @param  array   $sorted
	 * @param  array   $assets
	 */
	private function evaluate_asset($asset, $value, $original, &$sorted, &$assets)
	{
		// Als de asset geen dependencies meer heeft, kunnen we hem aan de gesorteerde lijst
		// toevoegen en uit de array van assets halen. Anders verifieren we de dependencies van
		// de asset niet en bepalen we of deze al gesorteerd zijn.
		if (count($assets[$asset]['dependencies']) == 0) {
			$sorted[$asset] = $value;
			unset($assets[$asset]);
		} else {
			foreach ($assets[$asset]['dependencies'] as $key => $dependency) {

				if (! $this->dependency_is_valid($asset, $dependency, $original, $assets)) {
					unset($assets[$asset]['dependencies'][$key]);
					continue;
				}

				// Als de dependency nog niet is toegevoegd aan de gesorteerde lijst, kunnen we hem niet
				// uit de lijst van de asset's dependencies verwijderen. We proberen het opnieuw in de
				// volgende rit door de loop.
				if (! isset($sorted[$dependency])) continue;

				unset($assets[$asset]['dependencies'][$key]);
			}
		}
	}

	/**
	 * Check of een dependency geldig is.
	 *
	 * @param  string  $asset
	 * @param  string  $dependency
	 * @param  array   $original
	 * @param  array   $assets
	 * @return bool
	 */
	private function dependency_is_valid($asset, $dependency, $original, $assets)
	{
		if (! isset($original[$dependency])) {
			return false;
		} elseif ($dependency === $asset) {
			throw new \Exception("Asset [$asset] is afhankelijk van zichzelf.");
		} elseif (isset($assets[$dependency]) and in_array($asset, $assets[$dependency]['dependencies'])) {
			throw new \Exception("Assets [$asset] en [$dependency] hebben een afhankelijkheid op elkaar.");
		}
	}
}