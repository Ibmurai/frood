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
 * @author   Bo Thinggaard <both@fynskemedier.dk>
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
			\/([a-z][a-z0-9_]*) # 3 : controller
			\/([a-z][a-z0-9_]*) # 4 : action
		/x';
		$matches = array();
		if (preg_match($exp, $request->getRequestString(), $matches)) {
			$request
				->setModule($this->_module)
				->setSubModule('public')
				->setController($matches[1])
				->setAction($matches[2])
			;
		}
	}
}
