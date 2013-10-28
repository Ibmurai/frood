<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodHttpResponse - Represents a HTTP response.
 *
 * @category Frood
 * @package  Http
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodHttpResponse {

	/** @var integer Http response code. */
	private $_responseCode = FroodHttpResponseCode::CODE_OK;

	/** @var string[] Response headers. */
	private $_headers = array();

	/** @var string Response message body. */
	private $_message = '';

	/**
	 * Set response code.
	 *
	 * @param integer $responseCode The http response code.
	 *
	 * @return FroodHttpResponse This.
	 */
	public function setResponseCode($responseCode) {
		$this->_responseCode = $responseCode;
		return $this;
	}

	/**
	 * Get response code.
	 *
	 * @return integer The response code.
	 */
	public function getResponseCode() {
		return $this->_responseCode;
	}

	/**
	 * Add a HTTP header.
	 *
	 * @param string $name   Header name.
	 * @param string $value Header,
	 *
	 * @return FroodHttpResponse This.
	 */
	public function addHeader($name, $value) {
		$this->_headers[$name] = $value;
		return $this;
	}

	/**
	 * Get headers.
	 *
	 * @return string[]
	 */
	public function getHeaders() {
		return $this->_headers;
	}

	/**
	 * Set message.
	 *
	 * @param string $message The reponse message body.
	 *
	 * @return FroodHttpResponse This.
	 */
	public function setMessage($message) {
		$this->_message = $message;
		return $this;
	}

	/**
	 * Get message.
	 *
	 * @return string The reponse message body.
	 */
	public function getMessage() {
		return $this->_message;
	}
}