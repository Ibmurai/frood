<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodModuleRouter - Routing inside a module.
 *
 * @category Frood
 * @package  Routing
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodModuleRouter extends FroodRouter {
	/** @var string The module this router is for. */
	private $_module;

	/**
	 * Construct a new module router for the given module.
	 *
	 * @param string $module The module to construct a router for.
	 */
	public function __construct($module) {
		$this->_module = $module;
	}

	/**
	 * Route a given request, modifying the request.
	 *
	 * @param FroodRequest $request The request to route.
	 */
	public function route(FroodRequest $request) {
		$exp = '/
			^
			(?:([a-z][a-z0-9_]*))?   # subModule or controller
			(?:\/([a-z][a-z0-9_]*))? # controller or action
			(?:\/([a-z][a-z0-9_]*))? # action or nothing
		/x';
		$matches = array();
		if (preg_match($exp, $request->getRequestString(), $matches)) {
			$request->setModule($this->_module);
			$moduleConfiguration = Frood::getFroodConfiguration()->getModuleConfiguration($this->_module);
			if (isset($matches[1]) && $moduleConfiguration->hasSubModule($matches[1])) {
				$request
					->setSubModule($matches[1])
					->setController(isset($matches[2]) ? $matches[2] : 'index')
					->setAction(isset($matches[3]) ? $matches[3] : 'index')
				;
			} else {
				$request
					->setSubModule('public')
					->setController(isset($matches[1]) ? $matches[1] : 'index')
					->setAction(isset($matches[2]) ? $matches[2] : 'index')
				;
			}
		}
	}

	/**
	 * Get the module this router is routing.
	 *
	 * @return string
	 */
	public function getModule() {
		return $this->_module;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return parent::__toString() . "({$this->_module})";
	}
}
