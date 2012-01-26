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
 * @category   Frood
 * @author     Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author     Bo Thinggaard <akimsko@tnactas.dk>
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
	
	private $_requestString;
	
	public function __construct($requestString) {
		$this->_requestString = $requestString;
	}
	
	public function isComplete() {
		return (isset($this->_module) && isset($this->_subModule) && isset($this->_controller) && isset($this->_action));
	}
	
	public function matchPrefix($prefix) {
		if (strpos($request->_requestString, $prefix) === 0) {
			$this->_requestString = substr($this->_requestString, strlen($prefix));
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
}
