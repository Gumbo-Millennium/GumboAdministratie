<?php
namespace System;

class Validator
{
	/**
	 * De attributen die gevalideert worden.
	 *
	 * @var array
	 */
	public $attributes;

	/**
	 * De validatieregels.
	 *
	 * @var array
	 */
	public $rules;

	/**
	 * De validatieberichten.
	 *
	 * @var array
	 */
	public $messages;

	/**
	 * De post-validatie errorberichten.
	 *
	 * @var array
	 */
	public $errors;

	/**
	 * De taal die gebruikt moet worden als er errorberichten opgehaald worden.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * De size gerelateerde validatieregels.
	 *
	 * @var array
	 */
	protected $size_rules = array('size', 'between', 'min', 'max');

	/**
	 * De numeriek gerelateerde validatieregels.
	 *
	 * @var array
	 */
	protected $numeric_rules = array('numeric', 'integer');

	/**
	 * Maak een nieuwe Validator instance.
	 *
	 * @param  array  $attributes
	 * @param  array  $rules
	 * @param  array  $messages
	 */
	public function __construct($attributes, $rules, $messages = array())
	{
		foreach ($rules as $key => &$rule) {
			$rule = (is_string($rule)) ? explode('|', $rule) : $rule;
		}

		$this->attributes = $attributes;
		$this->messages = $messages;
		$this->rules = $rules;
	}

	/**
	 * Factory voor het maken van nieuwe validator instances.
	 *
	 * @param  array      $attributes
	 * @param  array      $rules
	 * @param  array      $messages
	 * @return Validator
	 */
	public static function make($attributes, $rules, $messages = array())
	{
		return new static($attributes, $rules, $messages);
	}

	/**
	 * Valideer de target array met de opgegeven validatieregels.
	 *
	 * @return bool
	 */
	public function invalid()
	{
		return ! $this->valid();
	}

	/**
	 * Valideer de target array met de opgegeven validatieregels.
	 *
	 * @return bool
	 */
	public function valid()
	{
		$this->errors = new Messages;

		foreach ($this->rules as $attribute => $rules) {

			foreach ($rules as $rule) {
				$this->check($attribute, $rule);
			}
		}

		return count($this->errors->messages) == 0;
	}

	/**
	 * Evalueer een attribuut tegen een validatieregel.
	 *
	 * @param  string  $attribute
	 * @param  string  $rule
	 */
	protected function check($attribute, $rule)
	{
		list($rule, $parameters) = $this->parse($rule);

		if (! method_exists($this, $validator = 'validate_'.$rule)) {
			throw new \Exception("Validation rule [$rule] doesn't exist.");
		}

		// Er wordt geen validatie uitgevoerd voor attributen die niet bestaan, tenzij de regel die gevalideerd
		// wordt "required" of "accepted" is. Geen andere regels hebben impliciete "required" checks.
		if (! static::validate_required($attribute) and ! in_array($rule, array('required', 'accepted'))) return;

		if (! $this->$validator($attribute, $parameters)) {
			$this->errors->add($attribute, $this->format_message($this->get_message($attribute, $rule), $attribute, $rule, $parameters));
		}
	}

