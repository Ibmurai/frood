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
		self::_init();

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
	 * Do initialization stuff unless it's already done.
	 *
	 * @return void
	 */
	private static function _init() {
		if (self::$_isBooted) {
			return;
		}

		$matches = array();
		if (preg_match('/
			\/([a-z]*)
			\/([a-z]*)
			\/index\.php
		$/x', $_SERVER['SCRIPT_FILENAME'], $matches)) {
			if ($matches[2] == 'admin') {
				self::$_module  = $matches[1];
				self::$_isAdmin = true;
			} else {
				self::$_module  = $matches[2];
				self::$_isAdmin = false;
			}
		}

		self::$_isBooted = true;
	}
	/*
	if (false === spl_autoload_functions()) {
	if (function_exists('__autoload')) {
		spl_autoload_register('__autoload', false);
	}
}
require_once dirname(__FILE__).'/../src/Frood.php';
spl_autoload_register(array('Frood', 'autoload'));
*/
	
	
}
