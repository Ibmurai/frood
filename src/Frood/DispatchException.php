<?php

/**
 * A custom Exception for exceptions during dispatching.
 *
 * PHP version 5
 *
 * @category Test
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-14
 */

/**
 * A custom Exception for exceptions during dispatching.
 *
 * @category   Test
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodDispatchException extends Exception {
	/** @var string The controller that Frood attempted to dispatch to. */
	protected $_controller;

	/** @var string The action that Frood attempted to call on the controller. */
	protected $_action;

	/** @var FroodParameters The parameters that Frood attempted to pass to the action. */
	protected $_parameters;

	/** @var boolean Was The Frood was in admin mode? */
	private $_isAdmin;

	/**
	 * Constructs the Exception.
	 *
	 * @param string          $controller The controller that Frood attempted to dispatch to.
	 * @param string          $action     The action that Frood attempted to call on the controller.
	 * @param FroodParameters $parameters The parameters that Frood attempted to pass to the action.
	 * @param boolean         $isAdmin    Was The Frood was in admin mode?
	 * @param string          $message    The Exception message to throw.
	 * @param int             $code       The Exception code.
	 *
	 * @return void
	 */
	public function __construct($controller = '', $action = '', FroodParameters $parameters = null, $isAdmin = false, $message = '', $code = 0) {
		if ($message == '') {
			$message = "Frood could not call $controller::$action($parameters)";
			if ($isAdmin) {
				$message .= ' [ADMIN mode]';
			} else {
				$message .= ' [PUBLIC mode]';
			}
		}

		parent::__construct($message, $code);

		$this->_controller = $controller;
		$this->_action     = $action;
		$this->_parameters = $parameters;
		$this->_isAdmin    = $isAdmin;
	}

	/**
	 * Get the controller that Frood attempted to dispatch to, as a string.
	 *
	 * @return string The controller that Frood attempted to dispatch to.
	 */
	public function getController() {
		return $this->_controller;
	}

	/**
	 * Get the action that Frood attempted to call on the controller, as a string.
	 *
	 * @return string The action that Frood attempted to call on the controller.
	 */
	public function getAction() {
		return $this->_action;
	}

	/**
	 * Get the parameters that Frood attempted to pass to the action.
	 *
	 * @return FroodParameters The parameters that Frood attempted to pass to the action.
	 */
	public function getParameters() {
		return $this->_parameters;
	}

	/**
	 * Was The Frood was in admin mode?
	 *
	 * @return boolean
	 */
	public function isAdmin() {
		return $this->_isAdmin;
	}
}
