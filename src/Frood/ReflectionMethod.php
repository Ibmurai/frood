<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodReflectionMethod - Analyzes an action signature and docblock to enable actions with real signatures.
 *
 * @category Frood
 * @package  Reflection
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodReflectionMethod {
	/** @var ReflectionMethod The reflection of the method we're reflecting ;) */
	protected $_reflectionMethod;

	/** @var FroodController The controller instance we're reflecting an action for. */
	protected $_controllerInstance;

	/**
	 * Construct a new instance.
	 *
	 * @param string|object $instance Either a string containing the name of the class to reflect, or an object.
	 * @param string        $method   The method name to reflect.
	 *
	 * @return void
	 */
	public function __construct(FroodController $instance, $method) {
		$reflectionClass = new ReflectionClass($instance);

		$this->_reflectionMethod   = $reflectionClass->getMethod($method);
		$this->_controllerInstance = $instance;
	}

	/**
	 * Get the supplied parameters as a hashed array, with defaults if applicable.
	 *
	 * @param FroodParameters $params The parameters for the action.
	 *
	 * @return array
	 */
	private function getParameters(FroodParameters $params) {
		$docs = $this->getParameterDocs();

		$values = array();

		foreach ($docs as $key => $doc) {
			$ucName = ucfirst($key);
			if ($doc['default'] === '') {
				$values[$key] = $params->{"get$ucName"}($doc['type']);
			} else {
				$values[$key] = $params->{"get$ucName"}($doc['type'], $doc['default'] !== 'null' ? $doc['default'] : null);
			}
		}

		return $values;
	}

	/**
	 * Call the reflecte method with the given parameters.
	 * Actions with a single parameter of type FroodParameters are simply called with the instance.
	 * Actions with real signatures are called as such.
	 *
	 * @param FroodParameters $params The parameters for the action.
	 *
	 * @return void
	 *
	 * @throws RuntimeException If the docblock params do not match the method signature.
	 */
	public function call(FroodParameters $params) {
		if ($this->isOldschoolAction()) {
			return $this->_reflectionMethod->invoke($this->_controllerInstance, $params);
		} else {
			$paramsArray   = $this->getParameters($params);
			$orderedParams = array();

			if (count($this->_reflectionMethod->getParameters()) != count($paramsArray)) {
				throw new RuntimeException('Action docblock parameters do not match the method parameters.');
			}

			foreach ($this->_reflectionMethod->getParameters() as $param) {
				if (array_key_exists($param->getName(), $paramsArray)) {
					$orderedParams[] = $paramsArray[$param->getName()];
				} else {
					throw new RuntimeException('Action docblock parameters do not match the method parameters.');
				}
			}

			return $this->_reflectionMethod->invokeArgs($this->_controllerInstance, $orderedParams);
		}
	}

	/**
	 * Parse the docblock for param descriptions and defaults, and get them in a structured way.
	 *
	 * @return array
	 */
	private function getParameterDocs() {
		$matches = array();

		$count = preg_match_all(
			'/
				@param\s+
				(?<type>[^\s]+)
				\s+
				\$(?<name>[^\s]+)
				[ \t]*
				(?:\<(?<default>[^><\r\n]*?)\>)?
			/xm',
			$this->_reflectionMethod->getDocComment(),
			$matches
		);

		$params = array();

		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$params[$matches['name'][$i]] = array(
					'type'    => $matches['type'][$i],
					'name'    => $matches['name'][$i],
					'default' => $matches['default'][$i],
				);
			}
		}

		return $params;
	}

	/**
	 * Returns true if the method takes one parameter of type FroodParameters.
	 *
	 * @return boolean True if the method takes one parameter of type FroodParameters.
	 */
	public function isOldschoolAction() {
		if ($this->_reflectionMethod->getNumberOfParameters() == 1) {
			if (($params = $this->_reflectionMethod->getParameters()) && ($class = $params[0]->getClass())) {
				return $class->getName() == 'FroodParameters';
			}
		}

		return false;
	}
}
