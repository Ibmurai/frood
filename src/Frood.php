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
 * Frood - you just call dispatch!
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class Frood {
	/** @var string The module we're working with. */
	private $_module = null;

	/** @var boolean Are we handling admin pages? */
	private $_isAdmin = null;

	/**
	 * Do initialization stuff.
	 *
	 * @param string  $module  The dirname of the module to work with.
	 * @param boolean $isAdmin Are we handling admin pages?
	 *
	 * @return void
	 */
	public function __construct($module = null, $isAdmin = false) {
		$this->_setupAutoloader();
		$this->_setupModuleAndIsAdmin($module, $isAdmin);
		$this->_buildUriFormat();
	}

	/**
	 * Dispatch an action to a controller.
	 * Call with no parameters to determine everything from the request.
	 *
	 * @param string $controller The controller to call.
	 * @param string $action     The action to invoke.
	 * @param array  $parameters The parameters for the action.
	 *
	 * @return void
	 *
	 * @throws FroodDispatchException  When Frood cannot dispatch.
	 * @throws FroodParameterException When the parameters are invalid.
	 */
	public function dispatch($controller = null, $action = null, $parameters = null) {
		if ($controller === null) {
			$controller = $this->_guessController();
		}

		if ($action === null) {
			$action = $this->_guessAction();
		}

		if ($parameters === null) {
			$parameters = $this->_guessParameters();
		}

		if (method_exists($controller, $action)) {
			call_user_func(array($controller, $action), $parameters);
		} else {
			throw new FroodDispatchException($controller, $action, $parameters);
		}
	}

	/**
	 * Dispatch an action to a controller, and produce a 404 for an invalid request.
	 * The header of the 404 will contain an X-Frood-Message header with the exception string.
	 * Determines everything from the request.
	 *
	 * @return void
	 */
	public function safeDispatch() {
		try {
			$this->dispatch();
		} catch (FroodDispatchException $e) {
			header("X-Frood-Message: {$e->getMessage()}", false, 404);
		}
	}

	/**
	 * Attempts to load the given class.
	 *
	 * @param string $name The name of the class to load.
	 *
	 * @return void
	 */
	public function autoload($name) {
		if ($filePath = $this->_classNameToPath($name)) {
			require_once $filePath;
		}
	}

	/**
	 * Attempt to guess the controller to call, based on the request.
	 *
	 * @return null|string The name of a controller. Or null if it can't guess.
	 */
	private function _guessController() {
		$requestUri = $_SERVER['REQUEST_URI'];

		$matches = array();
		if (preg_match($this->_uriFormat, $requestUri, $matches)) {
			return self::convertHtmlNameToPhpName("{$this->_module}_{$matches[1]}_controller");
		}

		return null;
	}

	/**
	 * Attempt to guess the action to call, based on the request.
	 *
	 * @return null|string The name of an action. 'index' if it can't guess. null if the URI isn't up to snuff.
	 */
	private function _guessAction() {
		$requestUri = $_SERVER['REQUEST_URI'];

		$matches = array();
		if (preg_match($this->_uriFormat, $requestUri, $matches)) {
			$action = isset($matches[2]) ? $matches[2] : 'index';
			$action = self::convertHtmlNameToPhpName($action, false);

			return $action . 'Action';
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
	 * Register the autoloader.
	 *
	 * @return void
	 */
	private function _setupAutoloader() {
		if (false === spl_autoload_functions()) {
			if (function_exists('__autoload')) {
				spl_autoload_register('__autoload', false);
			}
		}

		spl_autoload_register(array($this, 'autoload'));
	}

	/**
	 * Determine context: Module and whether we are in admin mode.
	 * Call with no parameters to determine this from the request.
	 *
	 * @param string  $module  The dirname of the module to work with.
	 * @param boolean $isAdmin Are we handling admin pages?
	 *
	 * @return void
	 */
	private function _setupModuleAndIsAdmin($module = null, $isAdmin = false) {
		if ($module === null) {
			$matches = array();
			if (preg_match('/
				\/([a-z]*)
				\/([a-z]*)
				\/index\.php
			$/ix', $_SERVER['SCRIPT_FILENAME'], $matches)) {
				if ($matches[2] == 'admin') {
					$this->_module  = $matches[1];
					$this->_isAdmin = true;
				} else {
					$this->_module  = $matches[2];
					$this->_isAdmin = false;
				}
			}
		} else {
			$this->_module  = $module;
			$this->_isAdmin = $isAdmin;
		}
	}

	/**
	 * Builds the regex to parse the uri.
	 *
	 * @return void
	 */
	private function _buildUriFormat() {
		$this->_uriFormat = '/^
			\/modules
			\/' . $this->_module . '                     #     module name
			' . ($this->_isAdmin ? '\/admin' : '') . '   #     admin if in admin mode
			\/([a-z][a-z0-9_]*)                          # 1 : controller
			(?:\/([a-z][a-z0-9_]*))?                     # 2 : action
		/x';
	}

	/**
	 * Convert a class name to a path to a file containing the class
	 * definition.
	 * Used by the autoloader.
	 *
	 * @param string $name The name of the class.
	 *
	 * @return null|string A full path or null if no suitable file could be found.
	 */
	private function _classNameToPath($name) {
		// Search for classes in Frood...
		$searchLocations = array(
			dirname(__FILE__),
		);

		// ...And in the module
		if ($this->_module !== null) {
			$searchLocations[] = realpath(dirname(__FILE__) . '/../../../' . $this->_module . '/class');
		}

		if (preg_match('/^((?:[A-Z][a-z]+)+)$/', $name)) {
			// Build a regular expression matching... Well... The end of the filepaths to accept...
			$regex = '/' . substr($name, 0, 1) . preg_replace('/([A-Z])/', '\/?\\1', substr($name, 1)) . '.php$/';

			foreach ($searchLocations as $classPath) {
				$directory = new RecursiveDirectoryIterator($classPath);
				$iterator = new RecursiveIteratorIterator($directory);
				foreach ($iterator as $finfo) {
					if (preg_match($regex, $finfo->getPathname())) {
						return $finfo->getPathname();
					}
				}
			}
		}

		return null;
	}

	/**
	 * Converts a camelCased string to a lowercased_with_underscores string.
	 *
	 * @param $name The CamelCased string to convert.
	 *
	 * @return string A lowercased_with_underscores version of $name.
	 */
	public static function convertPhpNameToHtmlName($name) {
		// First lowercase the first letter.
		$name = strtolower(substr($name, 0, 1)) . substr($name, 1);

		// Second replace capital letters with _ followed by the letter, lowercased.
		return preg_replace('/([A-Z])/e', "'_'.strtolower('\\1')", $name);
	}

	/**
	 * Converts a lowercased_with_underscores string to a CamelCased string.
	 *
	 * @param $name The lowercased_with_underscores string to convert.
	 * @param $ucFirst Set this to false to get a dromedaryCased string instead.
	 *
	 * @return string A CamelCased or dromedaryCased version of $name.
	 */
	public static function convertHtmlNameToPhpName($name, $ucFirst = true) {
		// First uppercase the first letter.
		if ($ucFirst) {
			$name = strtoupper(substr($name, 0, 1)) . substr($name, 1);
		}

		// Second replace _ followed by a letter with capital letters.
		return preg_replace('/(_[a-z0-9])/e', "substr(strtoupper('\\1'),1)", $name);
	}
}
