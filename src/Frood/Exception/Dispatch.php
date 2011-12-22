<?php

/**
 * A custom Exception for exceptions during dispatching.
 *
 * PHP version 5
 *
 * @category Module
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-14
 */

/**
 * FroodExceptionDispatch - A custom Exception for exceptions during dispatching.
 *
 * @category   Module
 * @package    Frood
 * @subpackage Exception
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodExceptionDispatch extends FroodException {
	/** @var string The controller that Frood attempted to dispatch to. */
	protected $_controller;

	/** @var string The action that Frood attempted to call on the controller. */
	protected $_action;

	/** @var FroodParameters The parameters that Frood attempted to pass to the action. */
	protected $_parameters;

	/**
	 * Constructs the Exception.
	 *
	 * @param string          $controller   The controller that Frood attempted to dispatch to.
	 * @param string          $action       The action that Frood attempted to call on the controller.
	 * @param FroodParameters $parameters   The parameters that Frood attempted to pass to the action.
	 * @param string          $message      The Exception message to throw.
	 * @param int             $code         The Exception code.
	 * @param string          $messageExtra Any extra information to append to the message.
	 *
	 * @return void
	 */
	public function __construct($controller = '', $action = '', FroodParameters $parameters = null, $message = '', $code = 0, $messageExtra = '') {
		if ($message == '') {
			$message = "Frood could not call $controller::$action($parameters)";
		}
		if ($messageExtra != '') {
			$message .= " ($messageExtra)";
		}

		parent::__construct($message, $code);

		$this->_controller = $controller;
		$this->_action     = $action;
		$this->_parameters = $parameters;
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
}
