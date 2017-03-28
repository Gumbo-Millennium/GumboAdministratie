<?php
namespace System;

class Paginator
{
	/**
	 * De resultaten voor de huidige pagina.
	 *
	 * @var array
	 */
	public $results;

	/**
	 * Het totale aantal resultaten.
	 *
	 * @var int
	 */
	public $total;

	/**
	 * De huidige pagina.
	 *
	 * @var int
	 */
	public $page;

	/**
	 * Het aantal items per pagina.
	 *
	 * @var int
	 */
	public $per_page;

	/**
	 * De laatste pagina beschikbaar voor de set resultaten.
	 *
	 * @var int
	 */
	public $last_page;

	/**
	 * De taal die gebruikt moet worden met het genereren van pagina links.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * De waardes die aan het eind van de link querystring toegevoegd moeten worden.
	 *
	 * @var array
	 */
	public $append = array();

	/**
	 * Maak een nieuwe Pagination instance.
	 *
	 * @param  array  $results
	 * @param  int    $page
	 * @param  int    $total
	 * @param  int    $per_page
	 * @param  int    $last_page
	 */
	public function __construct($results, $page, $total, $per_page, $last_page)
	{
		$this->last_page = $last_page;
		$this->per_page = $per_page;
		$this->results = $results;
		$this->total = $total;
		$this->page = $page;
	}

	/**
	 * Maak een nieuwe Paginator instance.
	 *
	 * @param  array      $results
	 * @param  int        $total
	 * @param  int        $per_page
	 * @return Paginator
	 */
	public static function make($results, $total, $per_page)
	{
		return new static($results, static::page($total, $per_page), $total, $per_page, ceil($total / $per_page));
	}

	/**
	 * Haal de huidige pagina op van de request query string.
	 *
	 * De pagina zal gevalideerd en aangepast worden als het minder dan één of groter is dan de laatste pagina.
	 * Bijvoorbeeld, als de huidige pagina geen integer of kleiner dan één is, wordt één gereturnt.
	 * Als de hudige pagina groter is dan de laatste pagina, wordt de laatste pagina gereturnt.
	 *
	 * @param  int  $total
	 * @param  int  $per_page
	 * @return int
	 */
	public static function page($total, $per_page)
	{
		$page = Input::get('page', 1);

		if (is_numeric($page) and $page > $last_page = ceil($total / $per_page)) {
			return ($last_page > 0) ? $last_page : 1;
		}

		return ($page < 1 or filter_var($page, FILTER_VALIDATE_INT) === false) ? 1 : $page;
	}

	/**
	 * Maak de HTML pagination links.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	public function links($adjacent = 3)
	{
		if ($this->last_page <= 1) return '';

		// De hard-coded "7" is om rekening te houden met alle vaste elementen in een sliding reeks.
		// Namelijk: de huidige pagina, de twee weglatingstekens, de twee startende pagina's, en de laatste twee pagina's.
		$numbers = ($this->last_page < 7 + ($adjacent * 2)) ? $this->range(1, $this->last_page) : $this->slider($adjacent);

		return '<div class="pagination">'.$this->previous().$numbers.$this->next().'</div>';
	}

	/**
	 * Bouw een sliding HTML lijst met numerieke pagina links.
	 *
	 * @param  int     $adjacent
	 * @return string
	 */
	private function slider($adjacent)
	{
		if ($this->page <= $adjacent * 2) {
			return $this->range(1, 2 + ($adjacent * 2)).$this->ending();

		} elseif ($this->page >= $this->last_page - ($adjacent * 2)) {
			return $this->beginning().$this->range($this->last_page - 2 - ($adjacent * 2), $this->last_page);
		}

		return $this->beginning().$this->range($this->page - $adjacent, $this->page + $adjacent).$this->ending();
	}

	/**
	 * Genereer de "Vorige" HTML link.
	 *
	 * @return string
	 */
	public function previous()
	{
		$text = Lang::line('pagination.previous')->get($this->language);

		if ($this->page > 1) {
			return $this->link($this->page - 1, $text, 'prev_page').' ';
		}

		return HTML::span($text, array('class' => 'disabled prev_page')).' ';
	}

	/**
	 * Genereer de "Volgende" HTML link.
	 *
	 * @return string
	 */
	public function next()
	{
		$text = Lang::line('pagination.next')->get($this->language);

		if ($this->page < $this->last_page) {
			return $this->link($this->page + 1, $text, 'next_page');
		}

		return HTML::span($text, array('class' => 'disabled next_page'));
	}

	/**
	 * Bouw de eerste twee paginalinks voor een sliding pagina reeks.
	 *
	 * @return string
	 */
	private function beginning()
	{
		return $this->range(1, 2).'<span class="dots">...</span>';
	}

	/**
	 * Bouw de laatste twee paginalinks voor een sliding pagina reeks.
	 *
	 * @return string
	 */
	private function ending()
	{
		return '<span class="dots">...</span>'.$this->range($this->last_page - 1, $this->last_page);
	}

	/**
	 * Bouw een reeks van pagina links.
	 *
	 * Voor de huidige pagina wordt een HTML span element gegenereerd in plaats van een link.
	 *
	 * @param  int     $start
	 * @param  int     $end
	 * @return string
	 */
	private function range($start, $end)
	{
		$pages = '';

		for ($i = $start; $i <= $end; $i++) {
			$pages .= ($this->page == $i) ? HTML::span($i, array('class' => 'current')).' ' : $this->link($i, $i, null).' ';
		}

		return $pages;
	}

	/**
	 * Maak een HTML paginalink.
	 *
	 * @param  int     $page
	 * @param  string  $text
	 * @param  string
	 * @return string
	 */
	private function link($page, $text, $class)
	{
		$append = '';

		foreach ($this->append as $key => $value) {
			$append .= '&'.$key.'='.$value;
		}

		return HTML::link(Request::uri().'?page='.$page.$append, $text, compact('class'), Request::is_secure());
	}

	/**
	 * Stel de taal in die gebruikt moet worden wanneer er paginalinks gegenereerd moeten worden.
	 *
	 * @param  string     $language
	 * @return Paginator
	 */
	public function lang($language)
	{
		$this->language = $language;
		return $this;
	}

	/**
	 * Stel de items in die toegevoegd moeten worden aan de link querystrings.
	 *
	 * @param  array      $values
	 * @return Paginator
	 */
	public function append($values)
	{
		$this->append = $values;
		return $this;
	}
}