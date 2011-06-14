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
abstract class Frood {
	/** @var string The module we're working with. */
	private static $_module = null;

	/** @var boolean Are we handling admin pages? */
	private static $_isAdmin = null;

	/** @var boolean Has The Frood been initialized yet? */
	private static $_isInitialized = false;

	/**
	 * Dispatch an action to a controller.
	 * Call with no parameters to determine everything from the request.
	 *
	 * @param string $controller The controller to call.
	 * @param string $action     The action to invoke.
	 * @param array  $parameters The parameters for the action.
	 *
	 * @return void
	 */
	public static function dispatch($controller = null, $action = null, $parameters = null) {
		self::initialize();

		if ($controller === null) {
			$controller = self::_guessController();
		}

		if ($action === null) {
			$action = self::_guessAction();
		}

		if ($parameters === null) {
			$parameters = self::_guessParameters();
		}

		if (method_exists($controller, $action)) {
			call_user_func(array($controller, $action), $parameters);
		} else {
			// TODO: What to do?
			throw new RuntimeException("Could not call $controller::$action(...)");
		}
	}

	/**
	 * Attempts to load the given class.
	 *
	 * @param string $name The name of the class to load.
	 *
	 * @return void
	 */
	public static function autoload($name) {
		$matches = array();

		if ($filePath = self::classNameToPath($name)) {
			require_once $filePath;
		}
	}

	/**
	 * Do initialization stuff unless it's already done.
	 * This is automatically called when you dispatch.
	 *
	 * @return void
	 */
	public static function initialize() {
		if (self::$_isInitialized) {
			return;
		}

		self::_setupAutoloader();
		self::_setupModuleAndIsAdmin();

		self::$_isInitialized = true;
	}

	/**
	 * Register the autoloader.
	 *
	 * @return void
	 */
	private static function _setupAutoloader() {
		if (false === spl_autoload_functions()) {
			if (function_exists('__autoload')) {
				spl_autoload_register('__autoload', false);
			}
		}

		spl_autoload_register(array('Frood', 'autoload'));
	}

	/**
	 * Determine context: Module and whether we are in admin mode.
	 *
	 * @return void
	 */
	private static function _setupModuleAndIsAdmin() {
		$matches = array();
		if (preg_match('/
			\/([a-z]*)
			\/([a-z]*)
			\/index\.php
		$/ix', $_SERVER['SCRIPT_FILENAME'], $matches)) {
			if ($matches[2] == 'admin') {
				self::$_module  = $matches[1];
				self::$_isAdmin = true;
			} else {
				self::$_module  = $matches[2];
				self::$_isAdmin = false;
			}
		}
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
	public static function classNameToPath($name) {
		// Search for classes in Frood...
		$searchLocations = array(
			dirname(__FILE__),
		);

		// ...And in the module
		if (self::$_module !== null) {
			$searchLocations[] = realpath(dirname(__FILE__) . '/../../../' . self::$_module);
		}

		if (preg_match('/^((?:[A-Z][a-z]+)+)$/', $name)) {
			// Build a regular expression matching... Well... The end of the filepaths to accept...
			$regex = '/' . substr($name, 0, 1) . preg_replace('/([A-Z])/', '\/?\\1', substr($name, 1)) . '.php$/';

			$directory = new RecursiveDirectoryIterator(dirname(__FILE__));
			$iterator = new RecursiveIteratorIterator($directory);
			foreach ($iterator as $finfo) {
				if (preg_match($regex, $finfo->getPathname())) {
					require_once $finfo->getPathname();
				}
			}
		}
	}
}
