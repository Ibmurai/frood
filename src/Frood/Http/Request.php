<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodHttpRequest - Represents a HTTP request.
 *
 * @category Frood
 * @package  Http
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodHttpRequest {

	/** @var string[] Http request headers. */
	private $_headers;

	/** @var string Http message body. */
	private $_message;

	/** @var string Http request method. */
	private $_method;

	/**
	 * Get headers.
	 *
	 * @return string[] Headers.
	 */
	public function getHeaders() {
		if ($this->_headers === null) {
			$this->_headers = array();
			foreach ($_SERVER as $key => $val) {
				if (preg_match('/^HTTP_/', $key)) {
					$this->_headers[$key] = $val;
				}
			}
		}
		return $this->_headers;
	}

	/**
	 * Get header.
	 *
	 * @param string $name Header name.
	 *
	 * @return null|string Header or null if not found.
	 */
	public function getHeader($name) {
		$headers = $this->getHeaders();
		return isset($headers[$name]) ? $headers[$name] : null;
	}

	/**
	 * Get message.
	 *
	 * @return string The request body.
	 */
	public function getMessage() {
		if ($this->_message === null) {
			$this->_message = @file_get_contents('php://input');
		}
		return $this->_message;
	}

	/**
	 * Get request method.
	 *
	 * @return string The request method.
	 */
	public function getMethod() {
		if ($this->_method === null) {
			$this->_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
		}
		return $this->_method;
	}


}