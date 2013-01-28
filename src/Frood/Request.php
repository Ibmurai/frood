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

	/** @var FroodParameters The Frood parameters. */
	private $_parameters;

	/**
	 * Construct a new request.
	 *
	 * @param string $requestString The request string.
	 */
	public function __construct($requestString = '') {
		$this->_requestString = $requestString;
	}

	/**
	 * Get the Frood parameters.
	 *
	 * @return FroodParameters
	 */
	public function getParameters() {
		return $this->_parameters ? $this->_parameters : $this->_parameters = new FroodParameters();
	}

	/**
	 * Set the Frood parameters
	 *
	 * @param FroodParameters $parameters
	 *
	 * @return FroodRequest This.
	 */
	public function setParameters($parameters) {
		$this->_parameters = $parameters;
		return $this;
	}

	/**
	 * Determine if the request has been fully routed.
	 *
	 * @return boolean
	 */
	public function isComplete() {
		return $this->_module && $this->_subModule && $this->_controller && $this->_action;
	}

	/**
	 * Match the given prefix against the request string, removing the prefix if there is a match.
	 *
	 * @param string $prefix The prefix to match.
	 *
	 * @return boolean True if the prefix matched.
	 */
	public function matchPrefix($prefix) {
		$count = 0;
		$this->_requestString = preg_replace('/^' . preg_quote($prefix, '/') . '(?:\/|$)/', '', $this->_requestString, 1, $count);

		return $count == 1 ? true : false;
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

	/**
	 * Get controller class name.
	 *
	 * @return string
	 */
	public function getControllerClassName() {
		if (Frood::getFroodConfiguration()->getModuleConfiguration($this->getModule())->useNamespaces()) {
			return '\\' . FroodUtil::convertHtmlNameToPhpName("{$this->getModule()}\\{$this->getSubModule()}\\controller\\{$this->getController()}");
		} else {
			return FroodUtil::convertHtmlNameToPhpName("{$this->getModule()}_{$this->getSubModule()}_controller_{$this->getController()}");
		}
	}

	/**
	 * Get the action method name.
	 *
	 * @return string
	 */
	public function getActionMethodName() {
		return FroodUtil::convertHtmlNameToPhpName("{$this->getAction()}_action");
	}
}
