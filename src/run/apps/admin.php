<?php
/**
 * Run The Frood in admin mode.
 *
 * PHP version 5
 *
 * @category   Module
 * @package    Frood
 * @subpackage Runners/Apps
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since      2011-07-05
 */

$frood = new Frood(
	basename(realpath(dirname(__FILE__) . '/../../../../')), // The name of the module.
	'admin'                                                  // The app to run.
);

try {
	$frood->dispatch();
} catch (Exception $e) {
	echo '<h1>Frood error</h1>';
	echo '<p>' . get_class($e) . ': ' . $e->getMessage() . '</p>';
	exit;
}
