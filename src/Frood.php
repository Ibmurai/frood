<?php
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
	private $_moduleConfig;

	/** @var FroodConfiguration The Frood configuration */
	private $_froodConfig;

	/**
	 * Do initialization stuff.
	 *
	 * @param string  $module    The dirname of the module to work with.
	 */
	public function __construct($module = null, $subModule = null) {
		$this->_setupFroodAutoloader();

		$this->_module    = $module;
		$this->_subModule = $subModule;

		$this->_froodConfig  = new FroodConfiguration();

		$moduleConfigPath = dirname(__FILE__) . '/' . $this->_froodConfig->getModulesPath() . 'Configuration.php';

		if (file_exists($moduleConfigPath)) {
			include_once($moduleConfigPath);
			$moduleConfigClassName = $this->_module . 'Configuration';
			$this->_moduleConfig   = new $moduleConfigClassName();
		}

		$this->_moduleConfig = new FroodModuleConfiguration();

		$this->_setupModuleAutoloader();
		$this->_buildUriFormat();
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
			throw new FroodExceptionDispatch($controller, $method, $parameters, $this->_subModule, '', 0, "Could not autoload $controller");
		}

		$controllerInstance = new $controller($this->_module, $this->_subModule, $action);
		if (!($controllerInstance instanceof FroodController)) {
			throw new FroodExceptionDispatch($controller, $method, $parameters, $this->_subModule, '', 0, "$controller does not extend FroodController");
		}

		if (method_exists($controllerInstance, $method)) {
			$methodReflection = new FroodReflectionMethod($controllerInstance, $method);
			$methodReflection->call($parameters);

			$controllerInstance->render();
		} else {
			throw new FroodExceptionDispatch($controller, $method, $parameters, $this->_subModule, '', 0, "$controller has no $method method");
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
	 * Attempt to guess the controller to call, based on the request.
	 *
	 * @return null|string The name of a controller. Or null if it can't guess.
	 */
	private function _guessController() {
		$requestUri = self::_getRequestUri();

		$matches = array();
		if (preg_match($this->_uriFormat, $requestUri, $matches)) {
			return FroodUtil::convertHtmlNameToPhpName("{$this->_module}_{$this->_subModule}_controller_{$matches[1]}");
		}

		return null;
	}

	/**
	 * Attempt to guess the action to call, based on the request.
	 *
	 * @return null|string The name of an action. 'index' if it can't guess. null if the URI isn't up to snuff.
	 */
	private function _guessAction() {
		$requestUri = self::_getRequestUri();

		$matches = array();
		if (preg_match($this->_uriFormat, $requestUri, $matches)) {
			$action = isset($matches[2]) ? $matches[2] : 'index';
			$action = FroodUtil::convertHtmlNameToPhpName($action, false);

			return $action;
		}

		return null;
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
			dirname(__FILE__) . '/Frood',
		);

		$this->_froodAutoloader = new FroodAutoloader($classPaths);
	}

	/**
	 * Set the module autoloader up.
	 */
	private function _setupModuleAutoloader() {
		$modulePath = dirname(__FILE__) . '/' . $this->_froodConfig->getModulesPath() . $this->_module . '/';

		$classPaths = array(
			$modulePath . $this->_moduleConfig->getAutoloadBasePath($this->_subModule),
			$modulePath . $this->_moduleConfig->getAutoloadBasePath('shared'),
		);

		$this->_moduleAutoloader = new FroodAutoloader($classPaths);
	}


	/**
	 * Builds the regex to parse the uri.
	 */
	private function _buildUriFormat() {
		$this->_uriFormat = '/^
			\/([a-z][a-z0-9_]*)        # 1 : controller
			(?:\/([a-z][a-z0-9_]*))?   # 2 : action
		/x';
	}

	/**
	 * Get the real request URI.
	 *
	 * @return string The real request URI.
	 */
	private static function _getRequestUri() {
		return $_SERVER['REQUEST_URI'];
	}
}
