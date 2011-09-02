<?php
/**
 * The base class for The Frood.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-09
 */

/**
 * The Frood!
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class Frood {
	/** @var string The module we're working with. */
	private $_module;

	/** @var string Which application are we running? */
	private $_app;

	/** @var FroodAutoloader The autoloader instance. */
	private $_autoloader;

	/**
	 * Do initialization stuff.
	 *
	 * @param string  $module    The dirname of the module to work with.
	 * @param string  $app       Which application are we running?
	 * @param boolean $bootXoops Boot Xoops? The answer is probably only no for tests.
	 *
	 * @return void
	 *
	 * @throws RuntimeException If Xoops cannot be booted.
	 */
	public function __construct($module = null, $app = 'public', $bootXoops = true) {
		$this->_module = $module;
		$this->_app    = $app;

		$this->_setupAutoloader();
		$this->_buildUriFormat();

		if ($bootXoops) {
			$this->_bootXoops();
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
	 * @return void
	 *
	 * @throws FroodExceptionDispatch If Frood cannot dispatch.
	 */
	public function dispatch($controller = null, $action = null, FroodParameters $parameters = null) {
		if ($controller === null) {
			$controller = $this->_guessController();
		} else {
			$controller = FroodUtil::convertHtmlNameToPhpName("{$this->_module}_{$controller}_controller");
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
			throw new FroodExceptionDispatch($controller, $method, $parameters, $this->_app, '', 0, "Could not autoload $controller");
		}

		$controllerInstance = new $controller($this->_module, $this->_app);
		if (!($controllerInstance instanceof FroodController)) {
			throw new FroodExceptionDispatch($controller, $method, $parameters, $this->_app, '', 0, "$controller does not extend FroodController");
		}

		if (method_exists($controllerInstance, $method)) {
			call_user_func(array($controllerInstance, $method), $parameters);
			$controllerInstance->render($action);
		} else {
			throw new FroodExceptionDispatch($controller, $method, $parameters, $this->_app, '', 0, "$controller has no $method method");
		}
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
			return FroodUtil::convertHtmlNameToPhpName("{$this->_module}_{$matches[1]}_controller");
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
	 * Boot Xoops or die trying!
	 *
	 * @return void
	 *
	 * @throws RuntimeException If Xoops cannot be booted.
	 */
	private function _bootXoops() {
		if ($this->_app == 'admin') {
			if (($cpHeader = realpath(dirname(__FILE__) . '/../../../../include/cp_header.php')) && file_exists($cpHeader)) {
				include_once $cpHeader;
				$vararr = get_defined_vars();
				foreach ($vararr as $varName => $varValue) {
					$GLOBALS[$varName] = $varValue;
				}
			} else {
				throw new RuntimeException("Frood could not boot Xoops! [{$this->_app} app]");
			}
		} else {
			if (($xoopsMainfile = realpath(dirname(__FILE__) . '/../../../../mainfile.php')) && file_exists($xoopsMainfile)) {
				include_once $xoopsMainfile;
				$vararr = get_defined_vars();
				foreach ($vararr as $varName => $varValue) {
					$GLOBALS[$varName] = $varValue;
				}
			} else {
				throw new RuntimeException("Frood could not boot Xoops! [{$this->_app} app]");
			}
		}
	}

	/**
	 * Set the autoloader up.
	 *
	 * @return void
	 */
	private function _setupAutoloader() {
		include_once dirname(__FILE__) . '/Frood/Autoloader.php';

		// Search for classes in Frood...
		$classPaths = array(
			dirname(__FILE__) . '/Frood',
		);

		// ...And in the [app]/class folder...
		if ($folder = realpath(dirname(__FILE__) . '/../../../' . $this->_module . '/' . $this->_app . '/class')) {
			$classPaths[] = $folder;
		}

		// ...And in the modules class folder.
		if (($this->_module !== null) && ($folder = realpath(dirname(__FILE__) . '/../../../' . $this->_module . '/class'))) {
			$classPaths[] = $folder;
		}

		$this->_autoloader = new FroodAutoloader($classPaths);
	}


	/**
	 * Builds the regex to parse the uri.
	 *
	 * @return void
	 */
	private function _buildUriFormat() {
		$this->_uriFormat = '/^
			\/modules
			\/' . $this->_module . '   #     module name
			\/' . $this->_app . '      #     the app name
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
		if (isset($_SERVER['ORIGINAL_REQUEST_URI'])) {
			return $_SERVER['ORIGINAL_REQUEST_URI'];
		} else {
			return $_SERVER['REQUEST_URI'];
		}
	}
}
