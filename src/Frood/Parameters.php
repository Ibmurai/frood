<?php
/**
 * The parameters class for The Frood.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-09
 */

/**
 * FroodParameters - All controller actions are called with an
 * instance of this class.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 *
 * @SuppressWarnings(PHPMD.TooManyMethods) It's because of the many interfaces.
 */
class FroodParameters implements Iterator, Countable {
	/** @var string The constant to tell the get function that you want an integer. */
	const AS_INTEGER = 'integer';

	/** @var string The constant to tell the get function that you want a float. */
	const AS_FLOAT = 'float';

	/** @var string The constant to tell the get function that you want an array. */
	const AS_ARRAY = 'array';

	/** @var string The constant to tell the get function that you want a string. */
	const AS_STRING = 'string';

	/** @var string The constant to tell the get function that you want a ISO-8859-1 encoded string. */
	const AS_ISO = 'ISO-8859-1 encoded string';

	/** @var string The constant to tell the get function that you want a UTF-8 encoded string. */
	const AS_UTF8 = 'UTF-8 encoded string';

	/** @var array This associative array contains the actual parameter values. */
	private $_values = array();

	/**
	 * The constructor.
	 *
	 * The constructor generates parameters from GET and POST by default.
	 * If the same parameter(s) exist in both, POST "wins".
	 * Pass an associative array to override this behaviour.
	 *
	 * @param array $from An associative array to generate parameters from.
	 *
	 * @return void
	 */
	public function __construct(array $from = null) {
		if ($from === null) {
			$from = array_merge($_GET, $_POST);
		}

		foreach ($from as $key => $value) {
			if ($name = Frood::convertHtmlNameToPhpName($key)) {
				$this->_values[$name] = $value;
			}
		}
	}

	/**
	 * This handles all calls to ->getXxx() and ->hasXxx() methods.
	 *
	 * @param string $name The name of the method being called.
	 * @param array  $args An enumerated array containing the parameters passed to the $name'ed method.
	 *
	 * @return mixed It's like a box of chocolates.
	 *
	 * @throws RuntimeException      For non-existing methods and parameters.
	 * @throws FroodCastingException For get methods with failed casting.
	 */
	public function __call($name, array $args) {
		$matches = array();
		if (preg_match('/^((?:get)|(?:has))([A-Z][A-Za-z0-9]*)$/', $name, $matches)) {
			if ($matches[1] == 'get') {
				if (count($args) == 0) {
					return $this->_getParameter($matches[2], null, FroodNullParameter::getInstance());
				} else if (count($args) == 1) {
					return $this->_getParameter($matches[2], $args[0], FroodNullParameter::getInstance());
				} else if (count($args) == 2) {
					return $this->_getParameter($matches[2], $args[0], $args[1]);
				} else {
					throw new RuntimeException("->$name should be called with 0, 1 or 2 parameters. Called with " . count($args) . ' parameters.');
				}
			} else if ($matches[1] == 'has') {
				if (count($args) == 0) {
					return $this->_hasParameter($matches[2]);
				} else {
					throw new RuntimeException("->$name should be called with 0 parameters. Called with " . count($args) . ' parameters.');
				}
			}
		}

		throw new RuntimeException("Call to undefined method, $name.");
	}

	/**
	 * Get a nice string representation of the parameters.
	 *
	 * @return string A nice string representation of the parameters.
	 */
	public function __toString() {
		$res = array();

		foreach ($this->_values as $key => $value) {
			$res[] = "$key=$value";
		}

		return implode(', ', $res);
	}

