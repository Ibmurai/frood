<?php
/**
 * Run The Frood from a shell.
 *
 * Usage: php shell.php [module] [app] [controller] [action] parameters...
 * Where parameters are on the form name=value
 *
 * Example: php shell.php plaza public post new title=lol body="Omg teh lolz"
 *
 * PHP version 5
 *
 * @category   Module
 * @package    Frood
 * @subpackage Runners
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since      2011-07-07
 */

require_once dirname(__FILE__) . '/../Frood.php';
require_once dirname(__FILE__) . '/../Frood/FileParameter.php';

if ($argc < 4) {
	throw new Exception('Usage: php shell.php [module] [app] [controller] [action] parameters...');
}

$module     = $argv[1];
$app        = $argv[2];
$controller = $argv[3];
$action     = $argv[4];

$args = array();
foreach (array_slice($argv, 5) as $arg) {
	$matches = array();

	switch (true) {
		case preg_match('/([^=]+)=(.+)/', $arg, $matches):
			if (preg_match('/^array\(.*\)$/', $matches[2])) {
				if (($value = eval("return {$matches[2]};")) !== false) {
					$args[$matches[1]] = $value;
				} else {
					$args[$matches[1]] = $matches[2];
				}
			} else {
				$args[$matches[1]] = $matches[2];
			}
			break;
		default:
			throw new Exception("Bogus parameter, $arg.");
	}
}

$frood = new Frood($module, $app);

$parameters = new FroodParameters($args);

try {
	$frood->dispatch($controller, $action, $parameters);
} catch (Exception $e) {
	echo "Exception: {$e->getMessage()}\n";
}
