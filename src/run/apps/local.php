<?php
/**
 * Run The Frood in local mode.
 *
 * The local app is only reachable from the server network.
 * The logic is a bit... Nasty... I'm sorry...
 *
 * PHP version 5
 *
 * @category   Module
 * @package    Frood
 * @subpackage Runners/Apps
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since      2011-08-04
 */

if (!isset($_SERVER['REMOTE_ADDR'])) {
	header("X-Frood-Message: No remote address detected.", false, 403);
	exit;
}

$matches = array();
if (preg_match('/^10\.254\.0\.([0-9]+)/', $_SERVER['REMOTE_ADDR'], $matches)) {
	if ($matches[1] <= 1) {
		header("X-Frood-Message: Local app is not available from the desktop network.", false, 403);
		exit;
	}
} else {
	if (!isset($_SERVER['X_FORWARDED_FOR'])) {
		header("X-Frood-Message: Local app is not available.", false, 403);
		exit;
	} else if (!preg_match('/(?:^|,| )10\.254\.0\.([0-9]+)$/', $_SERVER['X_FORWARDED_FOR'])) {
		header("X-Frood-Message: Local app is not available.", false, 403);
		exit;
	}
}

$frood = new Frood(
	basename(realpath(dirname(__FILE__) . '/../../../../')), // The name of the module.
	'local'
);

try {
	$frood->dispatch();
} catch (Exception $e) {
	header('X-Frood-Exception: ' . get_class($e) . ', ' . $e->getMessage(), false, 404);
	exit;
}