	/**
	 * Get the named parameter.
	 *
	 * @param string $name    The name of the parameter to get.
	 * @param string $type    Ensure that the parameter value is of the given type. Use one of the AS_ class constants.
	 * @param mixed  $default A value to return if the parameter has not been set.
	 *
	 * @return mixed It's like a box of chocolates.
	 *
	 * @throws RuntimeException For non-existing parameters, failed type conversions or if no default is given for a missing parameter. Or if no default has been given for a parameter with a value of the wrong type.
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	private function _getParameter($name, $type, $default) {
		if ($this->_hasParameter($name)) {
			try {
				return self::_cast($type, $this->_values[$name]);
			} catch (FroodCastingException $e) {
				if ($default !== FroodNullParameter::getInstance()) {
					return self::_cast($type, $default);
				} else {
					return self::_cast($type, $this->_values[$name]);
				}
			}
		} else {
			if ($default !== FroodNullParameter::getInstance()) {
				return self::_cast($type, $default);
			} else {
				throw new RuntimeException("Attempting to retrieve parameter, $name, which has not been set and has no default value.");
			}
		}
	}

	/**
	 * Check if the named parameter is set.
	 *
	 * @param string $name The name of the parameter to check.
	 *
	 * @return boolean True if the named parameter is set.
	 */
	private function _hasParameter($name) {
		return array_key_exists($name, $this->_values);
	}

	/**
	 * Attempt to cast a value as the given type.
	 *
	 * @param string $type  Ensure that the parameter value is of the given type. Use one of the AS_ class constants.
	 * @param mixed  $value The value to cast.
	 *
	 * @return mixed It's like a box of chocolates.
	 *
	 * @throws FroodCastingException If the value could not be cast.
	 * @throws RuntimeException      If the type is unknown.
	 */
	private static function _cast($type, $value) {
		switch ($type) {
			case null:
				return $value;
			case self::AS_INTEGER:
				return self::_castAsInteger($value);
			case self::AS_STRING:
				return self::_castAsString($value);
			case self::AS_FLOAT:
				return self::_castAsFloat($value);
			default:
				throw new RuntimeException('Unknown type, ' . $type . '.');
				break;
		}
	}

	/**
	 * Attempt to cast a value to integer.
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @throws FroodCastingException If the value could not be cast.
	 *
	 * @return integer
	 */
	public function _castAsInteger($value) {
		if (is_int($value)) {
			return (int) $value;
		}
		if (is_string($value) && preg_match('/^\s*\-?[0-9]+\s*$/', $value)) {
			return intval($value);
		}

		throw new FroodCastingException($value, self::AS_INTEGER);
	}

	/**
	 * Attempt to cast a value to string.
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @throws FroodCastingException If the value could not be cast.
	 *
	 * @return string
	 */
	public function _castAsString($value) {
		if ($value !== null && !is_array($value) && !is_object($value)) {
			return (string) $value;
		}

		throw new FroodCastingException($value, self::AS_STRING);
	}

	/**
	 * Attempt to cast a value to float.
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @throws FroodCastingException If the value could not be cast.
	 *
	 * @return float
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function _castAsFloat($value) {
		if (is_float($value)) {
			return (float) $value;
		}
		if ($value !== null && !is_array($value) && !is_object($value) && is_string($value) && preg_match('/^\s*\-?[0-9\.,]+\s*$/', $value)) {
			return floatval(str_replace(',', '.', $value));
		}
		try {
			return (float) self::_cast(self::AS_INTEGER, $value);
		} catch (FroodCastingException $e) {
			throw new FroodCastingException($value, self::AS_FLOAT);
		}
	}

	/**
	 * Implementation of the Iterator interface.
	 *
	 * @return void
	 */
	public function rewind() {
		reset($this->_values);
	}

	/**
	 * Implementation of the Iterator interface.
	 *
	 * @return mixed
	 */
	public function current() {
		return current($this->_values);
	}

	/**
	 * Implementation of the Iterator interface.
	 *
	 * @return string
	 */
	public function key() {
		return key($this->_values);
	}

	/**
	 * Implementation of the Iterator interface.
	 *
	 * @return mixed
	 */
	public function next() {
		return next($this->_values);
	}

	/**
	 * Implementation of the Iterator interface.
	 *
	 * @return boolean
	 */
	public function valid() {
		$key = key($this->_values);
		return ($key !== NULL && $key !== FALSE);
	}

	/**
	 * Implementation of the Countable interface.
	 *
	 * @return integer
	 */
	public function count() {
		return count($this->_values);
	}
}