	/**
	 * Valideer dat een required attribuut bestaat in de attribuut array.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_required($attribute)
	{
		if (! array_key_exists($attribute, $this->attributes)) return false;

		if (is_string($this->attributes[$attribute]) and trim($this->attributes[$attribute]) === '') return false;

		return true;
	}

	/**
	 * Valideer dat een attribuut een matchende bevestigingsattribuut heeft.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_confirmed($attribute)
	{
		return array_key_exists($attribute.'_confirmation', $this->attributes) and $this->attributes[$attribute] == $this->attributes[$attribute.'_confirmation'];
	}

	/**
	 * Valideer dat een attribuut "geaccepteerd" is.
	 *
	 * Deze validatieregel houdt ook in dat het attribuut "required" is.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_accepted($attribute)
	{
		return static::validate_required($attribute) and ($this->attributes[$attribute] == 'yes' or $this->attributes[$attribute] == '1');
	}

	/**
	 * Valideer dat een attribuut numeriek is.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_numeric($attribute)
	{
		return is_numeric($this->attributes[$attribute]);
	}

	/**
	 * Valideer dat een attribuut een integer is.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_integer($attribute)
	{
		return filter_var($this->attributes[$attribute], FILTER_VALIDATE_INT) !== false;
	}

	/**
	 * Valideer de grootte van een attribuut.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_size($attribute, $parameters)
	{
		return $this->get_size($attribute) == $parameters[0];
	}

	/**
	 * Valideer dat de grootte van een attribuut tussen een set van waardes in zit.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_between($attribute, $parameters)
	{
		return $this->get_size($attribute) >= $parameters[0] and $this->get_size($attribute) <= $parameters[1];
	}

	/**
	 * Valideer dat de grootte van een attribuut groter is dan een minimale waarde.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_min($attribute, $parameters)
	{
		return $this->get_size($attribute) >= $parameters[0];
	}

	/**
	 * Valideer dat de grootte van een attribuut kleiner is dan een maximale waarde.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_max($attribute, $parameters)
	{
		return $this->get_size($attribute) <= $parameters[0];
	}

	/**
	 * Haal de grootte op van een attribuut.
	 *
	 * @param  string  $attribute
	 * @return mixed
	 */
	protected function get_size($attribute)
	{
		if (is_numeric($this->attributes[$attribute]) and $this->has_rule($attribute, $this->numeric_rules)) {
			return $this->attributes[$attribute];
		}

		return (array_key_exists($attribute, $_FILES)) ? $this->attributes[$attribute]['size'] / 1024 : Str::length(trim($this->attributes[$attribute]));
	}

	/**
	 * Valideer dat een attribuut in een lijst van waardes staat.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_in($attribute, $parameters)
	{
		return in_array($this->attributes[$attribute], $parameters);
	}

	/**
	 * Valideer dat een attribuut niet in een lijst met waardes staat.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_not_in($attribute, $parameters)
	{
		return ! in_array($this->attributes[$attribute], $parameters);
	}

	/**
	 * Valideer de uniekheid van een attribuut op een opgegeven databasetabel.
	 *
	 * Als een databasekolom niet opgegeven is, zal de attribuutnaam gebruikt worden.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_unique($attribute, $parameters)
	{
		if (! isset($parameters[1])) $parameters[1] = $attribute;

		return DB::connection()->table($parameters[0])->where($parameters[1], '=', $this->attributes[$attribute])->count() == 0;
	}

	/**
	 * Valideer dat een attribuut een geldig emailadres is.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_email($attribute)
	{
		return filter_var($this->attributes[$attribute], FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * Valideer dat een attribuut een geldige URL is.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_url($attribute)
	{
		return filter_var($this->attributes[$attribute], FILTER_VALIDATE_URL) !== false;
	}

	/**
	 * Valideer dat een attribuut een actieve URL is.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_active_url($attribute)
	{
		$url = str_replace(array('http://', 'https://', 'ftp://'), '', Str::lower($this->attributes[$attribute]));

		return checkdnsrr($url);
	}

	/**
	 * Valideer dat de MIME type van een bestand een afbeeldings MIME type is.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_image($attribute)
	{
		return static::validate_mimes($attribute, array('jpg', 'png', 'gif', 'bmp'));
	}

	/**
	 * Valideer dat een attribuut alleen alfabetische karakters bevat.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_alpha($attribute)
	{
		return preg_match('/^([a-z])+$/i', $this->attributes[$attribute]);
	}

	/**
	 * Valideer dat een attribuut alleen alfa-numerieke karakters bevat.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_alpha_num($attribute)
	{
		return preg_match('/^([a-z0-9])+$/i', $this->attributes[$attribute]);
	}

	/**
	 * Valideer dat een attribuut alleen alfa-numerieke karakters, streepjes en underscores bevat.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	protected function validate_alpha_dash($attribute)
	{
		return preg_match('/^([a-z0-9_-])+$/i', $this->attributes[$attribute]);
	}

	/**
	 * Valideer dat de MIME type van een file upload in een set van MIME types staat.
	 *
	 * @param  string  $attribute
	 * @param  array   $parameters
	 * @return bool
	 */
	protected function validate_mimes($attribute, $parameters)
	{
		foreach ($parameters as $extension) {
			if (File::is($extension, $this->attributes[$attribute]['tmp_name'])) return true;
		}

		return false;
	}

