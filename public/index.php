<?php
/**
 * Aurora - A minimal framework for PHP development
 *
 * @package  Aurora
 * @version  1.4.4
 * @author   Bart Willemsen
 * @link     http://www.bart-willemsen.nl
 */

// The path to the application directory.
$application = '../application';

// The path to the system directory.
$system      = '../system';

// The path to the packages directory.
$packages    = '../packages';

// The path to the modules directory.
$modules     = '../modules';

// The path to the storage directory.
$storage     = '../storage';

// The path to the public directory.
$public      = __DIR__;

// Launch Aurora.
require $system.'/aurora.php';