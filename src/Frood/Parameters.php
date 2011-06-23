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
 */
class FroodParameters implements Iterator, IteratorAggregate {
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
	 * @throws RuntimeException For non-existing methods and parameters.
	 */
	public function __call($name, array $args) {
		$matches = array();
		if (preg_match('/^((?:get)|(?:has))([A-Z][A-Za-z0-9]*)$/', $name, $matches)) {
			if ($matches[1] == 'get') {
				if (count($args) == 0) {
					return $this->_getParameter($matches[2]);
				} else if (count($args) == 1) {
					return $this->_getParameter($matches[2], $args[0]);
				} else {
					throw new RuntimeException("->$name should be called with 0 or 1 parameters. Called with " . count($args) . ' parameters.');
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
	 * @param mixed  $default A value to return if the parameter has not been set.
	 *
	 * @return mixed It's like a box of chocolates.
	 *
	 * @throws RuntimeException For non-existing parameters, if no default is given.
	 */
	private function _getParameter($name, $default = null) {
		if ($this->_hasParameter($name)) {
			return $this->_values[$name];
		} else {
			if ($default !== null) {
				return $default;
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
	 * Implementation of the IteratorAggregate interface.
	 *
	 * @return Iterator
	 */
	public function getIterator() {
		return $this;
	}
}
