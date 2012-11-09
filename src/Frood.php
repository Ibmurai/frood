<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
require_once dirname(__FILE__) . '/Frood/Autoloader.php';
require_once dirname(__FILE__) . '/Frood/Configuration.php';
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
	private static $_froodAutoloader;

	/** @var FroodModuleConfiguration The module configuration. */
	private $_moduleConfiguration;

	/** @var FroodConfiguration The Frood configuration. */
	private static $_froodConfiguration;

	/** @var FroodRouterChain The Frood router chain. */
	private $_routerChain;

	/**
	 * Initialize The Frood.
	 *
	 * @param FroodConfiguration $configuration The configuration.
	 *
	 * @throws FroodException
	 */
	public function __construct(FroodConfiguration $configuration = null) {
		if ($configuration) {
			self::$_froodConfiguration = $configuration;
		}

		$this->_setupFroodAutoloader();

		$this->_routerChain = new FroodRouterChain();
	}

	/**
	 * Get the Frood configuration.
	 *
	 * @return FroodConfiguration
	 */
	public static function getFroodConfiguration() {
		return self::$_froodConfiguration ? self::$_froodConfiguration : self::$_froodConfiguration = new FroodConfiguration();
	}

	/**
	 * Route a request, modifying the request.
	 *
	 * @param FroodRequest $request The request to route.
	 */
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
		throw new FroodExceptionDispatch($request, 'No prefix found matching the request');
	}

	/**
	 * Route a request and get the routed request.
	 *
	 * @param FroodRequest $request The request to route.
	 *
	 * TODO: Add @throws
	 *
	 * @return FroodRequest A routed request.
	 */
	public function route(FroodRequest $request = null) {
		if (!$request) {
			$request = new FroodRequest(self::getFroodConfiguration()->getRequestUri());
		}

		$this->_route($request);

		return $request;
	}

	/**
	 * Dispatch an action to a controller.
	 * Call with no parameters to determine everything from the request.
	 *
	 * @param FroodRequest    $request       The request to dispatch.
	 * @param boolean         $modifyHeaders Set to false, to disable the header modification by the rendering. (Used by FroodRemote)
	 *
	 * @throws FroodExceptionDispatch If Frood cannot dispatch.
	 */
	public function dispatch(FroodRequest $request = null, $modifyHeaders = true) {
		if (!$request) {
			$request = new FroodRequest(self::getFroodConfiguration()->getRequestUri());
		}

		if (!$request->isComplete()) {
			$this->route($request);
		}
		$this->_moduleConfiguration = self::getFroodConfiguration()->getModuleConfiguration($request->getModule());
		$this->_setupModuleAutoloader($request);

		$controller = $request->getControllerClassName();
		$method     = $request->getActionMethodName();

		if (!class_exists($controller)) {
			throw new FroodExceptionDispatch($request, '', 0, "Could not autoload $controller");
		}

		$controllerInstance = new $controller($request);
		if (!($controllerInstance instanceof FroodController)) {
			throw new FroodExceptionDispatch($request, '', 0, "$controller does not extend FroodController");
		}

		if (method_exists($controllerInstance, $method)) {
			$methodReflection = new FroodReflectionMethod($controllerInstance, $method);
			$methodReflection->call($request->getParameters());

			if ($modifyHeaders && ($renderer = $controllerInstance->getRenderer()) && $renderer->getContentType()) {
				header('Content-Type: ' . $renderer->getContentType());
			}

			$controllerInstance->render();
		} else {
			throw new FroodExceptionDispatch($request, '', 0, "$controller has no $method method");
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
	 * Set the Frood autoloader up.
	 */
	private function _setupFroodAutoloader() {
		if (!self::$_froodAutoloader) {
			$classPaths = array(
				self::getFroodPath() . 'Frood/',
			);
			FroodAutoloader::setCacheDir(self::getFroodConfiguration()->getCacheDir());
			self::$_froodAutoloader = new FroodAutoloader($classPaths);
		}
	}

	/**
	 * Set the module autoloader up.
	 *
	 * @param FroodRequest $request The request to determine the module from.
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
