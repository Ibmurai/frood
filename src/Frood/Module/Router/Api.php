<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodModuleRouterRest - Routing inside a module in a RESTful way.
 *
 * @category Frood
 * @package  Routing
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodModuleRouterApi extends FroodModuleRouter {
	/**
	 * Route a given request, modifying the request.
	 *
	 * @param FroodRequest $request The request to route.
	 */
	public function route(FroodRequest $request) {

		$exp = '/
			^
			api                      # subModule
			(?:\/([a-z][a-z0-9_]*))  # version
			(?:\/([a-z][a-z0-9_]*))  # controller
			(?:\/([a-z][a-z0-9_]*))? # action or nothing
		/x';

		$matches = array();
		if (preg_match($exp, $request->getRequestString(), $matches)) {

			$request->setModule($this->getModule());
			$moduleConfiguration = Frood::getFroodConfiguration()->getModuleConfiguration($this->getModule());

			if ($moduleConfiguration->hasSubModule('api')) {
				$request
					->setSubModule('api')
					->setController($matches[1] . '_' . $matches[2])
					->setAction(isset($matches[3]) ? $matches[3] : 'api')
				;

				return;
			}
		}

		parent::route($request);
	}
}