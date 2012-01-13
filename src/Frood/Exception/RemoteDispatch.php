<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodExceptionRemoteDispatch - A custom Exception for exceptions during remote dispatching.
 *
 * @category Frood
 * @package  Exception
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodExceptionRemoteDispatch extends FroodExceptionDispatch {
	/** @var string The host that Frood attempted to dispatch to. */
	protected $_host;

	/** @var string The module that Frood attempted to dispatch to. */
	protected $_module;

	/**
	 * Constructs the Exception.
	 *
	 * @param string          $host         The host that Frood attempted to dispatch to.
	 * @param string          $module       The module that Frood attempted to dispatch to.
	 * @param string          $controller   The controller that Frood attempted to dispatch to.
	 * @param string          $action       The action that Frood attempted to call on the controller.
	 * @param FroodParameters $parameters   The parameters that Frood attempted to pass to the action.
	 * @param string          $subModule    Which sub module was The Frood running?
	 * @param string          $message      The Exception message to throw.
	 * @param int             $code         The Exception code.
	 * @param string          $messageExtra Any extra information to append to the message.
	 *
	 * @return null
	 */
	public function __construct($host = '', $module = '', $controller = '', $action = '', FroodParameters $parameters = null, $subModule = '', $message = '', $code = 0, $messageExtra = '') {
		if ($message == '') {
			$message = "Frood could not call $module/$subModule/$controller/$action($parameters)";
			if ($host != '') {
				$message .= " on the host, $host.";
			} else {
				$message .= " locally.";
			}
		}

		parent::__construct($controller, $action, $parameters, $message, $code, $messageExtra);

		$this->_host   = $host;
		$this->_module = $module;
	}

	/**
	 * Get the host that Frood attempted to dispatch to.
	 *
	 * @return string The host that Frood attempted to dispatch to.
	 */
	public function getHost() {
		return $this->_host;
	}

	/**
	 * Get the module that Frood attempted to dispatch to.
	 *
	 * @return string The module that Frood attempted to dispatch to.
	 */
	public function getModule() {
		return $this->_module;
	}
}
