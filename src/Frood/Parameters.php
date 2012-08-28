<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodParameters - Controller actions may be called with an instance of this class.
 *
 * @category Frood
 * @package  Parameters
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodParameters extends FroodParameterCaster implements Iterator, Countable {
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
	 * @return null
	 */
	public function __construct(array $from = null) {
		if ($from === null) {
			$from = array_merge($_GET, $_POST);

			foreach ($_FILES as $key => $value) {
				$from[$key] = self::_parseFile($value);
			}
		}

		foreach ($from as $key => $value) {
			if ($name = FroodUtil::convertHtmlNameToPhpName($key)) {
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
	 * @throws FroodExceptionCasting For get methods with failed casting.
	 */
	public function __call($name, array $args) {
		$matches = array();
		if (preg_match('/^((?:get)|(?:has))([A-Z][A-Za-z0-9]*)$/', $name, $matches)) {
			switch ($matches[1]) {
				case 'get':
					if (count($args) == 0) {
						return $this->_getParameter($matches[2], null, FroodNullParameter::getInstance());
					} else if (count($args) == 1) {
						return $this->_getParameter($matches[2], $args[0], FroodNullParameter::getInstance());
					} else if (count($args) == 2) {
						return $this->_getParameter($matches[2], $args[0], $args[1]);
					} else {
						throw new RuntimeException("->$name should be called with 0, 1 or 2 parameters. Called with " . count($args) . ' parameters.');
					}
				case 'has':
					if (count($args) == 0) {
						return $this->_hasParameter($matches[2]);
					} else if (count($args) == 1) {
						return $this->_hasParameter($matches[2], $args[0]);
					} else {
						throw new RuntimeException("->$name should be called with 0 or 1 parameters. Called with " . count($args) . ' parameters.');
					}
			}
		} else {
			throw new RuntimeException("Call to undefined method, $name.");
		}
	}

	/**
	 * Get a nice string representation of the parameters.
	 *
	 * @return string A nice string representation of the parameters.
	 */
	public function __toString() {
		$res = array();

		foreach ($this->_values as $key => $value) {
			if ($value instanceof FroodFileParameter) {
				$res[] = "$key={$value->getPath()}";
			} else {
				$res[] = "$key=$value";
			}
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
	 * @throws RuntimeException For non-existing parameters, failed type conversions or if no default
	 *                          is given for a missing parameter. Or if no default has been given for
	 *                          a parameter with a value of the wrong type.
	 */
	private function _getParameter($name, $type, $default) {
		if ($this->_hasParameter($name)) {
			try {
				return self::_cast($type, $this->_values[$name]);
			} catch (FroodExceptionCasting $e) {
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
	 * Check if the named parameter is set. Optionally check if it is of a certain type.
	 *
	 * @param string $name The name of the parameter to check.
	 * @param string $type The type to ensure the parameter is of. One of the AS_ constants.
	 *
	 * @return boolean True if the named parameter is set.
	 */
	private function _hasParameter($name, $type = null) {
		if ($type !== null && array_key_exists($name, $this->_values)) {
			try {
				self::_cast($type, $this->_values[$name]);
			} catch (FroodExceptionCasting $e) {
				return false;
			}
		}

		return array_key_exists($name, $this->_values);
	}

	/**
	 * Parses an element of the $_FILES array.
	 *
	 * @param array $file An element from the PHP $_FILES array.
	 *
	 * @return FroodFileParameter|FroodFileParameter[]
	 */
	private static function _parseFile(array $file) {
		$result = array();

		if (count($file['name']) == 1) {
			$result[] = new FroodFileParameter(
				$file['tmp_name'],
				$file['name'],
				$file['size'],
				null, // We don't trust the submitted file type!
				$file['error']
			);
		} else {
			for ($i = 0; $i < count($file['name']); $i++) {
				$result[] = new FroodFileParameter(
					$file['tmp_name'][$i],
					$file['name'][$i],
					$file['size'][$i],
					null, // We don't trust the submitted file type!
					$file['error'][$i]
				);
			}
		}

		if (count($result) == 1) {
			return $result[0];
		} else {
			return $result;
		}
	}

	/**
	 * Add a parameter.
	 *
	 * @param string $name  The lowercased_with_underscores name of the parameter to add.
	 * @param mixed  $value The value of the parameter to add.
	 *
	 * @return FroodParameters This.
	 */
	public function addParameter($name, $value) {
		$this->_values[FroodUtil::convertHtmlNameToPhpName($name)] = $value;

		return $this;
	}

	/**
	 * Get these parameters as a string of "key=value" strings which are seperated by &'s.
	 * Used by FroodController::_redirect().
	 *
	 * @throws RuntimeException If you attempt to encode a file parameter.
	 *
	 * @return string A string of "key=value" strings which are seperated by &'s.
	 */
	public function toGetString() {
		$fields = array();
		$extras = array();
		foreach ($this as $key => $value) {
			if ($value instanceof FroodFileParameter) {
				throw new RuntimeException("You cannot GET encode file parameters ($key).");
			} else if (is_array($value)) {
				if (function_exists('http_build_str')) {
					$extras[] = http_build_str(array(FroodUtil::convertPhpNameToHtmlName($key) => $value));
				} else {
					throw new RuntimeException("You cannot GET encode multidimensional array parameters ($key) without the pecl_http extension.");
				}
			} else {
				$fields[FroodUtil::convertPhpNameToHtmlName($key)] = rawurlencode($value);
			}
		}

		$getParams = array();
		foreach ($fields as $key => $value) {
			$getParams[] = "$key=$value";
		}
		$getParams = array_merge($getParams, $extras);

		return implode('&', $getParams);
	}

	/**
	 * Implementation of the Iterator interface.
	 *
	 * @return null
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
