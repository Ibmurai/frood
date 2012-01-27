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

	/**
	 * Constructs the Exception.
	 * 
	 * @param FroodRequest $request      The Frood request.
	 * @param string       $host         The host that Frood attempted to dispatch to.
	 * @param string       $message      The Exception message to throw.
	 * @param int          $code         The Exception code.
	 * @param string       $messageExtra Any extra information to append to the message.
	 *
	 * @return null
	 */
	public function __construct(FroodRequest $request, $host = '', $message = '', $code = 0, $messageExtra = '') {
		if ($message == '') {
			$message = "Frood could not call /{$request->getModule()}/{$request->getSubModule()}/{$request->getController()}/{$request->getAction()}({$request->getParameters()})";
			if ($host != '') {
				$message .= " on the host, $host.";
			} else {
				$message .= " locally.";
			}
		}

		parent::__construct($request, $message, $code, $messageExtra);

		$this->_host = $host;
	}

	/**
	 * Get the host that Frood attempted to dispatch to.
	 *
	 * @return string The host that Frood attempted to dispatch to.
	 */
	public function getHost() {
		return $this->_host;
	}
}
