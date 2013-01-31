<?php
/**
 * This file is part of The Frood framework.
 *
 * @link      https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * RemoteDispatchErrors.
 *
 * @author Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodExceptionRemoteDispatchErrors extends FroodExceptionRemoteDispatch {
	/** @var array The errors which occured. */
	private $_errors;

	/**
	 * Constructs the Exception.
	 *
	 * @param FroodRequest $request The Frood request.
	 * @param string       $host    The host that Frood attempted to dispatch to.
	 * @param array        $errors  The errors gathered by the frood remote.
	 */
	public function __construct(FroodRequest $request, $host = '', $errors) {
		/** @var $errorStrings array */
		static $errorStrings = array(
			2 => 'E_WARNING',
			8 => 'E_NOTICE',
			256 => 'E_USER_ERROR',
			512 => 'E_USER_WARNING',
			1024 => 'E_USER_NOTICE',
			2048 => 'E_STRICT',
			4096 => 'E_RECOVERABLE_ERROR',
			8192 => 'E_DEPRECATED',
			16384 => 'E_USER_DEPRECATED',
		);

		$message = "Errors occured while Frood was calling /{$request->getModule()}/{$request->getSubModule()}/{$request->getController()}/{$request->getAction()}({$request->getParameters()})";
		if ($host != '') {
			$message .= " on the host, $host: \n\n";
		} else {
			$message .= " locally: \n\n";
		}

		$this->_errors = $errors;

		foreach ($errors as $error) {
			$message .= sprintf("%s | %s | %s | %d\n\n", $errorStrings[$error['errno']], $error['errstr'], $error['errfile'], $error['errline']);
		}

		parent::__construct($request, $host, $message);

		$this->_host = $host;
	}

	/**
	 * Get the errors which occured.
	 *
	 * @return array
	 */
	public function getErrors() {
		return $this->_errors;
	}
}
