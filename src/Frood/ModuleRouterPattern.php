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
abstract class FroodModuleRouterPattern extends FroodModuleRouter {
	/**
	 * Route a given request, modifying the request.
	 *
	 * @param FroodRequest $request The request to route.
	 */
	public function route(FroodRequest $request) {
		if (!$this->_patternRoute($request)) {
			parent::route($request);
		}
	}

	/**
	 * Internally used function. Attempts to route the given request using the
	 * defined pattern routes.
	 *
	 * @param FroodRequest The request to route.
	 */
	private function _patternRoute(FroodRequest $request) {
		foreach ($this->_getRoutePatterns() as $pattern => $route) {
			$matches = array();
			$pattern = str_replace('/', '\/', $pattern);
			if (preg_match('/^' . $pattern . '(?:$|\?)/', $request->getRequestString(), $matches)) {
				$this->_applyPatternRoute($request, $route, $matches);
				return true;
			}
		}

		return false;
	}

	/**
	 * Routes a request using the given pattern.
	 *
	 * @param FroodRequest $request The request to route.
	 * @param array        $route   The pattern route to apply.
	 * @param array        $matches The regex matches from the pattern.
	 */
	private function _applyPatternRoute(FroodRequest $request, $route, $matches) {
		if (array_key_exists('module', $route)) {
			$request->setModule($this->_applyMatches($route['module'], $matches));
		} else {
			$request->setModule($this->getModule());
		}
		if (array_key_exists('subModule', $route)) {
			$request->setSubModule($this->_applyMatches($route['subModule'], $matches));
		}
		if (array_key_exists('controller', $route)) {
			$request->setController($this->_applyMatches($route['controller'], $matches));
		}
		if (array_key_exists('action', $route)) {
			$request->setAction($this->_applyMatches($route['action'], $matches));
		}
		if (array_key_exists('parameters', $route)) {
			foreach ($route['parameters'] as $parameter => $pattern) {
				if ($value = $this->_applyMatches($pattern, $matches)) {
					$request->getParameters()->addParameter($parameter, $value);
				}
			}
		}
	}

	/**
	 * Apply regex matches to a string containing $-numbers...
	 *
	 * @param string $string  The string to apply matches to.
	 * @param array  $matches The matches to apply.
	 *
	 * @return string
	 */
	private function _applyMatches($string, $matches) {
		$dollars = array();
		preg_match_all('/\$(\d+)/', $string, $dollars);
		foreach ($dollars[1] as $index) {
			$string = preg_replace('/\$' . $index . '($|[^0-9]+)/', (isset($matches[$index]) ? $matches[$index] : '') . '$1', $string);
		}

		return $string;
	}

	/**
	 * This should return the patterns to use for routing.
	 *
	 * @return string[] The patterns to use for routing.
	 */
	abstract protected function _getRoutePatterns();
}
