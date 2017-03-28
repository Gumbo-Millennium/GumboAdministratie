<?php
namespace System\Cache;

class File implements Driver
{
	/**
	 * Bepaal of een item bestaat in de cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return (! is_null($this->get($key)));
	}

	/**
	 * Haal een item uit de cache.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get($key)
	{
		if (! file_exists(CACHE_PATH.$key)) {
			return null;
		}

		$cache = file_get_contents(CACHE_PATH.$key);

		// De cache verlooptijd is opgeslagen als een UNIX timestamp aan het begin
		// van het cachebestand. We halen het er hier uit en checken het.
		if (time() >= substr($cache, 0, 10)) return $this->forget($key);

		return unserialize(substr($cache, 10));
	}

	/**
	 * Schrijf een item naar de cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		file_put_contents(CACHE_PATH.$key, (time() + ($minutes * 60)).serialize($value), LOCK_EX);
	}

	/**
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		@unlink(CACHE_PATH.$key);
	}
}