<?php
/**
 * Run The Frood in admin mode.
 *
 * PHP version 5
 *
 * @category   Module
 * @package    Frood
 * @subpackage Runners
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since      2011-07-05
 */

require_once dirname(__FILE__) . '/../Frood.php';

$frood = new Frood(
	basename(realpath(dirname(__FILE__) . '/../../../')), // The name of the module.
	true                                                  // Admin mode.
);

try {
	$frood->dispatch();
} catch (FroodDispatchException $e) {
	echo '<h1>Frood error</h1>';
	echo $e->getMessage();
}
