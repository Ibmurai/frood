<?php
/**
 * Run The Frood in either public or admin mode, as determined from the URI.
 *
 * PHP version 5
 *
 * @category   Module
 * @package    Frood
 * @subpackage Runners
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since      2011-07-07
 */

$requestUri = $_SERVER['REQUEST_URI'];
$module     = basename(realpath(dirname(__FILE__) . '/../../../'));
$regex = '/^
	\/modules
	\/' . $module . '
	(\/admin)?
	(?:\/|$)
/x';

if (preg_match($regex, $requestUri, $matches)) {
	if (isset($matches[1]) && $matches[1] == '/admin') {
		include_once dirname(__FILE__) . '/admin.php';
	} else {
		include_once dirname(__FILE__) . '/public.php';
	}
} else {
	header("X-Frood-Error: I don't know how to handle the request URI, $requestUri.", false, 404);
}
