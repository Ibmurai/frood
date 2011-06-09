<?php
/**
 * Frood
 *
 * PHP version 5
 *
 * @author Jens Riisom Schultz <jers@fynskemedier.dk>
 */
/**
 * Frood
 *
 * @author Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class Frood {
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
	 * @return null
	 */
	public static function dispatch($controller = null, $action = null, $parameters = null) {
		self::_boot();

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

	private static function _boot() {
		var_dump($_SERVER['SCRIPT_FILENAME']);
		exit;
	}
}
