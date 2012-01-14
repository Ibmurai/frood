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
	/** @var string The module we're working with. */
	private $_module;

	/** @var string Which sub module are we running? */
	private $_subModule;

	/** @var FroodAutoloader The module autoloader instance. */
	private $_moduleAutoloader;

	/** @var FroodAutoloader The Frood autoloader instance. */
	private $_froodAutoloader;

	/** @var FroodModuleConfiguration The module configuration */
	private $_moduleConfiguration;

	/** @var FroodConfiguration The Frood configuration */
	private static $_froodConfiguration;

	/**
	 * Initialize The Frood.
	 *
	 * @param string             $module        The module to work with.
	 * @param string             $subModule     The sub module to work with.
	 * @param FroodConfiguration $configuration The configuration.
	 *
	 * @throws FroodException
	 */
	public function __construct($module = null, $subModule = null, FroodConfiguration $configuration = null) {
		$this->_setupFroodAutoloader();

		if ($configuration) {
			self::$_froodConfiguration = $configuration;
		}

		self::getFroodConfiguration()->getUriParser()->parse(self::getFroodConfiguration()->getRequestUri());

		$this->_module    = $module ? $module    : self::getFroodConfiguration()->getUriParser()->getModule();
		$this->_subModule = $module ? $subModule : self::getFroodConfiguration()->getUriParser()->getSubModule();
		$this->_moduleConfiguration = self::getFroodConfiguration()->getModuleConfiguration($module);

		$this->_setupModuleAutoloader();
	}

	/**
	 * Get the Frood configuration
	 *
	 * @return FroodConfiguration
	 */
	public static function getFroodConfiguration() {
		return self::$_froodConfiguration ? self::$_froodConfiguration : (self::$_froodConfiguration = new FroodConfiguration());
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
	public function dispatch($controller = null, $action = null, FroodParameters $parameters = null) {
		if ($controller === null) {
			$controller = $this->_guessController();
		} else {
			$controller = FroodUtil::convertHtmlNameToPhpName("{$this->_module}_{$this->_subModule}_controller_{$controller}");
		}

		if ($action === null) {
			$action = $this->_guessAction();
		} else {
			$action = FroodUtil::convertHtmlNameToPhpName($action, false);
		}
		$method = $action . 'Action';

		if ($parameters === null) {
			$parameters = $this->_guessParameters();
		}

		if (!class_exists($controller)) {
			throw new FroodExceptionDispatch($controller, $method, $parameters, '', 0, "Could not autoload $controller");
		}

		$controllerInstance = new $controller($this->_module, $this->_subModule, $action);
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
	 * Attempt to guess the controller to call, based on the request.
	 *
	 * @return null|string The name of a controller. Or null if it can't guess.
	 */
	private function _guessController() {
		if (!($controller = self::getFroodConfiguration()->getUriParser()->getController())) {
			return null;
		}
		return FroodUtil::convertHtmlNameToPhpName("{$this->_module}_{$this->_subModule}_controller_$controller");
	}

	/**
	 * Attempt to guess the action to call, based on the request.
	 *
	 * @return null|string The name of an action. 'index' if it can't guess. null if the URI isn't up to snuff.
	 */
	private function _guessAction() {
		if (!($action = self::getFroodConfiguration()->getUriParser()->getAction())) {
			return null;
		}
		return FroodUtil::convertHtmlNameToPhpName($action, false);
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
	private function _setupModuleAutoloader() {
		$modulePath = self::getFroodConfiguration()->getModuleBasePath($this->_module);

		$classPaths = array(
			$modulePath . $this->_moduleConfiguration->getAutoloadBasePath($this->_subModule),
			$modulePath . $this->_moduleConfiguration->getAutoloadBasePath('shared'),
		);

		$this->_moduleAutoloader = new FroodAutoloader($classPaths);
	}
}
