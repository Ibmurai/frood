<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
require_once dirname(__FILE__) . '/Frood/Autoloader.php';
/**
 * The Frood!
 *
 * @category Frood
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class Frood {
	/** @var FroodAutoloader The module autoloader instance. */
	private $_moduleAutoloader;

	/** @var FroodAutoloader The Frood autoloader instance. */
	private $_froodAutoloader;

	/** @var FroodModuleConfiguration The module configuration */
	private $_moduleConfiguration;

	/** @var FroodConfiguration The Frood configuration */
	private static $_froodConfiguration;
	
	/** @var FroodRouterChain The Frood router chain */
	private $_routerChain;

	/**
	 * Initialize The Frood.
	 *
	 * @param FroodConfiguration $configuration The configuration.
	 *
	 * @throws FroodException
	 */
	public function __construct(FroodConfiguration $configuration = null) {
		$this->_setupFroodAutoloader();
		if ($configuration) {
			self::$_froodConfiguration = $configuration;
		}
		$this->_routerChain = new FroodRouterChain();
	}

	/**
	 * Get the Frood configuration
	 *
	 * @return FroodConfiguration
	 */
	public static function getFroodConfiguration() {
		return self::$_froodConfiguration ? self::$_froodConfiguration : (self::$_froodConfiguration = new FroodConfiguration());
	}
	
	private function _route(FroodRequest $request) {
		$baseRoutes = self::getFroodConfiguration()->getBaseRoutes();
		uksort($baseRoutes, array('FroodUtil', 'cmplen'));
		foreach ($baseRoutes as $prefix => $modules) {
			if ($request->matchPrefix($prefix)) {
				foreach ($modules as $module) {
					$this->_routerChain->add(self::getFroodConfiguration()->getModuleConfiguration($module)->getRouter());
				}
				$this->_routerChain->route($request);
				return;
			}
		}
	}

	/**
	 * Dispatch an action to a controller.
	 * Call with no parameters to determine everything from the request.
	 *
	 * @param string          $controller The controller to call.
	 * @param string          $action     The action to invoke.
	 * @param FroodParameters $parameters The parameters for the action.
	 *
	 * @throws FroodExceptionDispatch If Frood cannot dispatch.
	 */
	public function dispatch(FroodRequest $request = null, FroodParameters $parameters = null) {
		if (!$request) {
			$request = new FroodRequest(self::getFroodConfiguration()->getRequestUri());
		}
		
		$this->_route($request);
		$this->_moduleConfiguration = self::getFroodConfiguration()->getModuleConfiguration($request->getModule());
		$this->_setupModuleAutoloader($request);
		
		$controller = FroodUtil::convertHtmlNameToPhpName("{$request->getModule()}_{$request->getSubModule()}_controller_{$request->getController()}");
		$action = FroodUtil::convertHtmlNameToPhpName($request->getAction(), false);
		$method = $action . 'Action';

		if ($parameters === null) {
			$parameters = $this->_guessParameters();
		}

		if (!class_exists($controller)) {
			throw new FroodExceptionDispatch($controller, $method, $parameters, '', 0, "Could not autoload $controller");
		}

		$controllerInstance = new $controller($request);
		if (!($controllerInstance instanceof FroodController)) {
			throw new FroodExceptionDispatch($controller, $method, $parameters, '', 0, "$controller does not extend FroodController");
		}

		if (method_exists($controllerInstance, $method)) {
			$methodReflection = new FroodReflectionMethod($controllerInstance, $method);
			$methodReflection->call($parameters);

			$controllerInstance->render();
		} else {
			throw new FroodExceptionDispatch($controller, $method, $parameters, '', 0, "$controller has no $method method");
		}
	}

	/**
	 * Unregister the autoloader.
	 *
	 * @throws RumtimeException If the autoloader could not be unregistered.
	 */
	public function unregisterAutoloader() {
		$this->_moduleAutoloader->unregister();
		$this->_moduleAutoloader = null;
	}

	/**
	 * Get the full path to Frood.php, not including Frood.php.
	 *
	 * @return string
	 */
	public static function getFroodPath() {
		return dirname(__FILE__) . '/';
	}

	/**
	 * Generate a FroodParameters instance, based on the request.
	 *
	 * @return FroodParameters Parameters for a controller action.
	 */
	private function _guessParameters() {
		return new FroodParameters();
	}

	/**
	 * Set the Frood autoloader up.
	 */
	private function _setupFroodAutoloader() {
		$classPaths = array(
			self::getFroodPath() . 'Frood/',
		);

		$this->_froodAutoloader = new FroodAutoloader($classPaths);
	}

	/**
	 * Set the module autoloader up.
	 */
	private function _setupModuleAutoloader(FroodRequest $request) {
		$modulePath = self::getFroodConfiguration()->getModuleBasePath($request->getModule());

		$classPaths = array(
			$modulePath . $this->_moduleConfiguration->getAutoloadBasePath($request->getSubModule()),
			$modulePath . $this->_moduleConfiguration->getAutoloadBasePath('shared'),
		);

		$this->_moduleAutoloader = new FroodAutoloader($classPaths);
	}
}
