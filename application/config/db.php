<?php

return array(

	/*
	 * Default Database Connectie
	 *--------------------------------------------------------------------------
	 *
	 * De naam van de default database connectie.
	 *
	 * Deze connectie is de standaard voor alle databaseoperaties tenzij een
	 * andere connectie is opgegeven bij het utvoeren van de operatie.
	 */

	'default' => 'gumbo',

	/*
	 * Database Connecties
	 *--------------------------------------------------------------------------
	 *
	 * Alle databaseconnecties die gebruikt worden door je applicatie.
	 *
	 * Ondersteunde drivers: 'mysql', 'pgsql', 'sqlite'.
	 *
	 * Note: voor de SQLite driver zal het pad en de "sqlite" extentie automatisch
	 *       toegevoegd worden. Je hoeft alleen de database naam op te geven.
	 *
	 * Gebruik je een driver die niet ondersteund wordt? Je kunt nog steeds een
	 * PDO connectie opzetten. Geef gewoon een driver en DSN optie op:
	 *
	 *      'odbc' => array(
	 *          'driver'   => 'odbc'
	 *          'dsn'      => 'je-dsn',
	 *          'username' => 'username',
	 *          'password' => 'password',
	 *      )
	 *
	 * Note: Wanneer je een niet ondersteunde driver gebruikt, kunnen Storm en de query
	 *       builder niet helemaal zoals verwacht werken.
	 */

	'connections' => array(

		'gumbo' => array(
			'driver'   => 'mysql',
			'host'     => 'localhost',
			'database' => 'gumbo',
			'username' => 'root',
			'password' => '',
			'charset'  => 'utf8',
		),
	),
);