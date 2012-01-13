<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodExceptionCasting - A custom Exception for exceptions during parameter value casting.
 *
 * @category Frood
 * @package  Exception
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodExceptionCasting extends FroodException {
	/** @var mixed The value that could not be cast. */
	protected $_value;

	/** @var string The type that Frood attempted to cast to. */
	protected $_type;

	/**
	 * Constructs the Exception.
	 *
	 * @param mixed  $value        The value that could not be cast.
	 * @param string $type         The type that Frood attempted to cast to.
	 * @param string $message      The Exception message to throw.
	 * @param int    $code         The Exception code.
	 * @param string $extraMessage An optional extra string to append to the $message.
	 *
	 * @return null
	 */
	public function __construct($value = null, $type = '', $message = '', $code = 0, $extraMessage = '') {
		if ($message == '') {
			$message = 'Parameter value, ' . var_export($value, true) . ", could not be cast as $type.";
		}
		if ($extraMessage != '') {
			$message .= " ($extraMessage)";
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
