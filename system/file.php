<?php
namespace System;

class File
{

	/**
	 * Haal de inhoud van een bestand op.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function get($path)
	{
		return file_get_contents($path);
	}

	/**
	 * Schrijf naar een bestand.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public static function put($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX);
	}

	/**
	 * Voeg toe aan een bestand.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public static function append($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
	}

	/**
	 * Haal de extentie uit een bestandspad.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Haal de regels om een opgegeven regel in een bestand op.
	 *
	 * @param  string  $path
	 * @param  int     $line
	 * @param  int     $padding
	 * @return array
	 */
	public static function snapshot($path, $line, $padding = 5)
	{
		if (! file_exists($path)) return array();

		$file = file($path, FILE_IGNORE_NEW_LINES);

		array_unshift($file, '');

		if (($start = $line - $padding) < 0) $start = 0;

		if (($length = ($line - $start) + $padding + 1) < 0) $length = 0;

		return array_slice($file, $start, $length, true);
	}

	/**
	 * Haal de MIME type op via de extentie.
	 *
	 * @param  string  $extension
	 * @param  string  $default
	 * @return string
	 */
	public static function mime($extension, $default = 'application/octet-stream')
	{
		$mimes = Config::get('mimes');

		if (array_key_exists($extension, $mimes)) {
			return (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

		return $default;
	}

	/**
	 * Bepaal of een bestand een opgegeven type is.
	 *
	 * De fileinfo PHP extentie zal gebruikt worden om de MIME type van
	 * het bestand te bepalen. Alle bestanden in de File::$mimes array
	 * mogen als type gepassed worden.
	 */
	public static function is($extension, $path)
	{
		$mimes = Config::get('mimes');

		if (!array_key_exists($extension, $mimes)) {
			throw new \Exception("Bestandsextentie [$extension] is onbekend. Kan bestandstype niet bepalen.");
		}

		$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

		return (is_array($mimes[$extension])) ? in_array($mime, $mimes[$extension]) : $mime === $mimes[$extension];
	}

	/**
	 * Maak een response dat de download van een bestand forceert.
	 *
	 * @param  string  $path
	 * @param  string  $name
	 * @return Response
	 */
	public static function download($path, $name = null)
	{
		if (is_null($name)) {
			$name = basename($path);
		}

		$response = Response::make(file_get_contents($path));

		$response->header('Content-Description', 'File Transfer');
		$response->header('Content-Type', static::mime(static::extension($path)));
		$response->header('Content-Disposition', 'attachment; filename="'.$name.'.'.static::extension($path).'"');
		$response->header('Content-Transfer-Encoding', 'binary');
		$response->header('Expires', 0);
		$response->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		$response->header('Pragma', 'public');
		$response->header('Content-Length', filesize($path));

		return $response;
	}

	/**
	 * Sla een geüpload bestand op.
	 *
	 * @param  string  $key
	 * @param  string  $path
	 * @return bool
	 */
	public static function upload($key, $path)
	{
		if (! is_dir(pathinfo($path, PATHINFO_DIRNAME))) {
			mkdir(pathinfo($path, PATHINFO_DIRNAME), 0777, true);
		}

		return (array_key_exists($key, $_FILES)) ? move_uploaded_file($_FILES[$key]['tmp_name'], $path) : false;
	}

}
