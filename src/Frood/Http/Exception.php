<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * FroodHttpException.
 *
 * @category Frood
 * @package  Http
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodHttpException extends Exception {

	/**
	 * Constructor.
	 *
	 * @param string $message      The message body.
	 * @param int    $responseCode The HTTP response code.
	 */
	public function __construct($message, $responseCode) {
		parent::__construct($message, $responseCode);
	}
}