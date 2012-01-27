<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRequest - Represents a Frood request.
 *
 * @category Frood
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodRequest {
	/** @var string The module name. */
	private $_module;

	/** @var string The sub module name. */
	private $_subModule;

	/** @var string The controller name. */
	private $_controller;

	/** @var string The action name. */
	private $_action;

	/** @var string The current request string. This will be modified, stripping the base route. */
	private $_requestString;

	/**
	 * Construct a new request.
	 *
	 * @param string $requestString The request string.
	 */
	public function __construct($requestString) {
		$this->_requestString = $requestString;
	}

	/**
	 * Determine if the request has been fully routed.
	 *
	 * @return boolean
	 */
	public function isComplete() {
		return (isset($this->_module) && isset($this->_subModule) && isset($this->_controller) && isset($this->_action));
	}

	/**
	 * Match the given prefix against the request string, removing the prefix if there is a match.
	 *
	 * @param string $prefix The prefix to match.
	 *
	 * @return boolean True if the prefix matched.
	 */
	public function matchPrefix($prefix) {
		if (strpos($this->_requestString, $prefix) === 0) {
			if (!$this->_requestString = substr($this->_requestString, strlen($prefix))) {
				$this->_requestString = '';
			}
			return true;
		}
		return false;
	}

	/**
	 * Get the module name.
	 *
	 * @return string The module.
	 */
	public function getModule() {
		return $this->_module;
	}

	/**
	 * Set the module name.
	 *
	 * @param string $module
	 *
	 * @return FroodRequest This.
	 */
	public function setModule($module) {
		$this->_module = $module;
		return $this;
	}

	/**
	 * Get the sub module name.
	 *
	 * @return string The sub module.
	 */
	public function getSubModule() {
		return $this->_subModule;
	}

	/**
	 * Set the sub module name.
	 *
	 * @param string $subModule
	 *
	 * @return FroodRequest This.
	 */
	public function setSubModule($subModule) {
		$this->_subModule = $subModule;
		return $this;
	}

	/**
	 * Get the controller name.
	 *
	 * @return string The controller.
	 */
	public function getController() {
		return $this->_controller;
	}

	/**
	 * Set the controller name.
	 *
	 * @param string $controller
	 *
	 * @return FroodRequest This.
	 */
	public function setController($controller) {
		$this->_controller = $controller;
		return $this;
	}

	/**
	 * Get the action name.
	 *
	 * @return string The action.
	 */
	public function getAction() {
		return $this->_action;
	}

	/**
	 * Set the action name.
	 *
	 * @param string $action
	 *
	 * @return FroodRequest This.
	 */
	public function setAction($action) {
		$this->_action = $action;
		return $this;
	}

	/**
	 * Get the request string.
	 *
	 * @return string The request string.
	 */
	public function getRequestString() {
		return $this->_requestString;
	}
}
