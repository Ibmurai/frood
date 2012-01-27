<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodExceptionDispatch - A custom Exception for exceptions during dispatching.
 *
 * @category Frood
 * @package  Exception
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodExceptionDispatch extends FroodException {
	/** @var FroodRequest The Frood request. */
	protected $_request;

	/**
	 * Constructs the Exception.
	 *
	 * @param FroodRequest $request      The Frood request.
	 * @param string       $message      The Exception message to throw.
	 * @param int          $code         The Exception code.
	 * @param string       $messageExtra Any extra information to append to the message.
	 *
	 * @return null
	 */
	public function __construct(FroodRequest $request, $message = '', $code = 0, $messageExtra = '') {
		if ($message == '') {
			$action = FroodUtil::convertHtmlNameToPhpName($request->getAction(), false);
			$message = "Frood could not call {$request->getController()}::{$action}Action({$request->getParameters()})";
		}
		if ($messageExtra != '') {
			$message .= " ($messageExtra)";
		}

		parent::__construct($message, $code);

		$this->_request = $request;
	}

	/**
	 * Get the Frood request.
	 *
	 * @return FroodRequest
	 */
	public function getRequest() {
		return $this->_request;
	}
}
