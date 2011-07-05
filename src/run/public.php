<?php
/**
 * Run The Frood in public mode.
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
	false                                                 // Public mode.
);

try {
	$frood->dispatch();
} catch (FroodDispatchException $e) {
	header("X-Frood-Message: {$e->getMessage()}", false, 404);
}
