<?php

/**
 * A custom Exception for exceptions during remote dispatching.
 *
 * PHP version 5
 *
 * @category Module
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-14
 */

/**
 * FroodRemoteDispatchException - A custom Exception for exceptions during remote dispatching.
 *
 * @category   Module
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRemoteDispatchException extends FroodDispatchException {
	/**
	 * Constructs the Exception.
	 *
	 * @param string          $host       The host that Frood attempted to dispatch to.
	 * @param string          $controller The controller that Frood attempted to dispatch to.
	 * @param string          $action     The action that Frood attempted to call on the controller.
	 * @param FroodParameters $parameters The parameters that Frood attempted to pass to the action.
	 * @param string          $message    The Exception message to throw.
	 * @param int             $code       The Exception code.
	 *
	 * @return void
	 */
	public function __construct($host = '', $controller = '', $action = '', FroodParameters $parameters = null, $message = '', $code = 0) {
		if ($message == '') {
			$message = "Frood could not call $controller::$action($parameters) on the host, $host. [REMOTE mode]";
		}

		parent::__construct($controller, $action, $parameters, false, $message, $code);

		$this->_host = $host;
	}

	/**
	 * Was The Frood was in admin mode?
	 *
	 * @return boolean
	 */
	public function isAdmin() {
		return false;
	}
}
