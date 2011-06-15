<?php

/**
 * A custom Exception for wrong action parameters.
 *
 * PHP version 5
 *
 * @category Test
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-14
 */

/**
 * A custom Exception for wrong action parameters.
 *
 * @category   Test
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodParameterException extends Exception {
	/** @var string The name of the parameter. */
	protected $_key;

	/** @var string The value of the parameter. */
	protected $_value;

	/**
	 * Constructs the Exception.
	 *
	 * @param string          $key     The name of the parameter.
	 * @param string          $value   The value of the parameter.
	 * @param string          $message The Exception message to throw.
	 * @param int             $code    The Exception code.
	 *
	 * @return void
	 */
	public function __construct($key = '', $value = '', $message = '', $code = 0) {
		if ($message == '') {
			$message = "Frood does not like the parameter, $key, with value: $value";
		}

		parent::__construct($message, $code);

		$this->_key   = $key;
		$this->_value = $value;
	}

	/**
	 * Get the name of the parameter.
	 *
	 * @return string The name of the parameter.
	 */
	public function getKey() {
		return $this->_key;
	}

	/**
	 * Get the value of the parameter.
	 *
	 * @return string The value of the parameter.
	 */
	public function getValue() {
		return $this->_value;
	}
}
