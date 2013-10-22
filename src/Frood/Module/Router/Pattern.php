<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodModuleRouterPattern - Routing inside a module... With patterns!
 *
 * @category Frood
 * @package  Routing
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
abstract class FroodModuleRouterPattern extends FroodModuleRouterRegex {
	/**
	 * This returns the regex patterns to use for routing.
	 *
	 * @return array[] The patterns to use for routing.
	 */
	protected function _getRouteRegexes() {
		$regexes = array();

		foreach ($this->_getRoutePatterns() as $pattern => $route) {
			$matches = array();
			preg_match_all('/\{([a-z]+[a-z0-9_]*)\}/', $pattern, $matches);

			$parameters = array();
			if (isset($matches[1])) {
				foreach ($matches[1] as $index => $parameterName) {
					$parameters[$parameterName] = '$' . ($index + 1);
				}

				foreach ($matches[0] as $replaceMe) {
					$pattern = preg_replace('/' . preg_quote($replaceMe, '/') . '/', '(.*)', $pattern);
				}
			}

			if (array_key_exists('module', $route)) {
				$regexes[$pattern]['module'] = $route['module'];
			}
			if (array_key_exists('subModule', $route)) {
				$regexes[$pattern]['subModule'] = $route['subModule'];
			}
			if (array_key_exists('controller', $route)) {
				$regexes[$pattern]['controller'] = $route['controller'];
			}
			if (array_key_exists('action', $route)) {
				$regexes[$pattern]['action'] = $route['action'];
			}

			$regexes[$pattern]['parameters'] = $parameters;
		}

		return $regexes;
	}

	/**
	 * This should return the patterns to use for routing.
	 *
	 * Example:
	 * array(
	 *     'lol/{name}' => array(
	 *         'module'     => 'Lolmodule', // Optional
	 *         'subModule'  => 'public',
	 *         'controller' => 'index',
	 *         'action'     => 'index',
	 *     ),
	 * )
	 *
	 * @return array[] The patterns to use for routing.
	 */
	abstract protected function _getRoutePatterns();
}
