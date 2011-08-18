<?php
/**
 * Run a Frood app as determined from the URI.
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

$requestUri = $_SERVER['REQUEST_URI'];
$module     = basename(realpath(dirname(__FILE__) . '/../../../'));
$regex = '/^
	(
		\/modules
		\/' . $module . '   # 1 : start of uri
		\/
	)
	([a-z]*)                # 2 : app
	(.*)                    # 3 : the rest
/x';

if (preg_match($regex, $requestUri, $matches)) {
	$appRunFile = dirname(__FILE__) . '/apps/' . $matches[2] . '.php';

	if (file_exists($appRunFile)) {
		include_once $appRunFile;
	} else {
		$appRunFile = dirname(__FILE__) . '/apps/public.php';
		if (file_exists($appRunFile)) {
			$_SERVER['REQUEST_URI'] = preg_replace($regex, '$1public/$2$3', $requestUri);
			if ($matches[3] == '') {
				if ($matches[2] == '') {
					$_SERVER['REQUEST_URI'] .= 'index';
				} else {
					$_SERVER['REQUEST_URI'] .= '/index';
				}
			}
			header("X-Frood-Uri-Rewrite: {$_SERVER['REQUEST_URI']}");
			include_once $appRunFile;
		} else {
			header("X-Frood-Error: The public app could not be bootstrapped. That really sucks.", false, 404);
		}
	}
} else {
	header("X-Frood-Error: I don't know how to handle the request URI, $requestUri.", false, 404);
}