	/**
	 * Haal de juiste errorbericht op voor een attribuut en regel.
	 *
	 * Developer aangegeven attribuut-specifieke regels hebben eerste prioriteit.
	 * Developer aangegeven errorregels nemen tweede prioriteit.
	 *
	 * Als het bericht niet is aangegeven door de developer, zal de default gebruikt
	 * worden uit het validatie taalbestand.
	 *
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @return string
	 */
	protected function get_message($attribute, $rule)
	{
		if (array_key_exists($attribute.'_'.$rule, $this->messages)) {
			return $this->messages[$attribute.'_'.$rule];

		} elseif (array_key_exists($rule, $this->messages)) {
			return $this->messages[$rule];

		} else {
			$message = Lang::line('validation.'.$rule)->get($this->language);

			// Voor "size" regels dat validatie strings of files zijn, moeten we de default
			// errorbericht voor het juiste type aanpassen.
			if (in_array($rule, $this->size_rules) and ! $this->has_rule($attribute, $this->numeric_rules)) {
				return (array_key_exists($attribute, $_FILES))
					? rtrim($message, '.').' '.Lang::line('validation.kilobytes')->get($this->language).'.'
					: rtrim($message, '.').' '.Lang::line('validation.characters')->get($this->language).'.';
			}

			return $message;
		}
	}

	/**
	 * Vervang alle errorbericht placeholders met echte waardes.
	 *
	 * @param  string  $message
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @param  array   $parameters
	 * @return string
	 */
	protected function format_message($message, $attribute, $rule, $parameters)
	{
		$display = Lang::line('attributes.'.$attribute)->get($this->language, str_replace('_', ' ', $attribute));

		$message = str_replace(':attribute', $display, $message);

		if (in_array($rule, $this->size_rules)) {
			$max = ($rule == 'between') ? $parameters[1] : $parameters[0];

			$message = str_replace(array(':size', ':min', ':max'), array($parameters[0], $parameters[0], $max), $message);
		} elseif (in_array($rule, array('in', 'not_in', 'mimes'))) {
			$message = str_replace(':values', implode(', ', $parameters), $message);
		}

		return $message;
	}

	/**
	 * Bepaal of een attribuut een regel toegewezen heeft.
	 *
	 * @param  string  $attribute
	 * @param  array   $rules
	 * @return bool
	 */
	protected function has_rule($attribute, $rules)
	{
		foreach ($this->rules[$attribute] as $rule) {
			list($rule, $parameters) = $this->parse($rule);

			if (in_array($rule, $rules)) return true;
		}

		return false;
	}

	/**
	 * Haal de regelnaam en parameters van een regel.
	 *
	 * @param  string  $rule
	 * @return array
	 */
	protected function parse($rule)
	{
		$parameters = (($colon = strpos($rule, ':')) !== false) ? explode(',', substr($rule, $colon + 1)) : array();

		return array(is_numeric($colon) ? substr($rule, 0, $colon) : $rule, $parameters);
	}

	/**
	 * Stel de taal in die gebruikt moet worden wanneer er errorberichten opgehaald worden.
	 *
	 * @param  string     $language
	 * @return Validator
	 */
	public function lang($language)
	{
		$this->language = $language;
		return $this;
	}
}