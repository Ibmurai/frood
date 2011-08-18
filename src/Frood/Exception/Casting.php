<?php

/**
 * A custom Exception for exceptions during parameter value casting.
 *
 * PHP version 5
 *
 * @category Test
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-14
 */

/**
 * FroodExceptionCasting - A custom Exception for exceptions during parameter value casting.
 *
 * @category   Test
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodExceptionCasting extends Exception {
	/** @var mixed The value that could not be cast. */
	protected $_value;

	/** @var string The type that Frood attempted to cast to. */
	protected $_type;

	/**
	 * Constructs the Exception.
	 *
	 * @param mixed  $value   The value that could not be cast.
	 * @param string $type    The type that Frood attempted to cast to.
	 * @param string $message The Exception message to throw.
	 * @param int    $code    The Exception code.
	 *
	 * @return void
	 */
	public function __construct($value = null, $type = '', $message = '', $code = 0) {
		if ($message == '') {
			$message = 'Parameter value, ' . var_export($value, true) . ", could not be cast as $type.";
		}

		parent::__construct($message, $code);

		$this->_value = $value;
		$this->_type  = $type;
	}

	/**
	 * Get the value that could not be cast.
	 *
	 * @return mixed The value that could not be cast.
	 */
	public function getValue() {
		return $this->_value;
	}

	/**
	 * Get the type that Frood attempted to cast to.
	 *
	 * @return string The type that Frood attempted to cast to.
	 */
	public function getType() {
		return $this->_type;
	}
}
